<?php
/**
 * 用户验证器
 */

namespace app\common\validate;

class UserValidate extends CommonBaseValidate
{
    protected $rule = [
            'user_level_id|用户等级' => 'require',
    'username|账号' => 'require',
    'password|密码' => 'require',
    'mobile|手机号' => 'require',
    'nickname|昵称' => 'require',
    'status|是否启用' => 'require',

    ];

    protected $message = [
            'user_level_id.require' => '用户等级不能为空',
    'username.require' => '账号不能为空',
    'password.require' => '密码不能为空',
    'mobile.require' => '手机号不能为空',
    'nickname.require' => '昵称不能为空',
    'status.require' => '是否启用不能为空',

    ];

    protected $scene = [
        'admin_add'   => ['user_level_id','username','password','mobile','nickname','status',],
        'admin_edit'  => ['user_level_id','username','password','mobile','nickname','status',],
        'api_add'     => ['user_level_id','username','password','mobile','nickname','status',],
        'api_info'    => ['user_level_id','username','password','mobile','nickname','status',],
        'api_edit'    => ['user_level_id','username','password','mobile','nickname','status',],
        'api_del'     => ['user_level_id','username','password','mobile','nickname','status',],
        'api_disable' => ['user_level_id','username','password','mobile','nickname','status',],
        'api_enable'  => ['user_level_id','username','password','mobile','nickname','status',],
    ];

    

}
