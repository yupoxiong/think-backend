<?php
/**
 *
 * @author yupoxiong<i@yupoxiong.com>
 */

declare (strict_types=1);


namespace app\admin\listener;


use app\admin\exception\AdminServiceException;
use app\admin\service\AdminLogService;
use think\facade\Log;

class AdminUserLogout
{
    public function handle($user)
    {
        try {
            Log::error('eeee');
            (new AdminLogService)->create($user, '退出');
        } catch (AdminServiceException $e) {
            Log::error('记录退出出错，信息：'.$e->getMessage());
        }

    }
}