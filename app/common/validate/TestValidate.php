<?php
/**
 * 测试验证器
 */

namespace app\common\validate;

class TestValidate extends CommonBaseValidate
{
    protected $rule = [
        'avatar|头像'          => 'require',
        'username|用户名'       => 'require',
        'nickname|昵称'        => 'require',
        'mobile|手机号'         => 'require|mobile',
        'user_level_id|用户等级' => 'require',
        'password|密码'        => 'require',
        'status|是否启用'        => 'require',
        'lng|经度'             => 'require',
        'lat|纬度'             => 'require',
        'slide|相册'           => 'require',

    ];

    protected $message = [
        'avatar.require'        => '头像不能为空',
        'username.require'      => '用户名不能为空',
        'nickname.require'      => '昵称不能为空',
        'mobile.require'        => '手机号不能为空',
        'mobile.mobile'         => '手机号格式不正确',
        'user_level_id.require' => '用户等级不能为空',
        'password.require'      => '密码不能为空',
        'status.require'        => '是否启用不能为空',
        'lng.require'           => '经度不能为空',
        'lat.require'           => '纬度不能为空',
        'slide.require'         => '相册不能为空',

    ];

    protected $scene = [
        'admin_add'     => ['avatar', 'username', 'nickname', 'mobile', 'user_level_id', 'password', 'status', 'lng', 'lat', 'slide',],
        'admin_edit'    => ['id', 'avatar', 'username', 'nickname', 'mobile', 'user_level_id', 'password', 'status', 'lng', 'lat', 'slide',],
        'admin_del'     => ['id',],
        'admin_disable' => ['id',],
        'admin_enable'  => ['id',],
        'api_add'       => ['avatar', 'username', 'nickname', 'mobile', 'user_level_id', 'password', 'status', 'lng', 'lat', 'slide',],
        'api_info'      => ['id',],
        'api_edit'      => ['id', 'avatar', 'username', 'nickname', 'mobile', 'user_level_id', 'password', 'status', 'lng', 'lat', 'slide',],
        'api_del'       => ['id',],
        'api_disable'   => ['id',],
        'api_enable'    => ['id',],
    ];


}
