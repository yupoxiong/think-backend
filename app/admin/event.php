<?php

use app\admin\listener\AdminUserLogin;
use app\admin\listener\AdminUserLogout;

/**
 *
 * @author yupoxiong<i@yupoxiong.com>
 */

// AdminUserLogin

return [
    'bind' => [

    ],

    'listen' => [
        'AdminUserLogin'  => [
            AdminUserLogin::class,
        ],
        'AdminUserLogout' => [
            AdminUserLogout::class,
        ],
        'AppInit'         => [],
        'HttpRun'         => [],
        'HttpEnd'         => [],
        'LogLevel'        => [],
        'LogWrite'        => [],
    ],

    'subscribe' => [
    ],
];

