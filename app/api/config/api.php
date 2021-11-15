<?php
/**
 * api模块相关配置
 * @author yupoxiong<i@yupoxiong.com>
 */

return [

    // api跨域设置
    'cross_domain' => [
        // 是否允许跨域
        'allow'  => env('api.allow_cross_domain', true),
        // header设置
        'header' => [
            'Access-Control-Allow-Origin'    => '*',
            'Access-Control-Allow-Methods'   => '*',
            'Access-Control-Allow-Headers'   => 'content-type,token',
            'Access-Control-Request-Headers' => 'Origin, content-Type, Accept, token',
        ],
    ],
    // api响应配置
    'response'     => [
        // HTTP状态码和业务状态码同步
        'http_code_sync' => env('api.http_code_sync', false),
    ],
    'auth'=>[

    ],
];
