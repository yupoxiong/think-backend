<?php
/**
 *
 * @author yupoxiong<i@yupoxiong.com>
 */

declare (strict_types=1);


namespace app\facade;

use think\Facade;

class TimerTestService extends Facade
{
    protected static function getFacadeClass()
    {
        return 'app\service\TimerTestService';
    }
}
