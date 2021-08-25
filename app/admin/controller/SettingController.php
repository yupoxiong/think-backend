<?php
/**
 * 设置控制器
 */

namespace app\admin\controller;

use Exception;
use think\db\Query;
use think\Request;
use app\common\model\Setting;
use app\common\model\SettingGroup;
use app\common\validate\SettingValidate;
use app\admin\traits\AdminSettingForm;
use think\response\Json;

class SettingController extends AdminBaseController
{
    use AdminSettingForm;

    /**
     * @param Request $request
     * @param Setting $model
     * @return string
     * @throws Exception
     */
    public function index(Request $request, Setting $model): string
    {
        $param = $request->param();
        $data  = $model ->with('setting_group')
            ->scope('where', $param)
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
     * @param Setting $model
     * @param SettingValidate $validate
     * @return string|Json
     * @throws Exception
     */
    public function add(Request $request, Setting $model, SettingValidate $validate)
    {
        if ($request->isPost()) {
            $param           = $request->param();
            $validate_result = $validate->scene('add')->check($param);
            if (!$validate_result) {
                return admin_error($validate->getError());
            }

            foreach ($param['config_name'] as $key => $value) {
                if (($param['config_name'][$key]) === ''
                    || ($param['config_field'][$key] === '')
                    || ($param['config_type'][$key] === '')
                ) {
                    return admin_error('设置信息不完整');
                }

                if (in_array($param['config_type'][$key], ['select', 'multi_select']) && ($param['config_option'][$key] == '')) {
                    return admin_error('设置信息不完整');
                }

                $content[] = [
                    'name'    => $value,
                    'field'   => $param['config_field'][$key],
                    'type'    => $param['config_type'][$key],
                    'content' => $param['config_content'][$key],
                    'option'  => $param['config_option'][$key],
                ];
            }

            $param['content'] = $content;

            $result = $model::create($param);

            $url = URL_BACK;
            if (isset($param['_create']) && ((int)$param['_create']) === 1) {
                $url = URL_RELOAD;
            }

            /** @var SettingGroup $group */
            $group = SettingGroup::find($result->setting_group_id);
            create_setting_file($group);

            return $result ? admin_success('添加成功', $url) : admin_error();
        }

        $this->assign([
            'setting_group_list' => SettingGroup::select(),

        ]);

        return $this->fetch();
    }

    //修改
    public function edit($id, Request $request, Setting $model, SettingValidate $validate)
    {

        $data = $model->findOrEmpty($id);
        if ($request->isPost()) {
            $param           = $request->param();
            $validate_result = $validate->scene('edit')->check($param);
            if (!$validate_result) {
                return admin_error($validate->getError());
            }

            foreach ($param['config_name'] as $key => $value) {
                if (($param['config_name'][$key]) == ''
                    || ($param['config_field'][$key] == '')
                    || ($param['config_type'][$key] == '')
                ) {
                    return admin_error('设置信息不完整');
                }

                if (in_array($param['config_type'][$key], ['select', 'multi_select', 'radio', 'checkbox']) && ($param['config_option'][$key] == '')) {
                    return admin_error('设置信息不完整');
                }

                $content[] = [
                    'name'    => $value,
                    'field'   => $param['config_field'][$key],
                    'type'    => $param['config_type'][$key],
                    'content' => $param['config_content'][$key],
                    'option'  => $param['config_option'][$key],
                ];

            }

            $param['content'] = $content;

            $result = $data->save($param);

            //自动更新配置文件
            $group = SettingGroup::findOrEmpty($data->setting_group_id);
            create_setting_file($group);

            return $result ? admin_success() : admin_error();
        }

        $this->assign([
            'data'               => $data,
            'setting_group_list' => SettingGroup::select(),

        ]);
        return $this->fetch('add');

    }

    /**
     * @param $id
     * @param Setting $model
     * @return Json
     */
    public function del($id, Setting $model): Json
    {
        $check = $model->inNoDeletionIds($id);
        if (false !== $check) {
            return admin_error('ID为' . $check . '的数据不能被删除');
        }

        $result = $model::destroy(static function ($query) use ($id) {
            /** @var Query $query */
            $query->whereIn('id', $id);
        });

        return $result ? admin_success('删除成功', [], URL_RELOAD) : admin_error('删除失败');
    }


    protected function show($id)
    {
        $data = Setting::where('setting_group_id', $id)->select();

        foreach ($data as $key => $value) {

            $content_new = [];

            foreach ($value->content as $kk => $content) {

                $content['form'] = $this->getFieldForm($content['type'], $content['name'], $content['field'], $content['content'], $content['option']);

                $content_new[] = $content;
            }

            $value->content = $content_new;
        }

        //自动更新配置文件
        $group                = SettingGroup::find($id);
        $this->admin['title'] = $group->name;

        $this->assign([
            'data_config' => $data,
        ]);

        return $this->fetch('show');
    }


    //更新设置
    public function update(Request $request, Setting $model)
    {
        $param = $request->param();

        $id = $param['id'];

        $config = $model->findOrEmpty($id);

        $content_data = [];
        foreach ($config->content as $key => $value) {

            switch ($value['type']) {
                case 'image' :
                case 'file':

                    //处理图片上传
                    if (!empty($_FILES[$value['field']]['name'])) {
                        $attachment = new Attachment;
                        $file       = $attachment->upload($value['field']);
                        if ($file) {
                            $value['content'] = $param[$value['field']] = $file->url;
                        }
                    }
                    break;

                case 'multi_file':
                case 'multi_image':

                    if (!empty($_FILES[$value['field']]['name'])) {
                        $attachment = new Attachment;
                        $file       = $attachment->uploadMulti($value['field']);
                        if ($file) {
                            $value['content'] = $param[$value['field']] = json_encode($file);
                        }
                    }
                    break;

                default:
                    $value['content'] = $param[$value['field']];
                    break;
            }

            $content_data[] = $value;
        }

        $config->content = $content_data;
        $result          = $config->save();

        //自动更新配置文件
        $group = SettingGroup::findOrEmpty($config->setting_group_id);
        if (((int)$group->auto_create_file) === 1) {
            create_setting_file($group);
        }

        return $result ? admin_success('修改成功', URL_RELOAD) : admin_error();

    }


    //列表
    public function all(Request $request, SettingGroup $model)
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


    //单个配置的详情
    public function info($id)
    {

        return $this->show($id);
    }


    public function admin()
    {
        return $this->show(1);
    }

}//append_menu
//请勿删除上面的注释，上面注释为自动追加菜单方法标记
