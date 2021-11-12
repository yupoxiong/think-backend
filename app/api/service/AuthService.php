<?php
/**
 *
 * @author yupoxiong<i@yupoxiong.com>
 */

declare (strict_types=1);


namespace app\api\service;

use app\api\exception\ApiServiceException;
use app\common\model\User;
use think\facade\Event;

class AuthService extends ApiBaseService
{
    protected User $model;

    public function __construct()
    {
        $this->model = new User();
    }

    /**
     * @param $username
     * @param $password
     * @return string
     * @throws ApiServiceException
     */
    public function usernameLogin($username,$password): string
    {
        /** @var User $user */
        $user = $this->model->where('username','=',$username)->findOrEmpty();

        if($user->isEmpty()){
            throw new ApiServiceException('用户不存在');
        }

        $verify = password_verify($password, base64_decode($user->password));
        if (!$verify) {
            throw new ApiServiceException('密码错误');
        }

        // 检查是否被冻结
        if ($user->status !== 1) {
            throw new ApiServiceException('账号被冻结');
        }

        // Event_事件 管理用户登录
        Event::trigger('UserLogin', $user);

        $tokenService = new TokenService();
        return $tokenService->getToken($user->id);
    }


}