<?php
/**
 *
 * @author yupoxiong<i@yupoxiong.com>
 */

declare (strict_types=1);


namespace app\admin\validate;


class AdminRoleValidate extends AdminBaseValidate
{
    protected $rule = [
        'name|名称'        => 'require|unique:admin_role',
        'description|介绍' => 'require',
        'rules|权限'       => 'require',
    ];

    protected $scene = [
        'add'  => ['name', 'description'],
        'edit' => ['name', 'description'],
    ];
}