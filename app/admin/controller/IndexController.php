<?php
/**
 * 首页控制器
 * @author yupoxiong<i@yupoxiong.com>
 */

declare (strict_types=1);


namespace app\admin\controller;


use Exception;

class IndexController extends BaseController
{

    /**
     * @return string
     * @throws Exception
     */
    public function index(): string
    {



        return  $this->fetch();
    }

    /**
     * @return string
     * @throws Exception
     */
    public function test(): string
    {
        return  $this->fetch();
    }

    public function testNameThis()
    {
        dump(app('http')->getName());
        dump(request()->controller());

        dump(request()->action());
        return '';
    }
}