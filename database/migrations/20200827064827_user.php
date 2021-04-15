<?php

use think\migration\Migrator;
use think\migration\db\Column;

class User extends Migrator
{

    public function change()
    {
        $table = $this->table('user', ['comment' => '用户', 'engine' => 'InnoDB', 'encoding' => 'utf8mb4', 'collation' => 'utf8mb4_unicode_ci']);
        $table
            ->addColumn('user_level_id', 'integer', ['signed' => false, 'limit' => 10, 'default' => 0, 'comment' => '用户等级'])
            ->addColumn('username', 'string', ['limit' => 30, 'default' => '', 'comment' => '账号'])
            ->addColumn('password', 'string', ['limit' => 255, 'default' => '', 'comment' => '密码'])
            ->addColumn('mobile', 'string', ['limit' => 11, 'default' => '', 'comment' => '手机号'])
            ->addColumn('nickname', 'string', ['limit' => 20, 'default' => '', 'comment' => '昵称'])
            ->addColumn('avatar', 'string', ['limit' => 255, 'default' => '/static/index/images/avatar.png', 'comment' => '头像'])
            ->addColumn('status', 'boolean', ['signed' => false, 'limit' => 1, 'default' => 1, 'comment' => '是否启用'])
            ->addColumn('create_time', 'integer', ['signed' => false, 'limit' => 10, 'default' => 0, 'comment' => '创建时间'])
            ->addColumn('update_time', 'integer', ['signed' => false, 'limit' => 10, 'default' => 0, 'comment' => '更新时间'])
            ->addColumn('delete_time', 'integer', ['signed' => false, 'limit' => 10, 'default' => 0, 'comment' => '删除时间'])
            ->create();
    }
}
