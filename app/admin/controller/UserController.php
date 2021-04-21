<?php
/**
 * 用户控制器
 */

namespace app\admin\controller;

use Exception;
use think\Request;
use think\response\Json;
use app\common\model\User;
use app\common\model\UserLevel;

use app\common\validate\UserValidate;

class UserController extends AdminBaseController
{

    /**
     * 列表
     *
     * @param Request $request
     * @param User $model
     * @return string
     * @throws Exception
     */
    public function index(Request $request, User $model): string
    {
        $param = $request->param();
        $data  = $model->with('user_level')->scope('where', $param)
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
     * 添加
     *
     * @param Request $request
     * @param User $model
     * @param UserValidate $validate
     * @return string|Json
     * @throws Exception
     */
    public function add(Request $request, User $model, UserValidate $validate)
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

        $this->assign([
    'user_level_list' => UserLevel::select(),

]);



        return $this->fetch();
    }

    /**
     * 修改
     *
     * @param $id
     * @param Request $request
     * @param User $model
     * @param UserValidate $validate
     * @return string|Json
     * @throws Exception
     */
    public function edit($id, Request $request, User $model, UserValidate $validate)
    {
        $data = $model->findOrEmpty($id);
        if ($request->isPost()) {
            $param = $request->param();
            $check = $validate->scene('admin_edit')->check($param);
            if (!$check) {
                return admin_error($validate->getError());
            }
                        //处理头像上传
            if (!empty($_FILES['avatar']['name'])) {
                $attachment_avatar = new \app\common\model\Attachment;
                $file_avatar       = $attachment_avatar->upload('avatar');
                if ($file_avatar) {
                    $param['avatar'] = $file_avatar->url;
                }
            }
            

            $result = $data->save($param);

            return $result ? admin_success('修改成功', [], URL_BACK) : admin_error('修改失败');
        }

        $this->assign([
            'data' => $data,
            'user_level_list' => UserLevel::select(),

        ]);

        return $this->fetch('add');
    }

    /**
     * 删除
     *
     * @param mixed $id
     * @param User $model
     * @return Json
     */
    public function del($id, User $model): Json
    {
        if (count($model->noDeletionIds) > 0) {
            if (is_array($id)) {
                if (array_intersect($model->noDeletionIds, $id)) {
                    return admin_error('ID为' . implode(',', $model->noDeletionIds) . '的数据无法删除');
                }
            }  else if (in_array((int)$id, $model->noDeletionIds, true)) {
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
     * @param Request $request
     * @return Json
     */
    public function import(Request $request): Json
    {
        $param           = $request->param();
        $field_name_list = ['用户等级','账号','密码','手机号','昵称','头像','是否启用',];
        if (isset($param['action']) && $param['action'] === 'download_example') {
            $this->downloadExample($field_name_list);
        }

        $field_list = ['user_level_id','username','password','mobile','nickname','avatar','status',];
        $result = $this->importData('file','user',$field_list);

        return true === $result ? admin_success('操作成功', [], URL_RELOAD) : admin_error($result);
    }
}
