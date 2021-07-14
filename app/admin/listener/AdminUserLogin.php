<?php
/**
 *
 * @author yupoxiong<i@yupoxiong.com>
 */

declare (strict_types=1);


namespace app\admin\listener;


use app\admin\service\AdminLogService;

class AdminUserLogin
{
    public function handle($user)
    {
        // 记录日志
        (new AdminLogService())->create($user, '登录');

    }
}