<?php
/**
 * 设置分组控制器
 */

namespace app\admin\controller;

use Exception;
use app\admin\model\AdminMenu;
use think\Request;
use app\common\model\SettingGroup;

use app\common\validate\SettingGroupValidate;
use think\response\Json;

class SettingGroupController extends AdminBaseController
{
    protected $codeBlacklist = [
        'app', 'cache', 'database', 'console', 'cookie', 'log', 'middleware', 'session', 'template', 'trace',
        'ueditor', 'api', 'attachment', 'geetest', 'generate', 'admin', 'paginate',
    ];

    /**
     * @param Request $request
     * @param SettingGroup $model
     * @return string
     * @throws Exception
     */
    public function index(Request $request, SettingGroup $model): string
    {
        $param = $request->param();
        $data  = $model->scope('where', $param)
            ->paginate([
                'list_rows' => $this->admin['admin_list_rows'],
                'var_page'  => 'page',
                'query'     => $request->get()
            ]);

        // 关键词，排序等赋值
        $this->assign($request->get());

        $this->assign([
            'data'  => $data,
            'page'  => $data->render(),
            'total' => $data->total(),
        ]);
        return $this->fetch();
    }

    /**
     * @param Request $request
     * @param SettingGroup $model
     * @param SettingGroupValidate $validate
     * @return string|Json
     * @throws Exception
     */
    public function add(Request $request, SettingGroup $model, SettingGroupValidate $validate)
    {
        if ($request->isPost()) {
            $param           = $request->param();
            $validate_result = $validate->scene('add')->check($param);
            if (!$validate_result) {
                return admin_error($validate->getError());
            }

            if (in_array($param['code'], $this->codeBlacklist)) {
                return admin_error('代码 ' . $param['code'] . ' 在黑名单内，禁止使用');
            }
            /** @var SettingGroup $result */
            $result = $model::create($param);

            $url = URL_BACK;
            if (isset($param['_create']) && $param['_create'] == 1) {
                $url = URL_RELOAD;
            }

            /** @var SettingGroup $data */
            $data = $model->find($result->id);
            create_setting_menu($data);
            create_setting_file($data);

            return $result ? admin_success('添加成功', $url) : admin_error();
        }

        $this->assign([
            'module_list' => $this->getModuleList(),
        ]);

        return $this->fetch();
    }

    //修改
    public function edit($id, Request $request, SettingGroup $model, SettingGroupValidate $validate)
    {
        /** @var SettingGroup $data */
        $data = $model->find($id);
        if ($request->isPost()) {
            $param           = $request->param();
            $validate_result = $validate->scene('edit')->check($param);
            if (!$validate_result) {
                return admin_error($validate->getError());
            }

            $result = $data->save($param);

            create_setting_menu($data);
            create_setting_file($data);

            return $result ? admin_success() : admin_error();
        }

        $this->assign([
            'data'        => $data,
            'module_list' => $this->getModuleList(),

        ]);
        return $this->fetch('add');

    }

    /**
     * @param $id
     * @param SettingGroup $model
     * @return Json
     * @throws Exception
     */
    public function del($id, SettingGroup $model)
    {
        if (count($model->noDeletionId) > 0) {
            if (is_array($id)) {
                if (array_intersect($model->noDeletionId, $id)) {
                    return admin_error('ID为' . implode(',', $model->noDeletionId) . '的数据无法删除');
                }
            } else if (in_array($id, $model->noDeletionId)) {
                return admin_error('ID为' . $id . '的数据无法删除');
            }
        }

        //删除限制
        $relation_name    = 'setting';
        $relation_cn_name = '设置';
        $tips             = '下有' . $relation_cn_name . '数据，请删除' . $relation_cn_name . '数据后再进行删除操作';
        if (is_array($id)) {
            foreach ($id as $item) {
                /** @var SettingGroup $data */
                $data = $model->find($item);
                if ($data->$relation_name->count() > 0) {
                    return admin_error($data->name . $tips);
                }
            }
        } else {
            /** @var SettingGroup $data */
            $data = $model->find($id);
            if ($data->$relation_name->count() > 0) {
                return admin_error($data->name . $tips);
            }
        }

        if ($model->softDelete) {
            $result = $model->whereIn('id', $id)->useSoftDelete('delete_time', time())->delete();
        } else {
            $result = $model->whereIn('id', $id)->delete();
        }

        return $result ? admin_success('操作成功', URL_RELOAD) : admin_error();
    }


    /**
     * 生成配置文件，配置文件名为模块名
     * @param $id
     * @param SettingGroup $model
     * @return Json
     * @throws Exception
     */
    public function file($id, SettingGroup $model)
    {
        /** @var SettingGroup $data */
        $data = $model->find($id);

        $file = $data->code . '.php';
        if ($data->module !== 'app') {
            $file = $data->module . '/' . $data->code . '.php';
        }

        $path    = app()->getConfigPath() . $file;
        $warning = cache('create_setting_file_' . $data->code);
        $have    = file_exists($path);
        if (!$warning && $have) {

            cache('create_setting_file_' . $data->code, '1', 5);
            return admin_error('当前配置文件已存在，如果确认要替换请在5秒内再次点击生成按钮');
        }

        $result = create_setting_file($data);
        return $result ? admin_success('生成成功', URL_RELOAD) : admin_error('生成失败');

    }

    public function menu($id, SettingGroup $model)
    {
        /** @var SettingGroup $data */
        $data = $model->find($id);

        $have    = AdminMenu::find(function ($query) use ($data) {
            $query->where('code', $data->code);
        });
        $warning = cache('create_setting_menu_' . $data->code);
        if (!$warning && $have) {

            cache('create_setting_menu_' . $data->code, '1', 5);
            return admin_error('当前配置菜单已存在，如果确认要替换请在5秒内再次点击生成按钮');
        }

        $result = create_setting_menu($data);
        return $result ? admin_success('生成成功', URL_RELOAD) : admin_error('生成失败');
    }

    /**
     * 获取所有项目模块
     * @return array
     */
    protected function getModuleList()
    {

        $app_path    = app()->getAppPath();
        $module_list = [];
        $all_list    = scandir($app_path);

        foreach ($all_list as $item) {
            if ($item !== '.' && $item !== '..' && is_dir($app_path . $item)) {
                $module_list[] = $item;
            }
        }

        return $module_list;
    }


}
