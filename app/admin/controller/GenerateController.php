<?php
/**
 * 代码自动生成控制器
 * @author yupoxiong<i@yufuping.com>
 */

namespace app\admin\controller;

use Exception;

use think\facade\Log;
use think\Request;
use generate\Generate;
use think\response\Json;

class GenerateController extends AdminBaseController
{

    //首页
    public function index()
    {
        $this->admin['title'] = 'AutoCode';
        return $this->fetch();
    }

    //自动生成页面
    public function add()
    {
        $this->admin['title'] = 'AutoCode';

        $this->assign([
            'table' => (new Generate())->getTable(),
            'menus' => (new Generate())->getMenu(10000),
        ]);

        return $this->fetch();
    }

    //获取表数据
    public function getTable()
    {
        $data = [
            'table_list'=>(new Generate())->getTable(),
        ];
        return admin_success('success',$data);
    }


    public function getMenu()
    {
        return admin_success('success', (new Generate())->getMenu(10000));
    }


    //自动生成代码接口
    public function create(Request $request)
    {
        $param = $request->param();


        $data = [
            'table'      => $param['table_name'],
            'cn_name'    => $param['cn_name'],
            'menu'       => [
                //创建菜单-1为不创建，0为顶级菜单
                'create' => $param['create_menu'],
                'menu'   => $param['create_menu_list']
            ],
            'controller' => [
                'module' => 'admin',
                'create' => $param['create_controller'] ?? 0,
                'name'   => $param['controller_name'],
                'action' => $param['controller_action_list'],
            ],
            'model'      => [
                'module'      => 'common',
                'create'      => $param['create_model'] ?? 0,
                'name'        => $param['model_name'],
                'timestamp'   => $param['auto_timestamp'] ?? 0,
                'soft_delete' => $param['soft_delete'] ?? 0,
            ],
            'validate'   => [
                'module' => 'common',
                'create' => $param['create_validate'] ?? 0,
                'name'   => $param['validate_name'],
            ],
            'view'       => [
                'create_index' => $param['create_view_index'] ?? 0,
                'index_button' => $param['index_operation_button'] ?? 1,
                'create_add'   => $param['create_view_add'] ?? 0,
                'enable'       => $param['list_enable'] ?? 0,
                'delete'       => $param['list_delete'] ?? 0,
                'create'       => $param['list_create'] ?? 0,
                'export'       => $param['list_export'] ?? 0,
                'refresh'      => $param['list_refresh'] ?? 0,
            ],
            'module'     => [
                'name_suffix' => $param['module_name_suffix'],
                'icon'        => $param['module_icon'],
            ],
        ];


        /**
         * 字段数据组装
         */
        $field_data = [];
        foreach ($param['field_name'] as $key => $value) {

            $field_data[] = [
                //字段名
                'field_name'        => $param['field_name'][$key][0],
                //字段类型
                'field_type'        => $param['field_type'][$key][0],
                //表单名称/中文名称
                'form_name'         => $param['form_name'][$key][0] ?? '',
                //是否为列表字段
                'is_list'           => $param['is_list'][$key][0] ?? 0,
                //是否为表单字段
                'is_form'           => $param['is_form'][$key][0] ?? 0,
                //表单类型
                'form_type'         => $param['form_type'][$key][0] ?? 0,
                //验证规则
                'form_validate'     => $param['form_validate'][$key] ?? 0,
                //默认值
                'field_default'     => $param['field_default'][$key][0] ?? 0,
                //获取器/修改器
                'getter_setter'     => $param['getter_setter'][$key][0] ?? 0,
                //是否参与列表排序
                'list_sort'         => $param['list_sort'][$key][0] ?? 0,
                //筛选字段
                'index_search'      => $param['index_search'][$key][0] ?? 0,
                //筛选自定义select
                'field_select_data' => $param['field_select_data'][$key][0] ?? '',
                //验证场景
                'field_scene'       => $param['field_scene'][$key] ?? 0,
                //关联
                'is_relation'       => $param['is_relation'][$key][0] ?? 0,
                //关联类型
                'relation_type'     => $param['relation_type'][$key][0] ?? 1,
                //关联表
                'relation_table'    => $param['relation_table'][$key][0] ?? '',
                //关联显示字段
                'relation_show'     => $param['relation_show'][$key][0] ?? 'name',
            ];
        }

        $data['data'] = $field_data;

        //已经组装好data,先生成表，再生成模型，验证器，控制器，视图
        $generate = new Generate($data);
        $msg      = '生成成功';
        $result   = false;
        try {
            $generate->run();
            $result = true;
        } catch (Exception $e) {
            Log::error($e);
            $msg = $e->getMessage();//.$e->getFile().$e->getLine();
        }

        return $result ? admin_success($msg) : admin_error($msg);
    }


    //自动生成form表单字段
    public function form()
    {
        $this->admin['title'] = 'AutoCode';
        return $this->fetch();
    }

    //生成表单字段html接口
    public function formField(Request $request)
    {
        $param = $request->param();
        if (empty($param['form_name']) || empty($param['field_name']) || empty($param['form_type'])) {
            return admin_error('信息不完整');
        }


        $result = false;

        try {

            if ($param['form_type'] === 'switch') {
                $param['form_type'] = 'switch_field';
            }

            $param['field_default'] = '';

            $class_name = parse_name($param['form_type'], 1);

            $class = '\\generate\\field\\' . $class_name;
            $data  = $class::create($param);

            $result = true;

            $msg = '生成表单html成功';
        } catch (Exception $exception) {

            $msg = $exception->getMessage();
        }

        return $result ? admin_success($msg, $data) : admin_error($msg);
    }


    //根据字段返回相关表单类型和验证
    public function getField(Request $request)
    {

        $param = $request->param();
        $name  = $param['name'];

        $data = (new Generate())->getAll($name);

        return admin_success('success', $data);

    }


    //获取验证select内容

    /**
     * @param Request $request
     * @return Json|void
     */
    public function getValidateSelect(Request $request)
    {

        $param = $request->param();

        $result = false;

        try {

            if ($param['form_type'] === 'switch') {
                $param['form_type'] = 'switch_field';
            }

            $class_name = parse_name($param['form_type'], 1);

            $class = '\\generate\\field\\' . $class_name;
            $data  = $class::rule();

            $result = true;

            $msg = '获取字段验证规则成功';
        } catch (Exception $exception) {

            $msg = $exception->getMessage();
        }

        return $result ? admin_success($msg, $data) : admin_error($msg);
    }


}