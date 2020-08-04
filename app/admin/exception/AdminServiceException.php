<?php
/**
 *
 * @author yupoxiong<i@yupoxiong.com>
 */

declare (strict_types=1);


namespace app\admin\exception;


use Exception;
use think\response\Json;
use Throwable;

class AdminServiceException extends Exception
{
    protected $content;

    /**
     * V3Exceptions constructor.
     *
     * @param string         $message
     * @param array          $content
     * @param int            $code
     * @param Throwable|null $previous

     */
    public function __construct($message = '', $code = 400, $content = null, Throwable $previous = null)
    {
        $this->content = $content;

        parent::__construct($message, $code, $previous);
    }

    /**
     * 异常返回统一接口
     *
     * @return Json
     */
    public function generateJSONResponse(): Json
    {
        return admin_result( $this->getMessage(),$this->content, $this->getCode());
    }
}