<?php
/**
 *
 * @author yupoxiong<i@yupoxiong.com>
 */

declare (strict_types=1);


namespace app\admin\service;


use think\facade\Event;
use think\facade\Log;

class ModelService
{
    public function create()
    {

        Event::listen('AdminUserLogin',function ($admin_user){
            Log::info('捕捉到了'.$admin_user->nickname.'的登录事件');
        });

        Event::subscribe('app\subscribe\User');
    }

    public function read()
    {

    }
}