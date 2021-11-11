<?php
/**
 * 后台基类
 * @author yupoxiong<i@yupoxiong.com>
 */

declare (strict_types=1);

namespace app\admin\controller;

use Exception;
use think\exception\HttpResponseException;
use think\response\Json;
use think\View;
use think\facade\Env;
use app\admin\model\{AdminMenu, AdminUser};
use app\admin\traits\{AdminAuthTrait, AdminPhpOffice, AdminTreeTrait};

class AdminBaseController
{
    // 引入树相关trait
    use AdminTreeTrait;

    // 引入权限判断相关trait
    use AdminAuthTrait;

    // 引入office相关trait
    use AdminPhpOffice;

    /**
     * 后台主变量
     * @var array
     */
    protected array $admin;

    /**
     * 视图变量
     * @var View
     */
    protected View $view;

    /**
     * 当前访问的URL
     * @var string
     */
    protected string $url;


    protected $menu;

    /**
     * 无需验证登录的url
     * @var array
     */
    protected array $loginExcept = [];

    /**
     * 无需验证权限的URL
     * @var array
     */
    protected array $authExcept = [
        'admin/error/err403',
        'admin/error/err404',
        'admin/error/err500',
    ];

    /**
     * 当前后台用户
     * @var AdminUser
     */
    protected AdminUser $user;

    public function __construct()
    {
        $this->initialize();
    }

    public function initialize(): void
    {
        $this->view = app()->make(View::class);

        $this->checkLogin();
        $this->checkAuth();
        $this->checkOneDeviceLogin();
        $this->checkToken();

        // 分页每页数量
        $this->admin['admin_list_rows'] = cookie('admin_list_rows') ?? 10;
        // 限制每页数量最多不超过100
        $this->admin['admin_list_rows'] = $this->admin['admin_list_rows'] < 100 ? $this->admin['admin_list_rows'] : 100;
        /** @var AdminMenu $menu */
        $this->menu = (new AdminMenu)->where(['url' => $this->url])->findOrEmpty();

        if (isset($this->user) && !$this->menu->isEmpty() && request()->method() === $this->menu->log_method) {
            $this->createLog($this->user, $this->menu->name);
        }

    }

    /**
     * 验证csrf token
     */
    protected function checkToken(): void
    {
        $default_list = 'add,edit,del,import,update,profile';
        $action_list  = setting('admin.safe.check_token_action_list') ?? $default_list;
        $action_list  = explode(',', $action_list);
        $is_check     = (bool)setting('admin.safe.check_token');

        if ($is_check && request()->isPost() && in_array(request()->action(true), $action_list, true)) {

            $check = request()->checkToken();

            if (false === $check) {
                throw new HttpResponseException(admin_error('token验证失败', [], URL_CURRENT, 403));
            }
        }
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
        // 顶部导航
        $this->admin['top_nav'] = (int)setting('admin.display.top_nav');

        $this->admin['base'] = setting('admin.base');

        $current_top_id = 0;

        if (!$this->menu->isEmpty()) {
            $menu     = $this->menu;
            $menu_all = (new AdminMenu)->field('id,parent_id,name,icon')->select()->toArray();

            $this->admin['title']      = $menu->name;
            $this->admin['breadcrumb'] = $this->getBreadCrumb($menu_all, $menu->id);
            if ($this->admin['top_nav'] === 1) {
                $current_top_id = $this->getTopParentIdById($menu_all, $menu->id);
            }
        }

        $this->admin['is_pjax']    = request()->isPjax();
        $this->admin['upload_url'] = url('admin/file/upload')->build();
        $this->admin['logout_url'] = url('admin/auth/logout')->build();


        if ('admin/auth/login' !== $this->url && !$this->admin['is_pjax']) {

            $show_menu = $this->user->getShowMenu($this->admin['top_nav']);

            $this->admin['top_menu']  = $show_menu['top'];
            $this->admin['left_menu'] = $this->getLeftMenu($show_menu['left'][$current_top_id], $menu->id ?? 0);
        }

        $this->admin['debug'] = Env::get('app_debug') ? 1 : 0;
        // 顶部导航
        $this->admin['top_nav'] = 1;
        // 顶部搜索
        $this->admin['top_search'] = 0;
        // 顶部消息
        $this->admin['top_message'] = 0;
        // 顶部通知
        $this->admin['top_notification'] = 0;
        // 文件删除url
        $this->admin['file_del_url'] = url('admin/file/del');
        $this->admin['map']          = config('map');

        $this->admin['user'] = $this->user ?? new AdminUser();

        // 赋值后台变量
        $this->assign([
            'admin' => $this->admin,
        ]);

        return $this->view->fetch($template, $vars);
    }

    /**
     * @param $name
     * @param $arguments
     * @return string|Json
     * @throws Exception
     */
    public function __call($name, $arguments)
    {
        if (request()->isPost()) {
            return admin_error('页面未找到');
        }
        return $this->fetch('error/404');
    }
}
