<?php
/**
 *
 * @author yupoxiong<i@yupoxiong.com>
 */

declare (strict_types=1);


namespace app\admin\controller;


class AuthController extends BaseController
{


    /**
     * @return string
     * @throws \Exception
     */
    public function login()
    {
        return $this->fetch();
    }


    public function userLogin()
    {
        return admin_success('登录成功');
    }

    public function logout()
    {

    }


    public function userLogout()
    {

    }
}