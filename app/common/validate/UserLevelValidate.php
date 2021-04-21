<?php
/**
 * 用户等级验证器
 */

namespace app\common\validate;

class UserLevelValidate extends CommonBaseValidate
{
    protected $rule = [
            'name|名称' => 'require',
    'description|简介' => 'require',
    'status|是否启用' => 'require',

    ];

    protected $message = [
            'name.require' => '名称不能为空',
    'description.require' => '简介不能为空',
    'status.require' => '是否启用不能为空',

    ];

    protected $scene = [
        'admin_add'   => ['name','description','status',],
        'admin_edit'  => ['name','description','status',],
        'api_add'     => ['name','description','status',],
        'api_info'    => ['name','description','status',],
        'api_edit'    => ['name','description','status',],
        'api_del'     => ['name','description','status',],
        'api_disable' => ['name','description','status',],
        'api_enable'  => ['name','description','status',],
    ];

    

}
