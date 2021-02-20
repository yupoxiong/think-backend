<?php
/**
 * api模块公共函数
 * @author yupoxiong<i@yupoxiong.com>
 */

use think\response\Json;

if (!function_exists('api_unauthorized')) {
    /**
     * 未授权
     * @param string $msg
     * @param array $data
     * @param int $code
     * @return Json
     */
    function api_unauthorized($msg = 'unauthorized', $data = [], $code = 401): Json
    {
        return api_result($msg, $data, $code);
    }
}


if (!function_exists('api_success')) {
    /**
     * 操作成功
     * @param array $data
     * @param string $msg
     * @param int $code
     * @return Json
     */
    function api_success($data = [], $msg = 'success', $code = 200): Json
    {
        return api_result($msg, $data, $code);
    }
}

if (!function_exists('api_error')) {
    /**
     * 操作失败
     * @param string $msg
     * @param array $data
     * @param int $code
     * @return Json
     */
    function api_error($msg = 'fail', $data = [], $code = 500): Json
    {
        return api_result($msg, $data, $code);
    }
}

if (!function_exists('api_result')) {
    /**
     * 返回json结果
     * @param string $msg
     * @param mixed $data
     * @param int $code
     * @return Json
     */
    function api_result($msg = 'fail', $data = [], $code = 500): Json
    {
        if (is_array($data) && empty($data)) {
            $data = (object)$data;
        }
        $header = [];
        //处理跨域请求问题
        if (config('api.cross_domain.allow')) {
            $header = ['Access-Control-Allow-Origin' => '*'];
            if (request()->isOptions()) {
                $header = config('api.cross_domain.header');
                return json('', 200, $header);
            }
        }

        return json([
            'code' => $code,
            'msg'  => $msg,
            'data' => $data,
        ], $code, $header);
    }
}


if (!function_exists('api_service_unavailable')) {
    /**
     * 系统维护中
     * @param string $msg
     * @param array $data
     * @param int $code
     * @return Json
     */
    function api_service_unavailable($msg = 'service unavailable', $data = [], $code = 503): Json
    {
        return api_result($msg, $data, $code);
    }
}


if (!function_exists('api_error_client')) {
    /**
     * 客户端错误
     * @param string $msg
     * @param array $data
     * @param int $code
     * @return Json
     */
    function api_error_client($msg = 'client error', $data = [], $code = 400): Json
    {
        return api_result($msg, $data, $code);
    }
}

if (!function_exists('api_error_server')) {
    /**
     * 服务端错误
     * @param string $msg
     * @param array $data
     * @param int $code
     * @return Json
     */
    function api_error_server($msg = 'server error', $data = [], $code = 500): Json
    {
        return api_result($msg, $data, $code);
    }
}

if (!function_exists('api_error_404')) {
    /**
     * 资源或接口不存在
     * @param string $msg
     * @param array $data
     * @param int $code
     * @return Json
     */
    function api_error_404($msg = '404 not found', $data = [], $code = 404): Json
    {
        return api_result($msg, $data, $code);
    }
}
