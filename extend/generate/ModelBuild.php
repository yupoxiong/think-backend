<?php
/**
 *
 * @author yupoxiong<i@yupoxiong.com>
 */

declare (strict_types=1);

namespace generate;

use Exception;
use generate\exception\GenerateException;
use think\facade\Db;

class ModelBuild extends Build
{

    public function __construct($data, $config)
    {
        $this->data   = $data;
        $this->config = $config;

        $this->template = $this->config['template']['model'];

        $this->code = file_get_contents($this->template['model']);
    }

    /**
     * 创建模型相关代码
     * @throws GenerateException
     */
    public function run(): bool
    {
        // 不生成模型
        if (!$this->data['model']['create']) {
            return true;
        }

        $auto_time    = 'protected $autoWriteTimestamp = true;';
        $soft_delete1 = 'use think\model\concern\SoftDelete;';
        $soft_delete2 = 'use SoftDelete;';

        $code = $this->code;
        $code = str_replace(array('[NAME]', '[TABLE_NAME]', '[MODEL_NAME]', '[MODEL_MODULE]'), array($this->data['cn_name'], $this->data['table'], $this->data['model']['name'], $this->data['model']['module']), $code);

        // 软删除
        if ($this->data['model']['soft_delete']) {
            $code = str_replace(array('[SOFT_DELETE_USE1]', '[SOFT_DELETE_USE2]',), array($soft_delete1, $soft_delete2), $code);
        } else {
            $code = str_replace(array("\n" . '[SOFT_DELETE_USE1]' . "\n", "\n    " . '[SOFT_DELETE_USE2]'), array('', ''), $code);
        }

        // 自动时间戳
        if ($this->data['model']['timestamp']) {
            $code = str_replace('[AUTO_TIMESTAMP]', $auto_time, $code);
        } else {
            $code = str_replace('[AUTO_TIMESTAMP]' . "\n\n", '', $code);
        }

        // 关联
        $relation_code = '';
        // 获取器/修改器
        $getter_setter_code = '';
        // 自定义选择数据
        $select_data_code = '';

        foreach ($this->data['data'] as $key => $value) {

            if($value['relation_type']>0){
                $tmp_code = file_get_contents($this->config['template']['path'] . 'model/relation.stub');
                if($value['relation_type'] === 1 || $value['relation_type'] === 2){
                    // 外键
                    $relation_type = 'belongsTo';
                    $table_name    = $this->getSelectFieldFormat($value['field_name'], 1);
                    // 表中文名
                    $cn_name    = '';
                    $table_info = Db::query('SHOW TABLE STATUS LIKE ' . "'" . $table_name . "'");
                    if ($table_info) {
                        $cn_name = $table_info[0]['Comment'] ?? '';
                    }
                    $relation_name  = parse_name($table_name, 1, false);
                    $relation_class = parse_name($table_name, 1);
                    $tmp_code       = str_replace(array('[RELATION_NAME]', '[RELATION_TYPE]', '[CLASS_NAME]', '[CN_NAME]'), array($relation_name, $relation_type, $relation_class, $cn_name), $tmp_code);
                    $relation_code  .= $tmp_code;
                } else{
                    // 主键
                    $relation_type = $value['relation_type'] === 3?$relation_type = 'hasOne':'hasMany';

                    $table_tmp = explode(',', $value['relation_table']);
                    foreach ($table_tmp as $item) {
                        $table_name     = parse_name($item, 0, false);
                        $relation_name  = parse_name($table_name, 1, false);
                        $relation_class = parse_name($item, 1);

                        //表中文名
                        $cn_name    = '';
                        $table_info = Db::query('SHOW TABLE STATUS LIKE ' . "'" . $table_name . "'");
                        if ($table_info) {
                            $cn_name = $table_info[0]['Comment'] ?? '';
                        }

                        $tmp_code_item = str_replace(array('[RELATION_NAME]', '[RELATION_TYPE]', '[CLASS_NAME]', '[CN_NAME]'), array($relation_name, $relation_type, $relation_class, $cn_name), $tmp_code);
                        $relation_code .= $tmp_code_item;
                    }
                }
            }else{
                // 如果是select，同时非关联
                if ($value['form_type'] === 'select') {
                    $field_select_data = $value['field_select_data'];

                    if (empty($field_select_data)) {
                        throw new GenerateException('请完善字段[' . $value['form_name'] . ']的自定义筛选/select数据');
                    }

                    $const_name = $this->getSelectFieldFormat($value['field_name'], 3);

                    $options     = explode("\r\n", $field_select_data);
                    $option_code = '// ' . $value['form_name'] . "列表\n" . 'const ' . $const_name . "= [\n";
                    foreach ($options as $item) {
                        $option_key_value = explode('||', $item);
                        if (is_numeric($option_key_value[0])) {
                            $option_item_key = $option_key_value[0];
                        } else {
                            $option_item_key = "'$option_key_value[0]'";
                        }

                        $option_code .= ($option_item_key . "=>'$option_key_value[1]',\n");
                    }
                    $option_code .= "];\n";

                    $select_data_code .= $option_code;

                    // 处理select自定义数据的获取器
                    $field5             = $this->getSelectFieldFormat($value['field_name'], 5);
                    $field4             = $this->getSelectFieldFormat($value['field_name'], 3);
                    $tmp_code           = file_get_contents($this->config['template']['path'] . 'model/getter_setter_select.stub');
                    $tmp_code           = str_replace(array('[FIELD_NAME5]', '[FIELD_NAME4]', '[FIELD_NAME]'), array($field5, $field4, $value['field_name']), $tmp_code);
                    $getter_setter_code .= $tmp_code;
                }
            }


            if ($value['getter_setter']) {
                switch ($value['getter_setter']) {
                    case 'switch':
                        $tmp_code           = file_get_contents($this->config['template']['path'] . 'model/getter_setter_switch.stub');
                        $tmp_code           = str_replace(array('[FIELD_NAME]', '[FORM_NAME_LOWER]', '[FORM_NAME]'), array(parse_name($value['field_name'], 1), $value['field_name'], $value['form_name']), $tmp_code);
                        $getter_setter_code .= $tmp_code;
                        break;
                    case 'datetime':
                        $tmp_code           = file_get_contents($this->config['template']['path'] . 'model/getter_setter_datetime.stub');
                        $tmp_code           = str_replace(array('[FIELD_NAME]', '[FORM_NAME]'), array(parse_name($value['field_name'], 1), $value['form_name']), $tmp_code);
                        $getter_setter_code .= $tmp_code;
                        break;
                    case 'date':
                        $tmp_code           = file_get_contents($this->config['template']['path'] . 'model/getter_setter_date.stub');
                        $tmp_code           = str_replace(array('[FIELD_NAME]', '[FORM_NAME]'), array(parse_name($value['field_name'], 1), $value['form_name']), $tmp_code);
                        $getter_setter_code .= $tmp_code;
                        break;
                    default:
                        break;
                }
            }
        }

        $code = str_replace(array('[RELATION]', '[GETTER_SETTER]', '[SELECT_DATA_LIST]'), array($relation_code, $getter_setter_code, $select_data_code), $code);


        //暂时不用switch，因为基础模型已经有status的获取器
        /*//switch字段
        $switch_field = '';
        foreach ($this->data['data'] as $value) {
            if ($value['form_type'] == 'switch') {
                $switch_field .= "'" . $value['field_name'] . "',";
            }
        }
        // switch字段替换
        $code = str_replace('[SWITCH_FIELD]', $switch_field, $code);*/


        // 搜索字段
        $search_field = '';
        // 条件字段
        $where_field = '';
        //日期/时间范围查询字段
        $time_field = '';
        foreach ($this->data['data'] as $value) {
            switch ($value['index_search']) {
                case 'search':
                    $search_field .= "'" . $value['field_name'] . "',";
                    break;

                case 'select':
                    $where_field .= "'" . $value['field_name'] . "',";
                    break;
                case 'date':
                case 'datetime':
                    $time_field .= "'" . $value['field_name'] . "',";
                    break;
                default:
                    break;
            }

        }
        // 搜索字段替换
        // 替换多图/多文件获取器，修改器
        $code = str_replace(array('[SEARCH_FIELD]', '[WHERE_FIELD]', '[TIME_FIELD]'), array($search_field, $where_field, $time_field), $code);

        try {
            file_put_contents($this->config['file_dir']['model'] . $this->data['model']['name'] . '.php', $code);
        } catch (Exception $e) {
            throw new GenerateException($e->getMessage());
        }

        return true;
    }

}
