<?php
/**
 * 登录相关
 * @author yupoxiong<i@yupoxiong.com>
 */

declare (strict_types=1);


namespace app\admin\service;


use app\admin\exception\AdminServiceException;
use app\admin\model\AdminUser;
use think\facade\Cookie;
use think\facade\Event;
use think\facade\Log;
use think\facade\Session;

class AuthService extends AdminService
{
    protected $model;

    /**
     * @var string 保存登录用户信息cookie和session的[ID]key值
     */
    protected $store_uid_key = 'admin_user_id';
    /**
     * @var string 保存登录用户信息cookie和session的[签名]key值
     */
    protected $store_sign_key = 'admin_user_sign';
    /**
     * @var mixed|string 用来签名加密/解密的key
     */
    protected $admin_key = '_ThisClassDefaultKey_';

    public function __construct()
    {
        $this->model = new AdminUser();

        $this->admin_key      = config('admin.safe.admin_key') ?? $this->admin_key;
        $this->store_uid_key  = config('admin.safe.store_uid_key') ?? $this->store_uid_key;
        $this->store_sign_key = config('admin.safe.store_sign_key') ?? $this->store_sign_key;
    }

    /**
     * 用户登录
     * @param $username
     * @param $password
     * @return AdminUser
     * @throws AdminServiceException
     */
    public function login($username, $password): AdminUser
    {

        $admin_user = $this->model->where('username', '=', $username)->findOrEmpty();

        if ($admin_user->isEmpty()) {
            throw new AdminServiceException('用户不存在');
        }

        /** @var AdminUser $admin_user */
        $verify = password_verify($password, base64_decode($admin_user->password));
        if (!$verify) {
            throw new AdminServiceException('密码错误');
        }

        // 检查是否被冻结
        if ($admin_user->status !== 1) {
            throw new AdminServiceException('账号被冻结');
        }

        // Event_事件 管理用户登录
        Event::trigger('AdminUserLogin', $admin_user);

        // 记录日志
        (new AdminLogService())->create($admin_user, '登录');

        return $admin_user;
    }


    /**
     * 设置用户登录信息
     * @param $admin_user
     * @param bool $remember
     */
    public function setAdminUserAuthInfo($admin_user, $remember = false): void
    {
        Session::set($this->store_uid_key, $admin_user->id);
        if ($remember) {
            Cookie::forever($this->store_uid_key, $admin_user->id);
            Cookie::forever($this->store_sign_key, $this->getUserSign($admin_user));
        }
    }

    /**
     * 获取登录用户信息
     * @return AdminUser
     * @throws AdminServiceException
     */
    public function getAdminUserAuthInfo(): AdminUser
    {
        //  当前管理员ID
        $admin_user_id = 0;
        // 当前获取登录信息的方式
        $store_from    = 0;

        if (Session::has($this->store_uid_key)) {
            // session
            $store_from    = 1;
            $admin_user_id = (int)Session::get($this->store_uid_key);
        } else if (Cookie::has($this->store_uid_key)) {
            // cookie
            $store_from    = 2;
            $admin_user_id = (int)Cookie::get($this->store_uid_key);
        }

        if ($admin_user_id === 0) {
            throw new AdminServiceException('未找到登录信息');
        }

        $admin_user = $this->model
            ->where('id', '=', $admin_user_id)
            ->findOrEmpty();

        /** @var AdminUser $admin_user */
        if (!$admin_user) {
            throw new AdminServiceException('用户不存在');
        }

        if ($admin_user->status !== 1) {
            throw new AdminServiceException('用户被冻结');
        }

        // 如果是cookie中的用户，验证sign是否正确
        if ($store_from === 2) {
            $cookie_sign = Cookie::get($this->store_sign_key);
            $check_sign  = $this->getUserSign($admin_user);
            if ($cookie_sign !== $check_sign) {
                throw new AdminServiceException('Cookie签名验证失败');
            }
        }

        return $admin_user;
    }

    /**
     * 退出
     * @param AdminUser $admin_user
     */
    public function logout($admin_user): bool
    {
        // Event_事件 管理用户退出
        Event::trigger('AdminUserLogout', $admin_user);

        $this->clearAuthInfo();

        return true;
    }

    public function checkImageCaptcha($param)
    {

    }

    /**
     * 清除登录用户信息
     */
    public function clearAuthInfo(): void
    {
        Session::delete($this->store_uid_key);

        Cookie::delete($this->store_uid_key);
        Cookie::delete($this->store_sign_key);

    }

    /**
     * 获取签名
     * @param $admin_user
     * @return string
     */
    public function getUserSign($admin_user): string
    {
        return md5(md5($this->admin_key . $admin_user->id) . $this->admin_key);
    }


}