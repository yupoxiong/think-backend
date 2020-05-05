<?php
/**
 *
 * @author yupoxiong<i@yupoxiong.com>
 */

declare (strict_types=1);


namespace app\admin\controller;


use app\admin\model\AdminUser;
use app\admin\validate\AdminUserValidate;
use Exception;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use think\Request;
use think\Response;
use think\response\Json;

class AdminUserController extends BaseController
{

    /**
     * 显示资源列表
     * @throws Exception
     */
    public function index(): string
    {
        return $this->fetch();
    }

    /**
     * 显示创建资源表单页.
     * @throws Exception
     */
    public function create(): string
    {
        return $this->fetch();
    }

    /**
     * 保存新建的资源
     *
     * @param Request $request
     * @param AdminUser $model
     * @param AdminUserValidate $validate
     * @return Json
     */
    public function save(Request $request, AdminUser $model, AdminUserValidate $validate): Json
    {

        $param = $request->param();
        $check = $validate->scene('admin_save')->check($param);
        if (!$check) {
            return admin_error($validate->getError());
        }

        $result = $model::create($param);

        return $result ? admin_success() : admin_error();

    }

    /**
     * 显示指定的资源
     *
     * @param int $id
     * @param AdminUser $model
     * @return string
     * @throws Exception
     */
    public function read($id,AdminUser $model): string
    {
        $data = $model->find($id);
        
        return $this->fetch();
    }

    /**
     * 显示编辑资源表单页.
     *
     * @param int $id
     * @return string
     * @throws Exception
     */
    public function edit($id): string
    {
        return $this->fetch();
    }

    /**
     * 保存更新的资源
     *
     * @param Request $request
     * @param int $id
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * 删除指定资源
     *
     * @param int $id
     * @return Response
     */
    public function delete($id)
    {
        //
    }
}