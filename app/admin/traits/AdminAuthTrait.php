<?php


namespace app\admin\traits;


use app\admin\exception\AdminServiceException;
use app\admin\model\AdminLog;
use app\admin\model\AdminUser;
use app\admin\service\AuthService;
use think\exception\HttpResponseException;
use think\response\Json;
use think\response\Redirect;

trait AdminAuthTrait
{


    /**
     * 检查权限
     * @return Json|Redirect|boolean
     */
    public function checkAuth()
    {

        $request = request();
        // 获取当前访问url,应用名+'/'+控制器名+'/'+方法名
        $this->url = $url = parse_name(app('http')->getName())
            . '/' . parse_name($request->controller())
            . '/' . parse_name($request->action());


        $auth_except = !empty($this->authExcept) ? array_map('parse_name', $this->authExcept) : $this->authExcept;

        // 验证权限
        if (!in_array($url, $auth_except, true)) {
            $auth = new AuthService();
            try {
                $this->user = $admin_user = $auth->getAdminUserAuthInfo();

                if ($admin_user->id !== 1 && !$this->checkPermission($admin_user, $url)) {

                    dump(222);
                    return $request->isGet() ? $this->fetch('public/error/403') : admin_error('无权限');
                }
            } catch (AdminServiceException $exception) {

                $redirect  = url($url)->build();
                $login_url = url('admin/auth/login', ['redirect' => $redirect])->build();

                if ($request->isGet()) {
                    throw new HttpResponseException(redirect($login_url));
                }

                throw new HttpResponseException(admin_error('未登录', [], $login_url, 401));
            }
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
    public function checkPermission($user, $url): bool
    {
        return in_array($url, $this->authExcept, true) || in_array($url, $user->auth_url, true);
    }


}