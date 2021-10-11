<?php
/**
 * 后台控制器生成
 * @author yupoxiong<i@yupoxiong.com>
 */

declare (strict_types=1);


namespace generate;


use Exception;
use generate\exception\GenerateException;
use generate\field\Editor;

class AdminControllerBuild extends Build
{
    protected array $actionList = [
        'index', 'add', 'info', 'edit', 'del', 'disable', 'enable', 'import', 'export'
    ];

    /**
     * AdminControllerBuild constructor.
     * @param array $data 数据
     * @param array $config 配置
     */
    public function __construct(array $data, array $config)
    {
        $this->data   = $data;
        $this->config = $config;

        $this->template = $this->config['template']['admin'];

        $this->code = file_get_contents($this->template['admin_controller']);
    }

    /**
     * 创建admin模块控制器相关代码
     * @return bool
     * @throws GenerateException
     */
    public function run(): bool
    {
        // 不生成后台控制器
        if (!$this->data['admin_controller']['create']) {
            return true;
        }

        // 生成action
        $this->createAction();

        $replace_content = [
            '[NAME]'              => $this->data['cn_name'],
            '[TABLE_NAME]'        => $this->data['table'],
            '[CONTROLLER_NAME]'   => $this->data['admin_controller']['name'],
            '[CONTROLLER_MODULE]' => $this->data['admin_controller']['module'],
            '[MODEL_NAME]'        => $this->data['model']['name'],
            '[MODEL_MODULE]'      => $this->data['model']['module'],
            '[VALIDATE_NAME]'     => $this->data['validate']['name'],
            '[VALIDATE_MODULE]'   => $this->data['validate']['module'],
            '[SERVICE_NAME]'      => $this->data['api_controller']['name'],
            '[SERVICE_MODULE]'    => $this->data['api_controller']['module'],
        ];

        foreach ($replace_content as $key => $value) {
            $this->code = str_replace($key, $value, $this->code);
        }

        $add_field_code  = '';
        $edit_field_code = '';

        //关联代码
        $relation_1 = '';
        $relation_2 = '';
        $relation_3 = '';

        //导出代码
        $export_header = '';
        $export_body   = '';
        $export_name   = '';
        $export_code   = '';
        //with代码
        $relation_with = '';
        //with列表
        $relation_with_list = '';

        // 导入代码
        $import_field     = '';
        $import_code      = '';
        $import_name_list = '';

        // 列表页关联查询
        $index_select = '';

        foreach ($this->data['data'] as $key => $value) {

            if ($value['is_form'] === 1) {
                $add_field_code_tmp  = '';
                $edit_field_code_tmp = '';
                if ($value['form_type'] === 'editor') {
                    $add_field_code_tmp  = Editor::$controllerAddCode;
                    $edit_field_code_tmp = Editor::$controllerEditCode;
                }

                $add_field_code_tmp = str_replace(
                    ['[FORM_NAME]', '[FIELD_NAME]'],
                    [$value['form_name'], $value['field_name']],
                    $add_field_code_tmp);

                $add_field_code .= $add_field_code_tmp;

                $edit_field_code_tmp = str_replace(
                    ['[FORM_NAME]', '[FIELD_NAME]'],
                    [$value['form_name'], $value['field_name']],
                    $edit_field_code_tmp);

                $edit_field_code .= $edit_field_code_tmp;

                //关联处理
                switch ($value['relation_type']) {
                    default:
                        break;
                    case 1:// 外键一对一
                    case 2:// 外键一对多

                        $table_name = $this->getSelectFieldFormat($value['field_name']);

                        $class_name = parse_name($table_name, 1);
                        $relation_1 .= 'use app\\common\\model\\' . $class_name . ";\n";

                        $code_3     = file_get_contents($this->template['relation']);
                        $list_name  = $this->getSelectFieldFormat($value['field_name'], 2);
                        $code_3     = str_replace(array('[LIST_NAME]', '[CLASS_NAME]'), array($list_name, $class_name), $code_3);
                        $relation_3 .= $code_3;
                        break;
                    case 3:
                    case 4:
                        $list_name  = $this->getSelectFieldFormat($value['field_name'], 2);
                        $const_name = $this->getSelectFieldFormat($value['field_name'], 3);
                        $assign     = "'$list_name'=>" . $this->data['table'] . '::' . $const_name . ',';
                        $relation_3 .= $assign;
                        break;
                }

                // 这里处理导入，表单字段为导入字段
                $import_field .= "'" . $value['field_name'] . "',";

                $import_name_list .= "'" . $value['form_name'] . "',";

            }

            if ($value['index_search'] === 'select') {

                if ($value['relation_type'] === 1 || $value['relation_type'] ===2) {
                    $table_name        = $this->getSelectFieldFormat($value['field_name'], 1);
                    $select_class_name = parse_name($table_name, 1);
                    $select_list_name  = $this->getSelectFieldFormat($value['field_name'], 2);
                    $code_select       = file_get_contents($this->config['template']['path'] . 'admin_controller/relation_data_list.stub');
                    $code_select       = str_replace(array('[LIST_NAME]', '[CLASS_NAME]'), array($select_list_name, $select_class_name), $code_select);

                    $index_select .= $code_select;
                } else if ($value['relation_type'] === 0) {
                    // 这里是处理select字段的选择列表

                    $list_name  = $this->getSelectFieldFormat($value['field_name'], 2);
                    $const_name = $this->getSelectFieldFormat($value['field_name'], 3);

                    $assign = "'$list_name'=>" . $this->data['table'] . '::' . $const_name . ',';

                    $index_select .= $assign;
                }
            }


            if ($value['is_list'] === 1) {

                //列表关联显示
                if ($value['relation_type'] === 1 || $value['relation_type'] === 2) {
                    $relation_with_name = $this->getSelectFieldFormat($value['field_name'], 1);
                    $relation_with_list .= empty($relation_with_list) ? $relation_with_name : ',' . $relation_with_name;

                }

                //如果有列表导出
                if ($this->data['view']['export'] === 1) {
                    $export_header .= "'" . $value['form_name'] . "',";
                    if ($value['getter_setter'] === 'switch') {
                        $export_body .= '        $record[' . "'" . $value['field_name'] . "'" . '] = $item->' . $value['field_name'] . '_text' . ";\n";
                    } else if ($value['relation_type'] === 1 || $value['relation_type'] === 2) {

                        $relation_name = $this->getSelectFieldFormat($value['field_name'], 1);
                        $export_body   .= '$record[' . "'" . $value['field_name'] . "'" . '] = $item->' . $relation_name . '->' . $value['relation_show'] . '?? ' . "'" . "'" . ";\n";
                    } else {
                        $export_body .= '$record[' . "'" . $value['field_name'] . "'" . '] = $item->' . $value['field_name'] . ";\n";
                    }
                }

            }

        }

        //启用禁用
        $enable_code = '';
        if (in_array(5, $this->data['admin_controller']['action'])) {
            $enable_tmp  = file_get_contents($this->template['action_enable']);
            $enable_tmp  = str_replace('[MODEL_NAME]', $this->data['model']['name'], $enable_tmp);
            $enable_code = $enable_tmp;
        }

        // 导出
        if ($export_header !== '') {
            $export_name = $this->data['table'];
            $code_export = file_get_contents($this->config['template']['path'] . 'controller/export.stub');
            $code_export = str_replace(array('[HEADER_LIST]', '[BODY_ITEM]', '[FILE_NAME]', '[MODEL_NAME]'), array($export_header, $export_body, $export_name, $this->data['model']['name']), $code_export);
            $export_code = $code_export;
        }

        // 导入
        if ($import_field !== '') {
            $table_name  = $this->data['table'];
            $code_import = file_get_contents($this->config['template']['path'] . 'controller/import.stub');
            $code_import = str_replace(array('[TABLE_NAME]', '[FILED_LIST]', '[FILED_NAME_LIST]'), array($table_name, $import_field, $import_name_list), $code_import);
            $import_code = $code_import;
        }

        if ($relation_3 !== '') {
            $code_2     = file_get_contents($this->config['template']['path'] . 'controller/relation_assign_1.stub');
            $code_2     = str_replace('[RELATION_LIST]', $relation_3, $code_2);
            $relation_2 = $code_2;
        }

        //如果有列表显示
        if ($relation_with_list !== '') {
            $relation_with = file_get_contents($this->config['template']['path'] . 'controller/relation_with.stub');
            $relation_with = str_replace('[WITH_LIST]', $relation_with_list, $relation_with);
        }

        //控制器添加方法特殊字段处理
        //控制器修改方法特殊字段处理
        $this->code = str_replace(
            array('[NAME]', '[CONTROLLER_NAME]', '[CONTROLLER_MODULE]', '[MODEL_NAME]', '[MODEL_MODULE]', '[VALIDATE_NAME]', '[VALIDATE_MODULE]', '[ADD_FIELD_CODE]', '[EDIT_FIELD_CODE]', '[RELATION_1]', '[RELATION_2]', '[RELATION_3]', '[EXPORT_CODE]', '[IMPORT_CODE]', '[ENABLE_CODE]', '[RELATION_WITH]', '[SEARCH_DATA_LIST]'),
            array($this->data['cn_name'], $this->data['controller']['name'], $this->data['controller']['module'], $this->data['model']['name'], $this->data['model']['module'], $this->data['validate']['name'], $this->data['validate']['module'], $add_field_code, $edit_field_code, $relation_1, $relation_2, $relation_3, $export_code, $import_code, $enable_code, $relation_with, $index_select),
            $this->code
        );

        try {
            $out_file = $this->config['file_dir']['admin_controller']
                . $this->data['admin_controller']['name']
                . 'Controller'
                . '.php';
            file_put_contents($out_file, $this->code);

        } catch (Exception $e) {
            throw new GenerateException($e->getMessage());
        }
        return true;

    }

    /**
     * 生成需要生成的action
     */
    protected function createAction(): void
    {
        foreach ($this->actionList as $action) {
            if (!in_array($action, $this->data['admin_controller']['action'], true)) {
                $upper      = strtoupper($action);
                $this->code = str_replace('[ACTION_' . $upper . ']', '', $this->code);
            }
        }

        foreach ($this->data['admin_controller']['action'] as $action) {
            $upper = strtoupper($action);
            if (false !== strpos($this->code, $upper)) {
                $tmp_code   = file_get_contents($this->template['admin']['action_' . $action]);
                $this->code = str_replace('[ACTION_' . $upper . ']', $tmp_code, $this->code);
            }
        }
    }

}