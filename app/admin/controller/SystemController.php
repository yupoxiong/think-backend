<?php
/**
 * 系统控制器
 * @author yupoxiong<i@yupoxiong.com>
 */

declare (strict_types=1);


namespace app\admin\controller;

use Exception;

class SystemController extends AdminBaseController
{

    /**
     * @return string
     * @throws Exception
     */
    public function index(): string
    {


        return $this->fetch();
    }

}