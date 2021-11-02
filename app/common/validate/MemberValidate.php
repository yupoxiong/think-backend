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
    'avatar|图片' => 'require',
    'status|是否启用' => 'require',

    ];

    protected $message = [
            'member_level_id.required' => '会员等级不能为空',
    'username.required' => '账号不能为空',
    'password.required' => '密码不能为空',
    'mobile.required' => '手机号不能为空',
    'nickname.required' => '昵称不能为空',
    'avatar.required' => '图片不能为空',
    'status.required' => '是否启用不能为空',

    ];

    protected $scene = [
        'admin_add'     => ['member_level_id', 'username', 'password', 'mobile', 'nickname', 'avatar', 'status', ],
        'admin_edit'    => ['id', 'member_level_id', 'username', 'password', 'mobile', 'nickname', 'avatar', 'status', ],
        'admin_del'     => ['id', ],
        'admin_disable' => ['id', ],
        'admin_enable'  => ['id', ],
        'api_add'       => ['member_level_id', 'username', 'password', 'mobile', 'nickname', 'avatar', 'status', ],
        'api_info'      => ['id', ],
        'api_edit'      => ['id', 'member_level_id', 'username', 'password', 'mobile', 'nickname', 'avatar', 'status', ],
        'api_del'       => ['id', ],
        'api_disable'   => ['id', ],
        'api_enable'    => ['id', ],
    ];

}
