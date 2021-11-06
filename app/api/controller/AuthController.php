<?php
/**
 *
 * @author yupoxiong<i@yupoxiong.com>
 */

declare (strict_types=1);


namespace app\api\controller;


use app\api\exception\ApiServiceException;
use app\api\service\TokenService;

class AuthController
{

    public function login()
    {
        try {
            return api_success([
                'token' => (new TokenService())->getToken(1),
            ]);
        } catch (ApiServiceException $e) {
            return  api_error('登录错误');
        }
    }

}