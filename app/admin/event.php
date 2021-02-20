<?php

use app\admin\listener\AdminUserLogin;

/**
 *
 * @author yupoxiong<i@yupoxiong.com>
 */

// AdminUserLogin

return [
    'bind'      => [

    ],

    'listen'    => [
        'AdminUserLogin' => [
            AdminUserLogin::class,
        ],
        'AppInit'  => [],
        'HttpRun'  => [],
        'HttpEnd'  => [],
        'LogLevel' => [],
        'LogWrite' => [],
    ],

    'subscribe' => [
    ],
];

