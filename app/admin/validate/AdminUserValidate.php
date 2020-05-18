<?php
/**
 *
 * @author yupoxiong<i@yupoxiong.com>
 */

declare (strict_types=1);


namespace app\admin\validate;


class AdminUserValidate extends AdminBaseValidate
{

    protected $rule = [
        'username|帐号'       => 'require|unique:admin_user',
        'password|密码'       => 'require',
        'new_password|新密码'  => 'require',
        're_password|确认新密码' => 'require|confirm:new_password',
        'nickname|昵称'       => 'require',
        'role|角色'           => 'require',
        'status|状态'         => 'require',
    ];
}