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

class AdminUserLogin
{

    public function handle($user): void
    {
        try {
            // 记录日志
            (new AdminLogService())->create($user, '登录');
        } catch (AdminServiceException $e) {
            Log::error('记录退出出错，信息：'.$e->getMessage());
        }
    }
}
