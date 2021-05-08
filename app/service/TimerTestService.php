<?php
/**
 *
 * @author yupoxiong<i@yupoxiong.com>
 */

declare (strict_types=1);


namespace app\service;


use think\facade\Log;

class TimerTestService
{

    public function test01()
    {
        Log::record('[01]'.date('Y-m-d H:i:s'));
    }

    public function test1()
    {
        Log::record(date('[1]'.'Y-m-d H:i:s'));
    }


    public function test5()
    {
        Log::record(date('[5]'.'Y-m-d H:i:s'));
    }

    public function test10()
    {
        Log::record(date('[10]'.'Y-m-d H:i:s'));
    }
}