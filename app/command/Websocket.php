<?php
declare (strict_types=1);

namespace app\command;

use GatewayWorker\BusinessWorker;
use GatewayWorker\Gateway;
use GatewayWorker\Register;
use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;
use Workerman\Connection\TcpConnection;
use Workerman\Lib\Timer;
use Workerman\Worker;

class Websocket extends Command
{
    protected function configure()
    {

        // 指令配置
        $this->setName('websocket')
            ->addArgument('action', Argument::OPTIONAL, "start|stop|restart|reload|status|connections", 'start')
            ->addOption('-d', 'd', Option::VALUE_NONE, '以守护进程的方式运行')
            ->setDescription('基于workerman的WebSocket服务');
    }

    protected function execute(Input $input, Output $output)
    {

        $action = $input->getArgument('action');
        $mode = $input->getOption('-d');

        // 重新构造命令行参数,以便兼容workerman的命令
        global $argv;

        $argv = [];

        array_unshift($argv, 'think', $action);
        if ($mode) {
            $argv[] = '-d';
        }

        $this->startServer();

    }


    /**
     * 启动workerman服务
     */
    public function startServer(): void
    {
        $register_port = '1238';
        $gateway_port = '8282';

        // register 必须是text协议
        $register = new Register('text://0.0.0.0:'.$register_port);

        // businessWorker 进程
        $worker = new BusinessWorker();
        // worker名称
        $worker->name = 'WebSocketBusinessWorker';
        // businessWorker进程数量
        $worker->count = 1;
        // 服务注册地址
        $worker->registerAddress = '127.0.0.1:'.$register_port;
        //设置处理业务的类,此处制定Events的命名空间
        $worker->eventHandler = '\app\workerman\websocket\WebSocketEvents';

        //=============================================//

        // gateway 进程，这里使用Text协议，可以用telnet测试
        $gateway = new Gateway('websocket://0.0.0.0:'.$gateway_port);
        // gateway名称，status方便查看
        $gateway->name = 'WebSocketGateway';
        // gateway进程数
        $gateway->count = 2;
        // 本机ip，分布式部署时使用内网ip
        $gateway->lanIp = '127.0.0.1';
        // 内部通讯起始端口，假如$gateway->count=4，起始端口为4000
        // 则一般会使用4000 4001 4002 4003 4个端口作为内部通讯端口
        $gateway->startPort = 2900;
        // 服务注册地址
        $gateway->registerAddress = '127.0.0.1:'.$register_port;
        // 从客户端发送心跳检测
        $gateway->pingNotResponseLimit = 0;
        // 心跳间隔
        $gateway->pingInterval = 0;
        // 心跳数据
        $gateway->pingData = '{"type":"ping"}';

        Worker::runAll();
    }
}
