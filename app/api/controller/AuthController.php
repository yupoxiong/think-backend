<?php
/**
 *
 * @author yupoxiong<i@yupoxiong.com>
 */

declare (strict_types=1);


namespace app\api\controller;


use app\api\service\TokenService;

class AuthController
{

    public function login()
    {
        return (new TokenService())->getToken(1);
    }

}