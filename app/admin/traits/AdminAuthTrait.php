<?php


namespace app\admin\traits;


use app\admin\exception\AdminServiceException;
use app\admin\model\AdminUser;
use app\admin\service\AuthService;

trait AdminAuthTrait
{


    public function checkAuth()
    {

        $request = request();
        // 获取当前访问url,应用名+'/'+控制器名+'/'+方法名
        $url = app('http')->getName() . '/' . $request->controller() . '/' . $request->action();

        // 验证权限
        if (!in_array($url, $this->authExcept, true)) {
            $auth = new AuthService();
            try {
                $admin_user = $auth->getAdminUserAuthInfo();

                if ($admin_user->id !== 1 && !$this->checkPermission($admin_user, $url)) {
                    error('无权限', $this->request->isGet() ? null : URL_CURRENT);
                }


            } catch (AdminServiceException $exception) {
                if ($request->isGet()) {
                    return $this->fetch('public/error/401');
                }
            }


            if (!$this->isLogin()) {
                error('未登录', 'auth/login');
            } else
        }

        if ((int)$request->param('check_auth') === 1) {
            success();
        }

        //记录日志
        $menu = AdminMenu::get(['url' => $this->url]);
        if ($menu) {
            $this->admin['title'] = $menu->name;
            if ($menu->log_method === $request->method()) {
                $this->createAdminLog($this->user, $menu);
            }
        }
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