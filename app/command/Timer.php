<?php
/**
 * 定时器命令行
 */
declare (strict_types = 1);

namespace app\command;

use app\workerman\timer\WorkermanTimer;
use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;
use Workerman\Worker;

class Timer extends Command
{
    protected function configure()
    {
        // 指令配置
        $this->setName('timer')
            ->addArgument('action', Argument::OPTIONAL, "start|stop|restart|reload|status|connections", 'start')
            ->addOption('-d', 'd', Option::VALUE_NONE, '以守护进程的方式运行')
            ->setDescription('基于Workerman的定时任务');
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
        $worker = new Worker();
        // 服务名称.
        $worker->name = 'TimerWorkerman';
        // 启动多少个进程数量，这里大家灵活配置，可以参考workerman的文档.
        $worker->count = config('timer.worker_count');
        // 当workerman的进程启动时的回调方法.
        $worker->onWorkerStart = [WorkermanTimer::class, 'onWorkerStart'];
        // 当workerman的进程关闭时的回调方法.
        $worker->onClose = [WorkermanTimer::class, 'onClose'];
        Worker::runAll();
    }
}
