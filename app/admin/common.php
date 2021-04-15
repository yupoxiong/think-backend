<?php
/**
 * 后台公共函数文件
 * @author yupoxiong<i@yupoxiong.com>
 */

use think\response\Json;

/** 不做任何操作 */
const URL_CURRENT = 'url://current';
/** 刷新页面 */
const URL_RELOAD = 'url://reload';
/** 返回上一个页面 */
const URL_BACK = 'url://back';
/** 关闭当前layer弹窗 */
const URL_CLOSE_LAYER = 'url://close_layer';
/** 关闭当前弹窗并刷新父级 */
const URL_CLOSE_REFRESH = 'url://close_refresh';

if (!function_exists('admin_success')) {

    /**
     * 后台返回成功
     * @param string $msg
     * @param array $result
     * @param int $code
     * @param string $url
     * @param array $header
     * @param array $options
     * @return Json
     */
    function admin_success($msg = '操作成功', $result = [], $url = URL_CURRENT, $code = 200, array $header = [], $options = []): Json
    {
        return admin_result($msg, $result, $url, $code, $header, $options);
    }
}


if (!function_exists('admin_error')) {
    /**
     * 后台返回错误
     * @param string $msg
     * @param array $result
     * @param string $url
     * @param int $code
     * @param array $header
     * @param array $options
     * @return Json
     */
    function admin_error($msg = '操作失败', $result = [], $url = URL_CURRENT, $code = 500, array $header = [], $options = []): Json
    {
        return admin_result($msg, $result, $url, $code, $header, $options);
    }
}

if (!function_exists('admin_result')) {


    /**
     * 后台返回结果
     * @param array $result
     * @param string $msg
     * @param string $url
     * @param int $code
     * @param array $header
     * @param array $options
     * @return Json
     */
    function admin_result($msg = '', $result = [], $url = URL_CURRENT, $code = 500, $header = [], $options = []): Json
    {

        $data = [
            'msg'    => $msg,
            'code'   => $code,
            'result' => empty($result) ? (object)$result : $result,
            'url'    => $url,
        ];

        return json($data, 200, $header, $options);

    }
}
