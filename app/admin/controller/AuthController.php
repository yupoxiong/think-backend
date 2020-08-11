<?php
/**
 *
 * @author yupoxiong<i@yupoxiong.com>
 */

declare (strict_types=1);


namespace app\admin\controller;


use app\admin\exception\AdminServiceException;
use app\admin\service\AuthService;
use app\admin\validate\AdminUserValidate;
use think\exception\ValidateException;
use think\Request;

class AuthController extends BaseController
{

    protected $authExcept = [
        'admin/auth/login',
        'admin/auth/logout',
    ];


    /**
     * @param Request $request
     * @param AuthService $service
     * @param AdminUserValidate $validate
     * @return string|\think\response\Json
     * @throws \Exception
     */
    public function login(Request $request, AuthService $service, AdminUserValidate $validate)
    {

        $redirect = $request->param('redirect') ?? url('admin/index/index');

        if ($request->isPost()) {
            $param = $request->param();
            try {
                $validate->scene('login')->failException(true)->check($param);

                $username = $param['username'];
                $password = $param['password'];
                $remember = $param['remember'];
                $redirect = $param['redirect'] ?? url('admin/index/index')->build();

                $admin_user = $service->login($username, $password);
                $service->setAdminUserAuthInfo($admin_user, $remember);

            } catch (ValidateException $e) {
                $msg = $e->getMessage();
                return admin_error(lang($msg));
            } catch (AdminServiceException $e) {
                $msg = $e->getMessage();
                return admin_error(lang($msg));
            }

            return admin_success('登录成功', [], $redirect);
        }

        $this->assign([
            'redirect' => $redirect,
        ]);

        return $this->fetch();
    }


    public function logout(AuthService $service)
    {
        $service->logout($this->user);

        return redirect(url('admin/index/index')->build());
    }

}