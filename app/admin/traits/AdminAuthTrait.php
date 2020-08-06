<?php


namespace app\admin\traits;


use app\admin\exception\AdminServiceException;
use app\admin\model\AdminLog;
use app\admin\model\AdminUser;
use app\admin\service\AuthService;
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

        // 验证权限
        if (!in_array($url, $this->authExcept, true)) {
            $auth = new AuthService();
            try {
                $this->user = $admin_user = $auth->getAdminUserAuthInfo();

                if ($admin_user->id !== 1 && !$this->checkPermission($admin_user, $url)) {

                    return $request->isGet() ? $this->fetch('public/error/403') : admin_error('无权限');
                }
            } catch (AdminServiceException $exception) {

                return $request->isGet() ? redirect('admin/auth/login') : admin_error('未登录');
            }
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


    public function createAdminLog($user, $menu): void
    {
        $data = [
            'admin_user_id' => $user->id,
            'name'          => $menu->name,
            'log_method'    => $menu->log_method,
            'url'           => request()->pathinfo(),
            'log_ip'        => request()->ip()
        ];
        $log  = AdminLog::create($data);

        $data_arr = [
            'header' => request()->header(),
            'param'  => request()->param(),
        ];

        $log_data = [
            'data' => json_encode($data_arr),
        ];
        $log->adminLogData()->save($log_data);
    }
}