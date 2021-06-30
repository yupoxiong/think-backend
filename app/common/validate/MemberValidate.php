<?php
/**
 * 会员验证器
 */

namespace app\common\validate;

class MemberValidate extends CommonBaseValidate
{
    protected $rule = [
            'member_level_id|会员等级' => 'require',
    'username|账号' => 'require',
    'password|密码' => 'require',
    'mobile|手机号' => 'require',
    'nickname|昵称' => 'require',
    'status|是否启用' => 'require',

    ];

    protected $message = [
            'member_level_id.require' => '会员等级不能为空',
    'username.require' => '账号不能为空',
    'password.require' => '密码不能为空',
    'mobile.require' => '手机号不能为空',
    'nickname.require' => '昵称不能为空',
    'status.require' => '是否启用不能为空',

    ];

    protected $scene = [
        'admin_add'   => ['member_level_id','username','password','mobile','nickname','status',],
        'admin_edit'  => ['member_level_id','username','password','mobile','nickname','status',],
        'api_add'     => ['member_level_id','username','password','mobile','nickname','status',],
        'api_info'    => ['member_level_id','username','password','mobile','nickname','status',],
        'api_edit'    => ['member_level_id','username','password','mobile','nickname','status',],
        'api_del'     => ['member_level_id','username','password','mobile','nickname','status',],
        'api_disable' => ['member_level_id','username','password','mobile','nickname','status',],
        'api_enable'  => ['member_level_id','username','password','mobile','nickname','status',],
    ];

    

}
