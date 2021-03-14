<?php

use think\migration\Migrator;
use think\migration\db\Column;

class Member extends Migrator
{

    public function change()
    {
        $table = $this->table('member', ['comment' => '会员', 'engine' => 'InnoDB', 'encoding' => 'utf8mb4', 'collation' => 'utf8mb4_unicode_ci']);
        $table
            ->addColumn('member_level_id', 'integer', ['limit' => 11, 'default' => 0, 'comment' => '会员等级'])
            ->addColumn('username', 'string', ['limit' => 30, 'default' => '', 'comment' => '账号'])
            ->addColumn('password', 'string', ['limit' => 255, 'default' => '', 'comment' => '密码'])
            ->addColumn('mobile', 'string', ['limit' => 11, 'default' => '', 'comment' => '手机号'])
            ->addColumn('nickname', 'string', ['limit' => 20, 'default' => '', 'comment' => '昵称'])
            ->addColumn('avatar', 'string', ['limit' => 255, 'default' => '/static/index/images/user_level_default.png', 'comment' => '图片'])
            ->addColumn('status', 'boolean', ['limit' => 1, 'default' => 1, 'comment' => '是否启用'])
            ->addColumn('create_time', 'integer', ['limit' => 10, 'default' => 0, 'comment' => '创建时间'])
            ->addColumn('update_time', 'integer', ['limit' => 10, 'default' => 0, 'comment' => '更新时间'])
            ->addColumn('delete_time', 'integer', ['limit' => 10, 'default' => 0, 'comment' => '删除时间'])
            ->create();
    }
}
