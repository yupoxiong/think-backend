<?php
/**
 * websocket命令行
 */
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
        $mode   = $input->getOption('-d');

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
        $config           = config('websocket');
        $register_port    = $config['register']['port'];
        $gateway_port     = $config['gateway']['port'];
        $register_address = $config['register']['ip'] . ':' . $register_port;

        // register 必须是text协议
        $register = new Register('text://0.0.0.0:' . $register_port);

        // businessWorker 进程
        $worker = new BusinessWorker();
        // worker名称
        $worker->name = $config['worker']['name'];
        // businessWorker进程数量
        $worker->count = $config['worker']['count'];
        // 服务注册地址
        $worker->registerAddress = $register_address;
        //设置处理业务的类,此处制定Events的命名空间
        $worker->eventHandler = $config['worker']['event_handler'];

        // gateway 进程，这里使用Text协议，可以用telnet测试
        $gateway = new Gateway('websocket://0.0.0.0:' . $gateway_port);
        // gateway名称，status方便查看
        $gateway->name = $config['gateway']['name'];
        // gateway进程数
        $gateway->count = $config['gateway']['count'];
        // 本机ip，分布式部署时使用内网ip
        $gateway->lanIp = $config['gateway']['lan_ip'];
        // 内部通讯起始端口，假如$gateway->count=4，起始端口为4000
        // 则一般会使用4000 4001 4002 4003 4个端口作为内部通讯端口
        $gateway->startPort = $config['gateway']['start_port'];
        // 服务注册地址
        $gateway->registerAddress = $register_address;
        // 从客户端发送心跳检测
        $gateway->pingNotResponseLimit = $config['gateway']['ping_not_response_limit'];
        // 心跳间隔
        $gateway->pingInterval = $config['gateway']['ping_interval'];
        // 心跳数据
        $gateway->pingData = $config['gateway']['ping_data'];

        Worker::runAll();
    }
}
