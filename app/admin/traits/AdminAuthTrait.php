<?php
/**
 * 验证登录、权限的trait
 */

namespace app\admin\traits;

use think\facade\Cache;
use think\facade\Log;
use think\response\Json;
use think\response\Redirect;
use app\admin\model\AdminUser;
use app\admin\service\AuthService;
use think\exception\HttpResponseException;
use app\admin\exception\AdminServiceException;

trait AdminAuthTrait
{
    protected string $loginDeviceKey = 'admin_user_current_login_device_id_';

    /**
     * 检查登录
     * @return bool
     */
    protected function checkLogin(): bool
    {
        $request = request();
        // 获取当前访问url,应用名+'/'+控制器名+'/'+方法名
        $this->url = $url = parse_name(app('http')->getName())
            . '/' . parse_name($request->controller())
            . '/' . parse_name($request->action());

        $login_except = !empty($this->loginExcept) ? array_map('parse_name', $this->loginExcept) : $this->loginExcept;

        if (in_array($url, $login_except, true)) {
            return true;
        }


        // 验证登录
        try {
            $this->user = (new AuthService)->getAdminUserAuthInfo();

        } catch (AdminServiceException $exception) {
            if(app()->isDebug()){
                Log::record('验证登录失败，信息：'.$exception->getMessage());
            }

            $redirect  = url($url)->build();
            $login_url = url('admin/auth/login', ['redirect' => $redirect])->build();

            if ($request->isGet()) {
                throw new HttpResponseException(redirect($login_url));
            }
            throw new HttpResponseException(admin_error('未登录', [], $login_url, 401));
        }

        return true;
    }


    /**
     * 检查权限
     * @return Json|Redirect|boolean
     */
    public function checkAuth()
    {
        $url = $this->url;

        $request = request();

        $login_except = !empty($this->loginExcept) ? array_map('parse_name', $this->loginExcept) : $this->loginExcept;

        // 如果在无需登录的URL里，直接返回
        if (in_array($url, $login_except, true)) {
            return true;
        }

        $auth_except = !empty($this->authExcept) ? array_map('parse_name', $this->authExcept) : $this->authExcept;

        // 如果在无需授权的URL里，直接返回
        if (in_array($url, $auth_except, true)) {
            return true;
        }

        // 验证权限
        if ($this->user->id !== 1 && !$this->checkPermission($this->user, $url)) {
            return $request->isGet() ? $this->fetch('public/error/403') : admin_error('无权限');
        }

        // 如果是提前验证权限
        if ($request->param('check_auth')) {
            throw new HttpResponseException(admin_success('验证权限成功', [], URL_CURRENT));
        }

        return true;
    }


    /**
     * 权限检查
     * @param AdminUser $user
     * @param string $url
     * @return bool
     */
    public function checkPermission(AdminUser $user, string $url): bool
    {
        return in_array($url, $this->authExcept, true) || in_array($url, $user->auth_url, true);
    }

    /**
     * 单设备登录检查
     * @param $user
     * @return bool
     */
    public function checkOneDeviceLogin(): bool
    {
        $request = request();
        $check   = (int)setting('admin.safe.one_device_login');
        if (!$check) {
            return true;
        }

        $login_except = !empty($this->loginExcept) ? array_map('parse_name', $this->loginExcept) : $this->loginExcept;

        if (in_array($this->url, $login_except, true)) {
            return true;
        }

        $device_id = (new AuthService)->getDeviceId($this->user);

        $login_device_id = $this->getLoginDeviceId($this->user);

        if ($login_device_id && $login_device_id !== $device_id) {
            $login_url = url('admin/auth/login')->build();
            if ($request->isGet()) {
                throw new HttpResponseException(redirect($login_url));
            }
            throw new HttpResponseException(admin_error('未登录', [], $login_url, 401));
        }

        return $this->setLoginDeviceId($this->user);
    }


    /**
     * 获取当前登录的设备ID
     * @param $user
     * @return string
     */
    public function getLoginDeviceId($user): string
    {
        $cache_key = $this->loginDeviceKey . $user->id;

        return (string)Cache::get($cache_key);
    }

    /**
     * 设置登录的设备ID
     * @param $user
     * @return bool
     */
    public function setLoginDeviceId($user): bool
    {
        $service   = new AuthService();
        $device_id = $service->getDeviceId($user);
        $cache_key = $this->loginDeviceKey . $user->id;
        return Cache::set($cache_key, $device_id);
    }

    /**
     * 清除当前登录的设备ID
     * @param $user
     * @return bool
     */
    public function clearLoginDeviceId($user): bool
    {
        $cache_key = $this->loginDeviceKey . $user->id;

        return Cache::delete($cache_key);


    }

}
