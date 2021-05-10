<?php
/**
 * 后台基类
 * @author yupoxiong<i@yupoxiong.com>
 */

declare (strict_types=1);

namespace app\admin\controller;

use Exception;
use think\View;
use think\facade\Env;
use app\admin\model\{AdminMenu,AdminUser};
use app\admin\traits\{AdminAuthTrait, AdminPhpOffice, AdminTreeTrait};

class AdminBaseController
{
    use AdminTreeTrait;
    use AdminAuthTrait;
    use AdminPhpOffice;

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
     * 无需验证登录的url
     * @var array
     */
    protected array $loginExcept =[];

    /**
     * 无需验证权限的URL
     * @var array
     */
    protected $authExcept = [];

    /**
     * 当前后台用户
     * @var AdminUser
     */
    protected $user;

    public function __construct()
    {
        $this->initialize();
    }

    public function initialize(): void
    {
        $this->checkLogin();
        $this->checkAuth();

        $this->view =  app()->make(View::class);

        $this->admin['admin_list_rows'] = cookie('admin_list_rows') ?? 10;
        $this->admin['admin_list_rows'] = $this->admin['admin_list_rows'] < 100 ? $this->admin['admin_list_rows'] : 100;
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
        /** @var AdminMenu $menu */
        $menu = (new AdminMenu)->where(['url' => $this->url])->find();
        if ($menu) {
            $menu_all = (new AdminMenu)->field('id,parent_id,name,icon')->select()->toArray();

            $this->admin['title']      = $menu->name;
            $this->admin['breadcrumb'] = $this->getBreadCrumb($menu_all, $menu->id);
        }

        $this->admin['name']       = '后台';
        $this->admin['is_pjax']    = request()->isPjax();
        $this->admin['upload_url'] = url('admin/file/upload')->build();

        if ('admin/auth/login' !== $this->url && !$this->admin['is_pjax']) {
            $this->admin['menu'] = $this->getLeftMenu($this->user->getShowMenu(),$menu->id??0);
        }

        $this->admin['debug'] = Env::get('app_debug');
        $this->admin['top_nav'] = 0;

        $this->admin['top_search'] = 0;
        $this->admin['top_message'] = 0;
        $this->admin['top_notification'] = 0;

        // 赋值后台变量
        $this->assign([
            'admin' => $this->admin,
            'user'  => $this->user,
        ]);

        return $this->view->fetch($template, $vars);
    }

}
