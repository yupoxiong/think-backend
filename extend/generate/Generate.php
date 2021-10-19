<?php
/**
 * 自动生成代码
 * @author yupoxiong<i@yufuping.com>
 */

namespace generate;

use app\admin\model\AdminMenu;
use Exception;
use generate\exception\GenerateException;
use generate\field\Field;
use generate\rule\Required;
use think\facade\Db;
use think\facade\Env;
use generate\traits\Tools;
use generate\traits\Tree;

class Generate
{

    use Tree;
    use Tools;

    // 配置
    protected $config = [];

    // 主数据
    protected $data = [];


    /**
     * 控制器和模型名、验证器名黑名单
     * @var array
     */
    protected array $blacklistName = [
        'Auth',
        'Index',
        'AdminUser',
        'AdminRole',
        'AdminMenu',
        'AdminLog',
        'AdminLogData',
        'Attachment',
    ];

    /**
     * 表名黑名单
     * @var array
     */
    protected array $blacklistTable = [
        'admin_user',
        'admin_role',
        'admin_menu',
        'admin_log',
        'admin_log_data',
        'migrations',
        'attachment',
        'setting',
        'setting_group',
    ];

    public function __construct($data = [], $config = null)
    {
        $root_path = app()->getRootPath();
        $app_path  = app()->getBasePath();

        $admin_controller_path = $root_path . 'extend/generate/stub/admin_controller/';
        $api_controller_path   = $root_path . 'extend/generate/stub/api_controller/';
        $model_path            = $root_path . 'extend/generate/stub/model/';
        $config_tmp            = [
            // 模版目录
            'template' => [

                'admin' => [
                    'controller'         => $admin_controller_path . 'AdminController.stub',
                    'action_index'       => $admin_controller_path . 'action_index.stub',
                    'action_add'         => $admin_controller_path . 'action_add.stub',
                    'action_info'        => $admin_controller_path . 'action_info.stub',
                    'action_edit'        => $admin_controller_path . 'action_edit.stub',
                    'action_del'         => $admin_controller_path . 'action_del.stub',
                    'action_disable'     => $admin_controller_path . 'action_disable.stub',
                    'action_enable'      => $admin_controller_path . 'action_enable.stub',
                    'action_import'      => $admin_controller_path . 'action_import.stub',
                    'action_export'      => $admin_controller_path . 'action_export.stub',
                    'relation_data_list' => $admin_controller_path . 'relation_data_list.stub',
                    'relation_assign_1'  => $admin_controller_path . 'relation_assign_1.stub',
                    'relation_with'      => $admin_controller_path . 'relation_with.stub',
                ],

                'api' => [
                    'controller'         => $api_controller_path . 'ApiController.stub',
                    'controller_index'   => $api_controller_path . 'api_index.stub',
                    'controller_add'     => $api_controller_path . 'api_add.stub',
                    'controller_info'    => $api_controller_path . 'api_info.stub',
                    'controller_edit'    => $api_controller_path . 'api_edit.stub',
                    'controller_del'     => $api_controller_path . 'api_del.stub',
                    'controller_disable' => $api_controller_path . 'api_disable.stub',
                    'controller_enable'  => $api_controller_path . 'api_enable.stub',

                    'service'         => $root_path . 'extend/generate/stub/ApiService.stub',
                    'service_index'   => $root_path . 'extend/generate/stub/api_service/api_index.stub',
                    'service_add'     => $root_path . 'extend/generate/stub/api_service/api_add.stub',
                    'service_info'    => $root_path . 'extend/generate/stub/api_service/api_info.stub',
                    'service_edit'    => $root_path . 'extend/generate/stub/api_service/api_edit.stub',
                    'service_del'     => $root_path . 'extend/generate/stub/api_service/api_del.stub',
                    'service_disable' => $root_path . 'extend/generate/stub/api_service/api_disable.stub',
                    'service_enable'  => $root_path . 'extend/generate/stub/api_service/api_enable.stub',
                ],

                'model' => [
                    'model'                  => $model_path . 'Model.stub',
                    'relation'               => $model_path . 'relation.stub',
                    'getter_setter_select'   => $model_path . 'getter_setter_select.stub',
                    'getter_setter_switch'   => $model_path . 'getter_setter_switch.stub',
                    'getter_setter_date'     => $model_path . 'getter_setter_date.stub',
                    'getter_setter_datetime' => $model_path . 'getter_setter_datetime.stub',
                ],

                'path'           => $root_path . 'extend/generate/stub/',
                'controller'     => $root_path . 'extend/generate/stub/Controller.stub',
                'api_controller' => $root_path . 'extend/generate/stub/ApiController.stub',

                'validate' => [
                    'validate' => $root_path . 'extend/generate/stub/Validate.stub',
                ],
                'view'     => [
                    'index'         => $root_path . 'extend/generate/stub/view/index.stub',
                    'index_path'    => $root_path . 'extend/generate/stub/view/index/',
                    'index_del1'    => $root_path . 'extend/generate/stub/view/index/del1.stub',
                    'index_del2'    => $root_path . 'extend/generate/stub/view/index/del2.stub',
                    'index_filter'  => $root_path . 'extend/generate/stub/view/index/filter.stub',
                    'index_export'  => $root_path . 'extend/generate/stub/view/index/export.stub',
                    'index_import'  => $root_path . 'extend/generate/stub/view/index/import.stub',
                    'index_select1' => $root_path . 'extend/generate/stub/view/index/select1.stub',
                    'index_select2' => $root_path . 'extend/generate/stub/view/index/select2.stub',
                    'add'           => $root_path . 'extend/generate/stub/view/add.stub',
                ],
            ],
            // 生成文件目录
            'file_dir' => [
                'admin_controller' => $app_path . 'admin/controller/',
                'api_controller'   => $app_path . 'api/controller/',
                'api_service'      => $app_path . 'api/service/',
                'model'            => $app_path . 'common/model/',
                'validate'         => $app_path . 'common/validate/',
                'view'             => $app_path . 'admin/view/',
            ],
        ];

        $config       = $config ?? $config_tmp;
        $this->config = $config;

        $this->data = $data;
    }

    /**
     * @return string
     * @throws GenerateException
     */
    public function run()
    {
        $this->checkName($this->data);
        $this->checkDir();

        $this->createModel();

        $this->createAdminController();

        $this->createAddView();
        $this->createIndexView();


        $this->createValidate();

        $this->createApiController();
        $this->createApiService();

        $this->createMenu();

        return '生成成功';

        // 先判断所有目录是否可写，控制器，模型，验证器，视图
        // 然后生成表，然后再生成各个代码
        // 检查目录

        // 检查名称

        // 判断是否为

    }


    /**
     * 获取所有表(除黑名单之外)
     * @return array
     */
    public function getTable()
    {
        $table_data = Db::query('SHOW TABLES');
        $table      = [];

        foreach ($table_data as $key => $value) {
            $current = current($value);
            if (!in_array($current, $this->blacklistTable, true)) {
                $table[] = $current;
            }
        }

        return $table;
    }

    /**
     * 获取后台已有菜单，以select形式返回
     * @param int $selected
     * @param int $current_id
     * @return string
     */
    public function getMenu($selected = 1, $current_id = 0)
    {
        $result = AdminMenu::where('id', '<>', $current_id)->order('sort_number', 'asc')->order('id', 'asc')->column('id,parent_id,name,sort_number', 'id');
        foreach ($result as $r) {
            $r['selected'] = (int)$r['id'] === $selected ? 'selected' : '';
        }
        $str = "<option value='\$id' \$selected >\$spacer \$name</option>";
        $this->initTree($result);
        return $this->getTree(0, $str, $selected);
    }


    // 获取所有模块
    protected function getModule()
    {
        $module = [];
        $path   = Env::get('app_path');
        $dir    = scandir($path);
        foreach ($dir as $item) {
            if ($item !== '.' && $item !== '..' && is_dir($path . $item)) {
                $module[] = $item;
            }
        }
        return count($module) > 0 ? $module : false;
    }

    /**
     * 检查目录（是否可写）
     * @return bool
     * @throws GenerateException
     */
    protected function checkDir(): bool
    {
        if (!is_dir($this->config['file_dir']['admin_controller'])) {
            $this->mkFolder($this->config['file_dir']['admin_controller']);
        }

        if (!is_dir($this->config['file_dir']['api_controller'])) {
            $this->mkFolder($this->config['file_dir']['api_controller']);
        }

        if (!is_dir($this->config['file_dir']['model'])) {
            $this->mkFolder($this->config['file_dir']['model']);
        }

        if (!is_dir($this->config['file_dir']['validate'])) {
            $this->mkFolder($this->config['file_dir']['validate']);
        }

        if (!is_dir($this->config['file_dir']['view'])) {
            $this->mkFolder($this->config['file_dir']['view']);
        }

        if (!is_writable($this->config['file_dir']['admin_controller'])) {
            throw new GenerateException('Admin控制器目录不可写');
        }
        if (!is_writable($this->config['file_dir']['api_controller'])) {
            throw new GenerateException('Api控制器目录不可写');
        }

        if (!is_writable($this->config['file_dir']['model'])) {
            throw new GenerateException('模型目录不可写');
        }

        if (!is_writable($this->config['file_dir']['validate'])) {
            throw new GenerateException('验证器目录不可写');
        }

        if (!is_writable($this->config['file_dir']['view'])) {
            throw new GenerateException('视图目录不可写');
        }

        return true;
    }

    /**
     * 检查名称是在黑名单,表是否存在
     * @param $data
     * @return bool
     * @throws GenerateException
     */
    protected function checkName($data): bool
    {
        if (in_array($data['admin_controller']['name'], $this->blacklistName, true)) {
            throw new GenerateException('控制器名非法');
        }

        if (in_array($data['admin_controller']['name'], $this->blacklistName, true)) {
            throw new GenerateException('控制器名非法');
        }

        if (in_array($data['model'], $this->blacklistName, true)) {
            throw new GenerateException('模型名非法');
        }

        if (in_array($data['validate'], $this->blacklistName, true)) {
            throw new GenerateException('验证器名非法');
        }

        if (in_array($data['table'], $this->blacklistTable, true)) {
            throw new GenerateException('表名非法');
        }

        return true;
    }


    /**
     * 创建菜单
     * @return bool
     * @throws GenerateException
     */
    protected function createMenu(): bool
    {
        return (new AdminMenuBuild($this->data, $this->config))->run();
    }

    /**
     * 创建后台控制器
     * @return bool
     * @throws GenerateException
     */
    protected function createAdminController(): bool
    {
        return (new AdminControllerBuild($this->data, $this->config))->run();
    }

    // 创建模型

    /**
     * @return bool
     * @throws GenerateException
     */
    protected function createModel(): bool
    {
        return (new ModelBuild($this->data, $this->config))->run();
    }

    /**
     * @return bool
     * @throws GenerateException
     */
    protected function createValidate(): bool
    {
        return (new ValidateBuild($this->data, $this->config))->run();
    }

    // 列表视图
    protected function createIndexView()
    {
        // 如果不需要列表视图，直接返回
        if (!$this->data['view']['create_index']) {
            return true;
        }

        // 列表数据名称
        $name_list = '';
        // 列表数据字段
        $field_list = '';
        // 搜索框显示
        $search_name = '';
        // 其他搜索html
        $search_html = '';
        $file_fields = ['file', 'image', 'video'];
        $sort_code   = '';

        // OPERATION_ICON
        // OPERATION_TEXT
        $operation_del_icon = '<i class="fas fa-trash-alt"></i>';
        $operation_del_text = '删除';

        $operation_edit_icon = '<i class="fas fa-pen"></i>';
        $operation_edit_text = '修改';

        $operation_disable_icon = '<i class="fas fa-ban"></i>';
        $operation_disable_text = '禁用';

        $operation_enable_icon = '<i class="far fa-circle"></i>';
        $operation_enable_text = '启用';


        foreach ($this->data['data'] as $key => $value) {

            // 排序处理
            if ($value['list_sort']) {
                if ($sort_code === '') {

                    $sort_code .= file_get_contents($this->config['template']['view']['index_path'] . 'sort1.stub');
                }
                $option_code = file_get_contents($this->config['template']['view']['index_path'] . 'sort_option.stub');
                $option_code = str_replace(array('[FORM_NAME]', '[FIELD_NAME]'), array($value['form_name'], $value['field_name']), $option_code);
                $sort_code   .= $option_code;
            }

            // 列表处理
            if ($value['is_list']) {
                // 名称显示
                $name_list .= str_replace('[FORM_NAME]', $value['form_name'], Field::$listNameHtml);
                // 字段内容显示
                if (in_array($value['form_type'], $file_fields)) {
                    // 图片显示
                    $field_list .= str_replace('[FIELD_NAME]', $value['field_name'], Field::$listImgHtml);
                } else if ($value['form_type'] === 'multi_image') {
                    // 多图显示
                    $field_list .= str_replace('[FIELD_NAME]', $value['field_name'], Field::$listMultiImgHtml);
                } else if ($value['form_type'] === 'multi_file') {
                    // 多文件展示
                    $field_list .= str_replace('[FIELD_NAME]', $value['field_name'], Field::$listMultiFileHtml);
                } else if ($value['form_type'] === 'switch') {
                    // status switch显示
                    if ($value['getter_setter'] === 'switch') {
                        $field_list .= str_replace('[FIELD_NAME]', $value['field_name'], Field::$listSwitchHtml);
                    }
                } else if ($value['form_type'] === 'select') {

                    if ($value['relation_type'] === 1 || $value['relation_type'] === 2) {
                        // 关联字段显示
                        $field_name = $this->getSelectFieldFormat($value['field_name'], 1) . '.' . $value['relation_show'] . '|default=' . "''";
                        $field_list .= str_replace('[FIELD_NAME]', $field_name, Field::$listFieldHtml);
                    } else if ($value['relation_type'] === 0) {
                        $field_name = $this->getSelectFieldFormat($value['field_name'], 4);
                        $field_list .= str_replace('[FIELD_NAME]', $field_name, Field::$listFieldHtml);
                    }
                } else {
                    // 普通字段显示
                    $field_list .= str_replace('[FIELD_NAME]', $value['field_name'], Field::$listFieldHtml);
                }

            }

            // 首页列表页搜索
            switch ($value['index_search']) {
                case 'search':
                    if (!empty($search_name)) {
                        $search_name .= '/' . $value['form_name'];
                    } else {
                        $search_name .= $value['form_name'];
                    }
                    break;

                case 'select':
                    if ($value['relation_type'] === 1 || $value['relation_type'] === 2) {
                        //关联字段筛选
                        $field_name  = str_replace('_id', '', $value['field_name']);
                        $search_html .= str_replace(array('[FIELD_NAME]', '[FIELD_NAME1]', '[FORM_NAME]', '[RELATION_SHOW]'), array($value['field_name'], $field_name, $value['form_name'], $value['relation_show']), Field::$listSearchRelationHtml);

                    } else if ($value['relation_type'] === 0) {
                        //自定义select
                        $field_select_data = $value['field_select_data'];
                        if (empty($field_select_data)) {
                            throw new GenerateException('请完善字段[' . $value['form_name'] . ']的自定义筛选/select数据');
                        }

                        $field_name_list = $this->getSelectFieldFormat($value['field_name'], 2);

                        $search_html .= str_replace(array('[FORM_NAME]', '[FIELD_NAME]', '[FIELD_LIST]'), array($value['form_name'], $value['field_name'], $field_name_list), Field::$listSearchSelectHtml);
                    }
                    break;

                case 'date':
                    $search_html .= str_replace(array('[FIELD_NAME]', '[FORM_NAME]'), array($value['field_name'], $value['form_name']), Field::$listSearchDate);
                    break;

                case 'datetime':
                    $search_html .= str_replace(array('[FIELD_NAME]', '[FORM_NAME]'), array($value['field_name'], $value['form_name']), Field::$listSearchDatatime);
                    break;
                default:
                    break;
            }

        }

        if ($sort_code !== '') {
            $sort_code .= file_get_contents($this->config['template']['view']['index_path'] . 'sort2.stub');;
        }


        $file = $this->config['template']['view']['index'];
        $code = file_get_contents($file);


        // 列表删除判断
        $del1 = '';
        $del2 = '';
        // 列表选择
        $select1 = '';
        $select2 = '';

        // 列表添加
        $create = '';

        // 列表刷新
        $refresh = '';

        // 如果有删除或者启用/禁用，开启选择
        if ($this->data['view']['delete'] || $this->data['view']['enable']) {
            $select1 = file_get_contents($this->config['template']['view']['index_select1']);
            $select2 = file_get_contents($this->config['template']['view']['index_select2']);
        }

        // 删除按钮处理
        if ($this->data['view']['delete']) {
            $del1 = file_get_contents($this->config['template']['view']['index_del1']);
            $del2 = file_get_contents($this->config['template']['view']['index_del2']);
            // 操作形式处理
            if ($this->data['view']['index_button'] == 1) {
                $operation_del_text = '';
            } else if ($this->data['view']['index_button'] == 2) {
                $operation_del_icon = '';
            }
            $del2 = str_replace(array('[OPERATION_DEL_ICON]', '[OPERATION_DEL_TEXT]'), array($operation_del_icon, $operation_del_text), $del2);
        }

        // 添加按钮处理
        if ($this->data['view']['create']) {
            $create = file_get_contents($this->config['template']['view']['index_path'] . 'create.stub');
        }

        // 刷新按钮处理
        if ($this->data['view']['refresh']) {
            $refresh = file_get_contents($this->config['template']['view']['index_path'] . 'refresh.stub');
        }

        $code = str_replace(array('[INDEX_DEL1]', '[INDEX_DEL2]', '[INDEX_SELECT1]', '[INDEX_SELECT2]', '[INDEX_CREATE]', '[INDEX_REFRESH]'), array($del1, $del2, $select1, $select2, $create, $refresh), $code);


        // 顶部筛选（filter）和导出。筛选功能暂时为必生成

        $filter = file_get_contents($this->config['template']['view']['index_filter']);
        $code   = str_replace('[INDEX_FILTER]', $filter, $code);

        // 导出
        $export = '';
        if ($this->data['view']['export']) {
            $export = file_get_contents($this->config['template']['view']['index_export']);
        }

        // 导入
        $import = '';
        if ($this->data['view']['import']) {
            $import = file_get_contents($this->config['template']['view']['index_import']);
        }

        // 启用/禁用
        $enable1 = '';
        $enable2 = '';
        if ($this->data['view']['enable']) {
            $enable1 = file_get_contents($this->config['template']['path'] . 'view/index/enable1.stub');
            $enable2 = file_get_contents($this->config['template']['path'] . 'view/index/enable2.stub');
            // 操作形式处理
            if ($this->data['view']['index_button'] === 1) {
                $operation_disable_text = '';
                $operation_enable_text  = '';
            } else if ($this->data['view']['index_button'] === 2) {
                $operation_disable_icon = '';
                $operation_enable_icon  = '';
            }
            $enable2 = str_replace(array('[OPERATION_DISABLE_ICON]', '[OPERATION_DISABLE_TEXT]', '[OPERATION_ENABLE_ICON]', '[OPERATION_ENABLE_TEXT]'), array($operation_disable_icon, $operation_disable_text, $operation_enable_icon, $operation_enable_text), $enable2);
        }

        if ($this->data['view']['index_button'] === 1) {
            $operation_edit_text = '';
        } else if ($this->data['view']['index_button'] === 2) {
            $operation_edit_icon = '';
        }

        $code = str_replace(
            array('[OPERATION_EDIT_ICON]', '[OPERATION_EDIT_TEXT]', '[INDEX_ENABLE1]', '[INDEX_ENABLE2]', '[INDEX_EXPORT]', '[INDEX_IMPORT]', '[NAME_LIST]', '[FIELD_LIST]', '[SEARCH_FIELD]', '[SORT_CODE]', '[SEARCH_HTML]'),
            array($operation_edit_icon, $operation_edit_text, $enable1, $enable2, $export, $import, $name_list, $field_list, $search_name, $sort_code, $search_html),
            $code);

        $msg = '';
        try {
            file_put_contents($this->config['file_dir']['view'] . $this->data['table'] . '/index.html', $code);
            $result = true;
        } catch (Exception $e) {
            $msg    = $e->getMessage();
            $result = false;
        }
        return $result ?? $msg;
    }

    // add视图页面
    protected function createAddView()
    {
        // 如果不需要列表视图，直接返回
        if (!$this->data['view']['create_add']) {
            return true;
        }

        $form_body     = '';
        $form_rules    = '';
        $form_messages = '';

        //日期控件类的字段名
        $date_field = ['date', 'datetime'];

        foreach ($this->data['data'] as $key => $value) {

            if ($value['is_form']) {
                if ($value['form_type'] === 'switch') {
                    $value['form_type'] = 'switch_field';
                } else if ($value['form_type'] === 'select') {
                    $value['relation_data'] = '';
                    // 这里是关联的
                    if ($value['relation_type'] === 1 || $value['relation_type'] === 2) {
                        $list_code              = file_get_contents($this->config['template']['path'] . 'view/add/relation_select_data.stub');
                        $list_name              = $this->getSelectFieldFormat($value['field_name'], 2);
                        $list_code              = str_replace(array('[DATA_LIST]', '[FIELD_NAME]', '[RELATION_SHOW]'), array($list_name, $value['field_name'], $value['relation_show']), $list_code);
                        $value['relation_data'] = $list_code;

                    } else if ($value['relation_type'] === 0) {
                        // 这里是非关联的
                        $list_code              = file_get_contents($this->config['template']['path'] . 'view/add/customer_select_data.stub');
                        $list_name              = $this->getSelectFieldFormat($value['field_name'], 2);
                        $list_code              = str_replace(array('[FIELD_LIST]', '[FIELD_NAME]'), array($list_name, $value['field_name']), $list_code);
                        $value['relation_data'] = $list_code;
                    }


                } else if (in_array($value['form_type'], $date_field, true)) {
                    //如果是日期控件类字段，默认值各式不符的一律修改成''
                    if (is_numeric($value['field_default'])) {
                        $value['field_default'] = '';
                    }
                }

                $class_name = parse_name($value['form_type'], 1);
                $class      = '\\generate\\field\\' . $class_name;
                $form_body  .= $class::create($value);

                if (in_array('required', $value['form_validate'], true)) {
                    $Required  = new Required;
                    $rule_html = $Required->formRule;

                    //如果是多选select，验证字段使用[]后缀
                    $multi_field = ['multi_select', 'multi_image', 'multi_file'];
                    if (in_array($value['form_type'], $multi_field)) {
                        $value['field_name'] .= '[]';
                    }

                    $form_rules .= str_replace('[FIELD_NAME]', $value['field_name'], $rule_html);

                    $msg_html      = $Required->formMsg;
                    $msg_html      = str_replace(array('[FIELD_NAME]', '[FORM_NAME]'), array($value['field_name'], $value['form_name']), $msg_html);
                    $form_messages .= $msg_html;
                }
            }
        }


        $file = $this->config['template']['view']['add'];
        $code = file_get_contents($file);
        $code = str_replace(
            array('[FORM_BODY]', '[FORM_RULES]', '[FORM_MESSAGES]'),
            array($form_body, $form_rules, $form_messages),
            $code);

        if (!is_dir($this->config['file_dir']['view'] . $this->data['table'])) {
            $this->mkFolder($this->config['file_dir']['view'] . $this->data['table']);
        }

        file_put_contents($this->config['file_dir']['view'] . $this->data['table'] . '/add.html', $code);

        return true;
    }


    // 创建API模块控制器
    protected function createApiController()
    {

        //不生成控制器
        if (!$this->data['api_controller']['create']) {
            return true;
        }

        $api = new ApiControllerBuild($this->data, $this->config);
        return $api->create();

    }

    // 创建API模块控制器
    protected function createApiService()
    {
        //不生成控制器
        if (!$this->data['api_controller']['create']) {
            return true;
        }

        $api = new ApiServiceBuild($this->data, $this->config);
        return $api->create();
    }


    /**
     * 创建目录
     * @param $path
     */
    protected function mkFolder($path): void
    {
        if (!is_readable($path)) {
            is_file($path) || mkdir($path, 0755) || is_dir($path);
        }
    }

    //获取目录下的所有类名
    public function getClassList($dir, $except = []): array
    {
        $files   = [];
        $handler = opendir($dir);
        while (($filename = readdir($handler)) !== false) {
            if ($filename !== '.' && $filename !== '..') {
                $filename = str_replace('.php', '', $filename);

                if (!in_array($filename, $except, true)) {
                    $files[] = $filename;
                }
            }
        }
        closedir($handler);

        return $files;
    }


    //获取字段列表
    public function getAll($table_name)
    {
        $parse_name = parse_name($table_name, 1);

        $data = [
            //表
            'table'      => [
                'name'    => $table_name,
                'cn_name' => ''
            ],
            //控制器
            'controller' => [
                'create'       => 1,
                'name'         => $parse_name,
                'action'       => [
                    'index'   => 1,
                    'add'     => 1,
                    'edit'    => 1,
                    'del'     => 1,
                    'enable'  => 0,
                    'disable' => 0,
                ],
                'del_relation' => [
                    //关联方法，删除模式，1判断关联，2不做操作，3关联删除
                    'user' => 1,
                ]
            ],
            //模型
            'model'      => [
                'create'         => 1,
                'name'           => $parse_name,
                'auto_timestamp' => 1,
                'soft_delete'    => 1,

            ],
            //验证器
            'validate'   => [
                'create' => 1,
                'name'   => $parse_name,
                'scene'  => [
                    'admin_add'  => 1,
                    'admin_edit' => 1,
                    'api_add'    => 1,
                    'api_edit'   => 1,
                    'index_add'  => 1,
                    'index_edit' => 1,

                ]
            ],
            //字段
            'field'      => [],
        ];

        //所有字段信息
        $field_list = Db::query('SHOW FULL COLUMNS FROM `' . $table_name . '`');
        //表信息
        $table_info = Db::query('SHOW TABLE STATUS LIKE ' . "'" . $table_name . "'");
        $table_info = $table_info[0];
        //表名
        $data['table']['name'] = $table_info['Name'];
        //表中文名
        $data['table']['cn_name'] = $table_info['Comment'];


        //定义好以下情况，
        //90%概率为列表的字段，
        //90%概率不为列表的字段，
        //90%为搜索的字段
        //90%不为搜索的字段
        //90%为表单的字段
        //90不为表单的字段
        //导出字段暂时和显示字段一样


        //显示为列表的字段名，暂时不用
        //$list_show_field = ['id','name','mobile','description','address','money','price', 'keywords','title','img','create_time','sort_number','user_id','status'];
        //列表隐藏字段，如果为text字段，大概率隐藏
        $list_hide_field = ['password', 'update_time', 'delete_time'];
        $list_hide_type  = ['tinytext', 'tinyblob', 'text', 'blob', 'longtext', 'longblob'];


        /**
         * @param $field
         * @return mixed
         * 常用类型
         * tinyint,smallint,mediumint,int,bigint,float,double,decimal
         * char,varchar,tinytext/tinyblob,text/blob,longtext/longblob
         * date,datetime,timestamp,time,year
         */


        //搜索字段，如果为varchar，char字段，大概率需要搜索
        $search_show_field = ['id', 'mobile', 'keywords', 'id_card', 'name', 'title', 'username', 'nickname', 'true_name', 'description'];
        $search_show_type  = ['char', 'varchar'];
        //搜索隐藏字段
        //$search_hide_field = ['id','name','description'];

        //导出显示字段
        //$export_show_field = ['id','create_time'];
        //导出隐藏字段，和列表隐藏字段差不多
        $export_hide_field = ['update_time', 'delete_time'];
        $export_hide_type  = ['tinytext', 'tinyblob', 'text', 'blob', 'longtext', 'longblob'];

        //表单显示字段
        //$form_show_field = ['id','create_time','update_time','delete_time'];
        //表单隐藏字段
        $form_hide_field = ['id', 'create_time', 'update_time', 'delete_time'];
        $form_hide_type  = ['double'];


        foreach ($field_list as $key => $value) {

            // 处理关联展示
            $relation_table = '';
            $relation_type  = 0;
            $relation_show  = 'name';

            // 处理主键ID没备注的情况
            $cn_name = $value['Comment'];
            if ($value['Field'] === 'id') {

                if (empty($cn_name)) {
                    $cn_name = 'ID';
                }
                // 主键关联
                $relation_table = $this->getRelationTable($table_name);
                if (!empty($relation_table)) {
                    $relation_type = 4;
                }
            } else if (strrchr($value['Field'], '_id') === '_id') {
                // 这里判断是否为关联外键
                $union_table = $this->getRelation($value['Field']);
                if ($union_table) {
                    $relation_type = 2;
                    $relation_show = $union_table;

                }
            }

            // 筛选搜索
            $index_search = '0';
            if ($value['Field'] === 'name' || $value['Field'] === 'title' || $value['Field'] === 'description') {
                $index_search = 'search';
            }

            $field_data = [
                // 字段名
                'name'              => $value['Field'],
                // 字段中文名
                'cn_name'           => $cn_name,
                // 字段类型
                'field_type'        => $value['Type'],
                // 字段长度
                'field_length'      => 1,
                // 默认值
                'default'           => $value['Default'],
                // 是否列表显示
                'is_list'           => 1,
                // 是否为表单字段
                'is_form'           => 1,
                // 表单类型
                'form_type'         => 'text',
                // 表单验证
                'validate'          => ['required'],
                'validate_html'     => '',
                // 验证场景
                'validate_scene'    => ['admin_add'],
                // 获取器/修改器
                'getter_setter'     => false,
                // 首页筛选
                'index_search'      => $index_search,
                'field_select_data' => '',
                // 关联显示
                'relation_type'     => $relation_type,
                //  关联表
                'relation_table'    => $relation_table,
                // 关联显示字段
                'relation_show'     => $relation_show,
            ];


            $field_info = $this->getFieldInfo($field_data['name'], $field_data['field_type']);

            $field_data['field_length'] = $field_info['length'];

            // 处理是否为列表显示
            if (in_array($field_info['name'], $list_hide_field, true) || in_array($field_info['type'], $list_hide_type, true)) {
                $field_data['is_list'] = 0;
            }

            // 处理是否为列表搜索
            if (in_array($field_info['name'], $search_show_field, true) && in_array($field_info['type'], $search_show_type, true)) {
                $field_data['is_search'] = 1;
            }

            // 处理是否不为表单字段
            if (in_array($field_info['name'], $form_hide_field, true) || in_array($field_info['type'], $form_hide_type, true)) {
                $field_data['is_form'] = 0;
            }

            // 处理字段表单类型
            $form_info                   = $this->getFormInfo($field_info);
            $field_data['form_type']     = $form_info['form_type'];
            $field_data['getter_setter'] = $form_info['getter_setter'];


            // 验证
            //$field_data['validate'] = [];
            $field_data['validate_html'] = $this->getValidateHtml($field_data);


            $data['field'][] = $field_data;

        }


        return $data;
    }

    /**
     * 获取主键一对多的关联表
     * @param $table_name
     * @return string
     */
    public function getRelationTable($table_name): string
    {

        $relation_table = '';
        $fk             = $table_name . '_id';
        $table_list     = $this->getTable();
        foreach ($table_list as $table) {
            //所有字段信息
            $field_list = Db::query('SHOW FULL COLUMNS FROM `' . $table . '`');
            foreach ($field_list as $field) {
                if ($field['Field'] === $fk) {
                    $relation_table .= empty($relation_table) ? $table : ',' . $table;
                    break;
                }
            }
        }

        return $relation_table;
    }

    public function getRelation($field): string
    {
        $result     = '';
        $table_name = str_replace('_id', '', $field);

        $table_info = Db::query('SHOW TABLE STATUS LIKE ' . "'" . $table_name . "'");
        if ($table_info) {

            $fields     = [];
            $field_list = Db::query('SHOW FULL COLUMNS FROM `' . $table_name . '`');

            foreach ($field_list as $item) {
                $fields[] = $item['Field'];
            }

            if (in_array('name', $fields, true)) {
                $result = 'name';
            } elseif (in_array('title', $fields, true)) {
                $result = 'title';
            } else {
                foreach ($fields as $item) {
                    if (strpos($item, 'name') !== false) {
                        $result = $item;
                        break;
                    }
                    if (strpos($item, 'title') !== false) {
                        $result = $item;
                        break;
                    }
                }
            }


        }
        return $result;
    }


    // 根据表单类型和长度返回相应的验证
    public function getValidateHtml($field_data)
    {
        $html = '';


        try {

            if ($field_data['form_type'] === 'switch') {
                $field_data['form_type'] = 'switch_field';
            }

            $class_name = parse_name($field_data['form_type'], 1);

            $class = '\\generate\\field\\' . $class_name;
            $html  = $class::rule($field_data['field_length']);

        } catch (Exception $exception) {
            return $exception->getMessage();
        }

        return $html;
    }

    /**
     * 获取字段的表单信息
     * @param $field_info
     * @return array
     */
    public function getFormInfo($field_info): array
    {

        // 结尾：_id为select，_time为datetime，_date为date，
        // 结尾：_count为number，_lng/_longitude为map，img为image，slide为multiImage
        // 开头：is_为switch
        // 字段名：lng/longitude为map,password为password，money,price为number
        // 字段名：status大概率为switch或者其他
        $field_data = [
            'form_type'     => 'text',
            'getter_setter' => false,
        ];

        // id
        if ($field_info['name'] === 'id') {
            $field_data['form_type'] = 'number';
        }

        // _id为下拉列表，大多数为关联
        if (strrchr($field_info['name'], '_id') === '_id') {
            $field_data['form_type'] = 'select';
        }

        // 日期时间
        if (strrchr($field_info['name'], '_time') === '_time') {
            $field_data['form_type'] = 'datetime';

            $ignore_field = ['create_time', 'update_time', 'delete_time'];
            if ($field_info['type'] === 'int' && !in_array($field_info['name'], $ignore_field, true)) {
                $field_data['getter_setter'] = 'datetime';
            }
        }

        // 日期
        if ($field_info['type'] === 'datetime') {
            $field_data['form_type'] = 'date';
        }

        // 日期
        if (strrchr($field_info['name'], '_date') === '_date') {
            $field_data['form_type'] = 'date';
            if ($field_info['type'] === 'int') {
                $field_data['getter_setter'] = 'date';
            }
        }
        // 日期
        if ($field_info['type'] === 'date') {
            $field_data['form_type'] = 'date';
        }

        // 数量
        if (strrchr($field_info['name'], '_count') === '_count') {
            $field_data['form_type'] = 'number';
        }

        // 数量
        if (strrchr($field_info['name'], '_number') === '_number') {
            $field_data['form_type'] = 'number';
        }

        // 经纬度
        if (strrchr($field_info['name'], '_lng') === '_lng') {
            $field_data['form_type'] = 'map';
        }

        // 图片
        if (strrchr($field_info['name'], 'img') === 'img') {
            $field_data['form_type'] = 'image';
        }

        // 视频
        if (strrchr($field_info['name'], 'video') === 'video') {
            $field_data['form_type'] = 'video';
        }

        // 轮播
        if (strrchr($field_info['name'], 'slide') === 'slide') {
            $field_data['form_type'] = 'multi_image';
        }

        // 密码
        if (strrchr($field_info['name'], 'password') === 'password') {
            $field_data['form_type'] = 'password';
        }

        // 颜色
        if (strrchr($field_info['name'], 'color') === 'color') {
            $field_data['form_type'] = 'color';
        }
        // 图标
        if (strrchr($field_info['name'], 'icon') === 'icon') {
            $field_data['form_type'] = 'icon';
        }

        // 价格，暂时用number
        if (strrchr($field_info['name'], 'price') === 'price') {
            $field_data['form_type'] = 'number';
        }

        // 金额，暂时用number
        if (strrchr($field_info['name'], 'money') === 'money') {
            $field_data['form_type'] = 'number';
        }

        // 状态
        if ($field_info['name'] === 'status') {
            $field_data['form_type']     = 'switch';
            $field_data['getter_setter'] = 'switch';
        }

        // 手机号
        if ($field_info['name'] === 'mobile' || $field_info['name'] === 'phone') {
            $field_data['form_type'] = 'mobile';
        }

        // 头像
        if ($field_info['name'] === 'avatar') {
            $field_data['form_type'] = 'image';
        }

        // 富文本
        if ($field_info['type'] === 'text' || $field_info['type'] === 'longtext') {
            $field_data['form_type'] = 'editor';
        }

        if (strpos($field_info['name'], 'is_') === 0 && $field_info['type'] === 'tinyint') {

            $field_data['form_type']     = 'switch';
            $field_data['getter_setter'] = 'switch';
        }

        return $field_data;
    }

    /**
     * @param $field_name
     * @param $type 1返回去掉_id的字段名，如果没有_id的话就返回原字段；
     * 2返回list，例如type字段的type_list，channel_id的channel_list;
     * 3为常量LIST，例如TYPE_LIST，CHANNEL_LIST；
     * 4为显示字段name,例如type_name，channel_name；
     * 这里要注意，如果原字段是_id结尾的，会干掉_id，例如channel_id_list不仅长，而且容易产生歧义，
     * 实际channel_list的话就非常明确，这是渠道列表,是一个二维数组。
     */
    protected function getSelectFieldFormat($field_name, $type = 1)
    {
        $_id_suffix   = '_id';
        $_list_suffix = '_list';
        $_name_suffix = '_name';

        switch ($type) {

            case 1:
            default:
                $result   = $field_name;
                $_id_post = strpos($field_name, $_id_suffix);
                if (strlen($field_name) === ($_id_post + 3)) {
                    $result = substr($result, 0, $_id_post);
                }
                break;
            case 2:

                $result   = $field_name;
                $_id_post = strpos($field_name, $_id_suffix);
                if (strlen($field_name) === ($_id_post + 3)) {
                    $result = substr($result, 0, $_id_post);
                }
                $result .= $_list_suffix;
                break;

            case 3:
                $result   = $field_name;
                $_id_post = strpos($field_name, $_id_suffix);
                if (strlen($field_name) === ($_id_post + 3)) {
                    $result = substr($result, 0, $_id_post);
                }
                $result = strtoupper($result . $_list_suffix);
                break;

            case 4:
                $result   = $field_name;
                $_id_post = strpos($field_name, $_id_suffix);
                if (strlen($field_name) === ($_id_post + 3)) {
                    $result = substr($result, 0, $_id_post);

                }
                $result .= $_name_suffix;
                break;

            case 5:
                $result   = $field_name;
                $_id_post = strpos($field_name, $_id_suffix);
                if (strlen($field_name) === ($_id_post + 3)) {
                    $result = substr($result, 0, $_id_post);
                }
                $result .= $_name_suffix;
                $result = parse_name($result, 1, true);
                break;
        }

        return $result;
    }
}