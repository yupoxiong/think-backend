<?php
/**
 * 后台基类
 * @author yupoxiong<i@yupoxiong.com>
 */

declare (strict_types=1);


namespace app\admin\controller;

use app\admin\traits\AdminTreeTrait;
use Exception;
use think\View;

class BaseController
{

    use AdminTreeTrait;

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


    public function __construct(View $view)
    {
        $this->view = $view;
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
        $this->admin['name'] = '后台';
        $this->admin['is_pjax'] = request()->isPjax();

        if(!$this->admin['is_pjax']){

        }

        // 赋值后台变量
        $this->assign([
            'admin' => $this->admin,
        ]);

        return $this->view->fetch($template, $vars);
    }


}