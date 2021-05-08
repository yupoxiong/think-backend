<?php
/**
 * 业务处理类
 */

/**
 * 用于检测业务代码死循环或者长时间阻塞等问题
 * 如果发现业务卡死，可以将下面declare打开（去掉//注释），并执行php start.php reload
 * 然后观察一段时间workerman.log看是否有process_timeout异常
 */
//declare(ticks=1);

namespace app\workerman\websocket;

use app\api\exception\ApiServiceException;
use app\api\service\TokenService;
use Exception;
use GatewayWorker\BusinessWorker;
use \GatewayWorker\Lib\Gateway;
use Workerman\Lib\Timer;

/**
 * 主逻辑
 * 主要是处理 onConnect onMessage onClose 三个方法
 * onConnect 和 onClose 如果不需要可以不用实现并删除
 */
class WebSocketEvents
{
    /**
     * 当客户端连接时触发
     * 如果业务不需此回调可以删除onConnect
     *
     * @param int $client_id 连接id
     * @throws Exception
     */
    public static function onConnect($client_id): void
    {
        // 连接到来后，定时30秒关闭这个链接，需要30秒内发认证并删除定时器阻止关闭连接的执行
        $auth_timer_id = Timer::add(10, function ($client_id) {
            Gateway::closeClient($client_id);
        }, array($client_id), false);
        Gateway::updateSession($client_id, array('auth_timer_id' => $auth_timer_id));

        $msg = '{"type":"connect","client_id":"' . $client_id . '"}';
        // 告诉客户端clientID
        Gateway::sendToClient($client_id, $msg);
    }

    /**
     * 当客户端发来消息时触发
     * @param int $client_id 连接id
     * @param mixed $message 具体消息
     * @throws \JsonException
     */
    public static function onMessage($client_id, $message): void
    {

        $_SESSION = Gateway::getSession($client_id);

        if ($message === 'd') {
            Gateway::sendToClient($client_id, 'd');
        } else {
            $msg = json_decode($message, true, 512, JSON_THROW_ON_ERROR);

            switch ($msg['type']) {
                case 'login':
                    $token  = $msg['token'];
                    $result = false;

                    try {
                        $result = (new TokenService())->checkToken($token);
                    } catch (ApiServiceException $e) {
                        $send_msg = [
                            'type' => 'auth',
                            'data' => 'auth failed',
                        ];
                        Gateway::sendToClient($client_id, json_encode($send_msg, JSON_THROW_ON_ERROR));
                    }

                    if ($result) {
                        $send_msg = [
                            'type' => 'auth',
                            'data' => 'auth success',
                        ];
                        Gateway::sendToClient($client_id, json_encode($send_msg, JSON_THROW_ON_ERROR));
                        Timer::del($_SESSION['auth_timer_id']);
                    }

                    break;
            }
        }

    }

    /**
     * 当用户断开连接时触发
     * @param int $client_id 连接id
     * @throws Exception
     */
    public static function onClose($client_id): void
    {

    }

    /**
     * @param BusinessWorker $businessWorker
     * 当businessWorker启动时
     */
    public static function onWorkerStart(BusinessWorker $businessWorker)
    {

    }
}
