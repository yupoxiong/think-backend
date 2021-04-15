<?php

use Phinx\Db\Adapter\MysqlAdapter;
use think\migration\Migrator;
use think\migration\db\Column;

class AdminLogData extends Migrator
{
    public function change()
    {
        $table = $this->table('admin_log_data', ['comment' => '后台操作日志数据', 'engine' => 'InnoDB', 'encoding' => 'utf8mb4', 'collation' => 'utf8mb4_unicode_ci']);
        $table
            ->addColumn('admin_log_id', 'integer', ['signed' => false, 'limit' => 10, 'comment' => '日志ID'])
            ->addColumn('data', 'text', ['limit' => MysqlAdapter::TEXT_LONG, 'comment' => '日志内容'])
            ->create();

    }
}
