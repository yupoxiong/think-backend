<?php
/**
 * 用户等级控制器
 */

namespace app\admin\controller;

use Exception;
use think\Request;
use think\response\Json;
use app\common\model\UserLevel;

use app\common\validate\UserLevelValidate;

class UserLevelController extends AdminBaseController
{

    /**
     * 列表
     * @param Request $request
     * @param UserLevel $model
     * @return mixed
     * @throws Exception
     */
    public function index(Request $request, UserLevel $model)
    {
        $param = $request->param();
        $data  = $model->scope('where', $param);
        if (isset($param['export_data']) && (int)$param['export_data'] === 1) {
            $header = ['ID', '名称', '简介', '图片', '是否启用', '创建时间',];
            $body   = [];
            $data   = $model->select();
            foreach ($data as $item) {
                $record                = [];
                $record['id']          = $item->id;
                $record['name']        = $item->name;
                $record['description'] = $item->description;
                $record['img']         = $item->img;
                $record['status']      = $item->status_text;
                $record['create_time'] = $item->create_time;

                $body[] = $record;
            }
            return $this->exportData($header, $body, 'user_level-' . date('Y-m-d-H-i-s'));
        }
        $data = $data->paginate([
            'list_rows' => $this->admin['admin_list_rows'],
            'var_page'  => 'page',
            'query'     => $request->get()
        ]);
        //关键词，排序等赋值
        $this->assign($request->get());

        $this->assign([
            'data'  => $data,
            'page'  => $data->render(),
            'total' => $data->total(),

        ]);
        return $this->fetch();
    }

    //添加
    public function add(Request $request, UserLevel $model, UserLevelValidate $validate)
    {
        if ($request->isPost()) {
            $param           = $request->param();
            $validate_result = $validate->scene('add')->check($param);
            if (!$validate_result) {
                return admin_error($validate->getError());
            }

            $result = $model::create($param);

            $url = URL_BACK;
            if (isset($param['_create']) && (int)$param['_create'] === 1) {
                $url = URL_RELOAD;
            }

            return $result ? admin_success('添加成功', [], $url) : admin_error();
        }


        return $this->fetch();
    }

    /**
     * 修改
     *
     * @param int $id
     * @param Request $request
     * @param AdminRole $model
     * @param AdminRoleValidate $validate
     * @return string|Json
     * @throws Exception
     */
    public function edit($id, Request $request, UserLevel $model, UserLevelValidate $validate)
    {
        $data = $model->findOrEmpty($id);
        if ($request->isPost()) {
            $param = $request->param();
            $check = $validate->scene('admin_edit')->check($param);
            if (!$check) {
                return admin_error($validate->getError());
            }

            $result = $data->save($param);

            return $result ? admin_success('修改成功', [], URL_BACK) : admin_error('修改失败');
        }

        $this->assign([
            'data' => $data,

        ]);

        return $this->fetch('add');
    }


    //删除
    public function del($id, UserLevel $model)
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

        if ($model->softDelete) {
            $result = $model->whereIn('id', $id)->useSoftDelete('delete_time', time())->delete();
        } else {
            $result = $model->whereIn('id', $id)->delete();
        }

        return $result ? admin_success('操作成功', URL_RELOAD) : admin_error();
    }


    /**
     * 启用
     * @param mixed $id
     * @param UserLevel $model
     * @return Json
     */
    public function enable($id, UserLevel $model): Json
    {
        $result = $model->whereIn('id', $id)->update(['status' => 1]);
        return $result ? admin_success('操作成功', [], URL_RELOAD) : admin_error();
    }


    /**
     * 禁用
     * @param mixed $id
     * @param UserLevel $model
     * @return Json
     */
    public function disable($id, UserLevel $model): Json
    {
        $result = $model->whereIn('id', $id)->update(['status' => 0]);
        return $result ? admin_success('操作成功', [], URL_RELOAD) : admin_error();
    }

}
