<?php
/**
 *
 * @author yupoxiong<i@yupoxiong.com>
 */

declare (strict_types=1);


namespace app\admin\listener;


use think\facade\Log;

class AdminUserLogin
{
    public function handle($user)
    {

        // 事件监听处理
        Log::error($user);

    }
}