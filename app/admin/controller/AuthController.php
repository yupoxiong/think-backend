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


    /**
     * @return string
     * @throws \Exception
     */
    public function login()
    {


        config('app.app_trace',false);

        return $this->fetch();
    }


    public function userLogin(Request $request, AuthService $service, AdminUserValidate $validate)
    {
        $param = $request->param();
        try {
            $validate->scene('login')->failException(true)->check($param);

            $username = $param['username'];
            $password = $param['password'];

            $admin_user = $service->login($username, $password);

        } catch (ValidateException $e) {
            $msg = $e->getMessage();

            return admin_error(lang($msg));

        } catch (AdminServiceException $e) {
            $msg = $e->getMessage();

            return admin_error(lang($msg));
        }

        return admin_success('登录成功', $admin_user);
    }

    public function logout()
    {

    }


    public function userLogout()
    {

    }
}