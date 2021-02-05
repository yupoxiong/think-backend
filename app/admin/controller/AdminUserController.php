<?php
/**
 * 后台用户控制器
 * @author yupoxiong<i@yupoxiong.com>
 */

declare (strict_types=1);

namespace app\admin\controller;

use app\admin\model\AdminRole;
use Exception;
use think\Request;
use think\Response;
use think\db\Query;
use think\response\Json;
use think\db\exception\DbException;

use app\admin\model\AdminUser;
use app\admin\validate\AdminUserValidate;

class AdminUserController extends AdminBaseController
{

    /**
     * 列表
     *
     * @param Request $request
     * @param AdminUser $model
     * @return string
     * @throws DbException
     * @throws Exception
     */
    public function index(Request $request, AdminUser $model): string
    {
        $param = $request->param();
        $data  = $model->scope('where', $param)
            ->paginate([
                'list_rows' => $this->admin['admin_per_page'],
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
     * @param AdminUser $model
     * @param AdminUserValidate $validate
     * @return string|Json
     * @throws Exception
     */
    public function add(Request $request, AdminUser $model, AdminUserValidate $validate)
    {
        if ($request->isPost()) {
            $param = $request->param();
            $check = $validate->scene('admin_add')->check($param);
            if (!$check) {
                return admin_error($validate->getError());
            }

            $result   = $model::create($param);
            $redirect = isset($param['_create']) && (int)$param['_create'] === 1 ? URL_RELOAD : URL_BACK;

            return $result ? admin_success('添加成功', [], $redirect) : admin_error('添加失败');
        }

        $this->assign([
            'role_list' => (new AdminRole)->select(),
        ]);

        return $this->fetch();
    }

    /**
     * 修改
     *
     * @param int $id
     * @param Request $request
     * @param AdminUser $model
     * @param AdminUserValidate $validate
     * @return string|Json
     * @throws Exception
     */
    public function edit($id, Request $request, AdminUser $model, AdminUserValidate $validate)
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
            'data'      => $data,
            'role_list' => (new AdminRole)->select(),
        ]);

        return $this->fetch('add');
    }

    /**
     * 删除
     *
     * @param mixed $id
     * @param AdminUser $model
     * @return Response
     */
    public function del($id, AdminUser $model): Response
    {
        $check = $model->checkDeleteId($id);
        if (true !== $check) {
            return admin_error('ID 为' . $check . '的数据无法删除');
        }

        $result = $model::destroy(static function ($query) use ($id) {
            /** @var Query $query */
            $query->whereIn('id', $id);
        });

        return $result ? admin_success('删除成功', [], URL_RELOAD) : admin_error('删除失败');
    }

    /**
     * 启用
     * @param mixed $id
     * @param AdminUser $model
     * @return Json
     */
    public function enable($id, AdminUser $model): Json
    {
        $result = $model->whereIn('id', $id)->update(['status' => 1]);
        return $result ? admin_success('操作成功', [], URL_RELOAD) : admin_error();
    }

    /**
     * 禁用
     * @param mixed $id
     * @param AdminUser $model
     * @return Json
     */
    public function disable($id, AdminUser $model): Json
    {
        if ((is_array($id) && in_array(1, $id)) || $id == 1) {
            return admin_error('超级管理员不能禁用');
        }

        $result = $model->whereIn('id', $id)->update(['status' => 0]);
        return $result ? admin_success('操作成功', [], URL_RELOAD) : admin_error();
    }

    /**
     * 个人资料
     * @param Request $request
     * @return mixed
     * @throws Exception
     */
    public function profile(Request $request, AdminUserValidate $validate)
    {
        if ($request->isPost()) {
            $param = $request->param();
            if ($param['update_type'] === 'password') {

                $validate_result = $validate->scene('password')->check($param);
                if (!$validate_result) {
                    return admin_error($validate->getError());
                }

                if (!password_verify($param['password'], base64_decode($this->user->password))) {
                    return admin_error('当前密码不正确');
                }
                $param['password'] = $param['new_password'];
            }

            return $this->user->save($param) ? admin_success('修改成功', URL_RELOAD) : admin_error();
        }

        $this->assign([
            'data' => $this->user,
        ]);
        return $this->fetch();
    }
}
