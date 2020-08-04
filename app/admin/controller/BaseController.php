<?php
/**
 * 后台基类
 * @author yupoxiong<i@yupoxiong.com>
 */

declare (strict_types=1);


namespace app\admin\controller;

use app\admin\exception\AdminServiceException;
use app\admin\service\AuthService;
use app\admin\traits\AdminAuthTrait;
use app\admin\traits\AdminTreeTrait;
use Exception;
use think\facade\Env;
use think\View;

class BaseController
{

    use AdminTreeTrait;
    use AdminAuthTrait;

    /**
     * 后台主变量
     * @var array
     */
    protected $admin;

    /**
     * 视图变量
     * @var View
     */
    protected $view;

    protected $authExcept;


    public function __construct(View $view)
    {
        $this->view = $view;
    }


    public function init()
    {


        $request = request();
        // 获取当前访问url,应用名+'/'+控制器名+'/'+方法名
        $url = app('http')->getName() . '/' . $request->controller() . '/' . $request->action();

        // 验证权限
        if (!in_array($url, $this->authExcept, true)) {
            $auth =new AuthService();
            try{
                $auth->getAdminUserAuthInfo();

            }catch (AdminServiceException $exception){

            }


            if (!$this->isLogin()) {
                error('未登录', 'auth/login');
            } else if ($this->user->id !== 1 && !$this->authCheck($this->user)) {
                error('无权限', $this->request->isGet() ? null : URL_CURRENT);
            }
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

        $this->admin['per_page'] = cookie('admin_per_page') ?? 10;
        $this->admin['per_page'] = $this->admin['per_page'] < 100 ? $this->admin['per_page'] : 100;
    }

    /**
     * 模板赋值
     * @param $name
     * @param null $value
     * @return View
     */
    protected function assign($name, $value = null): View
    {
        return $this->view->assign($name, $value);
    }


    /**
     * 渲染模板
     * @param string $template
     * @param array $vars
     * @return string
     * @throws Exception
     */
    protected function fetch(string $template = '', array $vars = []): string
    {
        $this->admin['name']    = '后台';
        $this->admin['is_pjax'] = request()->isPjax();

        if (!$this->admin['is_pjax']) {

        }

        $this->admin['debug'] = Env::get('app_debug');

        // 赋值后台变量
        $this->assign([
            'admin' => $this->admin,
        ]);

        return $this->view->fetch($template, $vars);
    }


}