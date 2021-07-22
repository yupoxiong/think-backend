<?php
/**
 *
 * @author yupoxiong<i@yupoxiong.com>
 */

declare (strict_types=1);


namespace app\workerman\timer;


use Workerman\Lib\Timer;

class WorkermanTimer
{

    /**
     * 服务进程启动时
     * @param $businessWorker
     */
    public static function onWorkerStart($businessWorker)
    {
        $worker_id = $businessWorker->id;
        // 获取所有定时器任务配置.
        $timedTask = config('timer.task');

        if (is_array($timedTask)) {
            // 循环检测任务绑定.
            foreach ($timedTask as $key => $value) {
                // 绑定任务进程.
                if ($value['worker_id'] === $worker_id) {
                    Timer::add($value['time'], $value['func']);
                }
            }
        }

    }

    /**
     * 服务进程结束时
     * @param $client_id
     */
    public static function onClose($client_id)
    {

    }

}