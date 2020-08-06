<?php
/**
 * 后台基类
 * @author yupoxiong<i@yupoxiong.com>
 */

declare (strict_types=1);


namespace app\admin\controller;

use app\admin\event\AdminUser;
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

    /**
     * 当前访问的URL
     * @var string
     */
    protected $url;

    /**
     * 无需验证的URL
     * @var array
     */
    protected $authExcept = [];

    /**
     * 当前后台用户
     * @var AdminUser
     */
    protected $user;


    public function __construct(View $view)
    {
        $this->view = $view;
        $this->initialize();
    }


    public function initialize()
    {

        $this->checkAuth();

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

        if ('admin/auth/login' !== $this->url && !$this->admin['is_pjax']) {
            $this->admin['menu'] = $this->getLeftMenu($this->user);
        }


        $this->admin['debug'] = Env::get('app_debug');

        // 赋值后台变量
        $this->assign([
            'admin' => $this->admin,
        ]);

        return $this->view->fetch($template, $vars);
    }


}