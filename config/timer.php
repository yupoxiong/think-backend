<?php
/**
 *
 * @author yupoxiong<i@yupoxiong.com>
 */

return [

    'task'         => [
        // 定时任务名称.
        'test01' => [
            'worker_id' => 0, // 需要绑定的进程id.
            'time'      => 0.1, // 时间间隔 秒为单位.
            'func'      => 'app\facade\TimerTestService::test01', // 定时执行的方法.
        ],

        // 定时任务名称.
        'test1'  => [
            'worker_id' => 0, // 需要绑定的进程id.
            'time'      => 1, // 时间间隔 秒为单位.
            'func'      => 'app\facade\TimerTestService::test1', // 定时执行的方法.
        ],

        // 定时任务名称.
        'test5'  => [
            'worker_id' => 0, // 需要绑定的进程id.
            'time'      => 5, // 时间间隔 秒为单位.
            'func'      => 'app\facade\TimerTestService::test5', // 定时执行的方法.
        ],

        // 定时任务名称.
        'test10' => [
            'worker_id' => 0, // 需要绑定的进程id.
            'time'      => 10, // 时间间隔 秒为单位.
            'func'      => 'app\facade\TimerTestService::test10', // 定时执行的方法.
        ],
    ],
    // 启动定时任务的进程数量
    'worker_count' => 1,

];