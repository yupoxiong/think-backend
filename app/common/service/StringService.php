<?php
/**
 *
 * @author yupoxiong<i@yupoxiong.com>
 */

declare (strict_types=1);


namespace app\common\service;


use app\common\exception\CommonServiceException;
use Exception;

class StringService extends CommonBaseService
{

    public const STR_NUMBER = '0123456789';
    public const STR_LOWER_CASE = 'abcdefghijklmnopqrstuvwxyz';
    public const STR_CAPITAL = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    public const STR_PUNCTUATION = '~!@#$%^&*()_+{}|:"<>?`-=[]\;,./';

    /**
     * @param int $length
     * @param bool $number
     * @param bool $lower_case
     * @param bool $capital
     * @param bool $punctuation
     * @return string
     * @throws CommonServiceException
     */
    public static function getRandString($length = 10, $number = true, $lower_case = true, $capital = true, $punctuation = true): string
    {
        $str = '';

        if ($number) {
            $str .= self::STR_NUMBER;
        }
        if ($lower_case) {
            $str .= self::STR_LOWER_CASE;
        }
        if ($capital) {
            $str .= self::STR_CAPITAL;
        }
        if ($punctuation) {
            $str .= self::STR_PUNCTUATION;
        }

        if ($str === '') {
            throw new CommonServiceException('请至少选择一种字符串');
        }

        $max    = strlen($str) - 1;
        $result = '';
        for ($i = 1; $i <= $length; $i++) {
            try {
                $rand = random_int(0, $max);
            } catch (Exception $e) {
                throw new CommonServiceException('rand_int函数执行错误，参考错误信息:' . $e->getMessage());
            }
            $result .= $str[$rand];
        }

        return $result;
    }

}