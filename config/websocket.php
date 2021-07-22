<?php
/**
 * websocket配置
 * @author yupoxiong<i@yupoxiong.com>
 */

declare (strict_types=1);

return [
    // Register配置
    'register' => [
        'ip'   => '127.0.0.1',
        'port' => '1238',
    ],
    // BusinessWorker配置
    'worker'   => [
        'name'          => 'WebSocketBusinessWorker',
        'count'         => 1,
        'event_handler' => '\app\workerman\websocket\WebSocketEvents',
    ],
    // Gateway配置
    'gateway'  => [
        // 端口
        'port'                    => '8282',
        'start_port'              => 2900,
        'name'                    => 'WebSocketGateway',
        'count'                   => 2,
        'lan_ip'                  => '127.0.0.1',
        'ping_data'               => '1',
        'ping_interval'           => 30,
        'ping_not_response_limit' => 1,
    ],


];