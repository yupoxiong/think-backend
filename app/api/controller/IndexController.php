<?php
/**
 *
 * @author yupoxiong<i@yupoxiong.com>
 */

declare (strict_types=1);


namespace app\api\controller;


class IndexController extends ApiBaseController
{

    public function index()
    {
        return api_success();
    }
}