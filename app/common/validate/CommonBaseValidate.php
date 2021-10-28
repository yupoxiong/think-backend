<?php
/**
 *
 * @author yupoxiong<i@yupoxiong.com>
 */

declare (strict_types=1);


namespace app\common\validate;


use think\facade\Log;
use think\Validate;

class CommonBaseValidate extends Validate
{

    /**
     * 验证16进制颜色
     * @param $value
     * @param $rule
     * @param array $data
     * @param string $field
     * @param string $desc
     * @return bool|string
     */
    protected function color16($value, $rule, $data = [], $field = '', $desc = '')
    {
        $pattern = '/^#?([a-fA-F0-9]{6}|[a-fA-F0-9]{3})$/';
        return preg_match($pattern, $value) ? true : $desc.'格式不正确';

    }
}