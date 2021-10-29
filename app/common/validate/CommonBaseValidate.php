<?php
/**
 *
 * @author yupoxiong<i@yupoxiong.com>
 */

declare (strict_types=1);


namespace app\common\validate;


use generate\validate\Color16;
use generate\validate\ComplexPassword;
use generate\validate\MiddlePassword;
use generate\validate\Number6;
use generate\validate\SimplePassword;
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
        return preg_match($pattern, $value) ? true : $desc.(new Color16())->getMsg();

    }


    /**
     * 验证6位数字密码
     * @param $value
     * @param $rule
     * @param array $data
     * @param string $field
     * @param string $desc
     * @return bool|string
     */
    protected function number6($value, $rule, $data = [], $field = '', $desc = '')
    {
        $pattern = '/^\d{6}$/';
        return preg_match($pattern, $value) ? true : $desc.(new Number6())->getMsg();

    }

    /**
     * 验证简单密码
     * @param $value
     * @param $rule
     * @param array $data
     * @param string $field
     * @param string $desc
     * @return bool|string
     */
    protected function simplePassword($value, $rule, $data = [], $field = '', $desc = '')
    {
        $pattern = '/^(?=.*[a-zA-Z])(?=.*\d).{6,16}$/';
        return preg_match($pattern, $value) ? true : $desc.(new SimplePassword())->getMsg();

    }


    /**
     * 验证简单密码
     * @param $value
     * @param $rule
     * @param array $data
     * @param string $field
     * @param string $desc
     * @return bool|string
     */
    protected function middlePassword($value, $rule, $data = [], $field = '', $desc = '')
    {
        $pattern = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,16}$/';
        return preg_match($pattern, $value) ? true : $desc.(new MiddlePassword())->getMsg();

    }

    /**
     * 验证简单密码
     * @param $value
     * @param $rule
     * @param array $data
     * @param string $field
     * @param string $desc
     * @return bool|string
     */
    protected function complexPassword($value, $rule, $data = [], $field = '', $desc = '')
    {
        $pattern = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.[$@!%#?&]).{8,16}$/';
        return preg_match($pattern, $value) ? true : $desc.(new ComplexPassword())->getMsg();

    }




}