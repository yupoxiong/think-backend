<?php
/**
 *
 * @author yupoxiong<i@yupoxiong.com>
 */

declare (strict_types=1);


namespace app\admin\controller;


use Exception;

class ErrorController extends AdminBaseController
{

    /**
     * @throws Exception
     */
    public function err403(): string
    {
        return $this->fetch('error/403');
    }
}