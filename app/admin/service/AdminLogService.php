<?php
/**
 *
 * @author yupoxiong<i@yupoxiong.com>
 */

declare (strict_types=1);


namespace app\admin\service;


use app\admin\model\AdminUser;
use app\admin\exception\AdminServiceException;
use app\admin\model\AdminLog;

use Exception;
use think\facade\Db;

class AdminLogService extends AdminService
{

    protected $model;

    public function __construct()
    {
        $this->model = new AdminLog();
    }

    /**
     * 创建日志
     * @param AdminUser $user 用户
     * @param string $name 操作名称
     * @throws AdminServiceException
     */
    public function create($user, $name): void
    {
        Db::startTrans();
        try {
            $data = [
                'admin_user_id' => $user->id,
                'name'          => $name,
                'log_method'    => request()->method(),
                'url'           => request()->pathinfo(),
                'log_ip'        => request()->ip()
            ];
            $log  = $this->model::create($data);

            $data_arr = [
                'header' => request()->header(),
                'param'  => request()->param(),
            ];

            $log_data = [
                'data' => json_encode($data_arr, JSON_THROW_ON_ERROR),
            ];
            $log->adminLogData()->save($log_data);

            Db::commit();

        } catch (Exception $exception) {
            Db::rollback();
            throw new AdminServiceException($exception->getMessage());
        }
    }
}