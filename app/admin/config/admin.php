<?php

/**
* 后台设置:后台管理方面的设置
* 此配置文件为自动生成，生成时间2021-07-08 10:17:15
*/

return [
    //基本设置:后台的基本信息设置
    'base'=>[
    //后台名称
    'name'=>'XX后台系统',
    //后台简称
    'short_name'=>'后台1',
    //后台作者
    'author'=>'xx科技',
    //后台版本
    'version'=>'0.1',
],
    //登录设置:后台登录相关设置
    'login'=>[
    //登录token验证
    'token'=>'0',
    //验证码
    'captcha'=>'0',
    //登录背景
    'background'=>'/static/admin/images/login-default-bg.jpg',
    //极验ID
    'geetest_id'=>'66cfc0f309e368364b753dad7d2f67f2',
    //极验KEY
    'geetest_key'=>'99750f86ec232c997efaff56c7b30cd3',
],
    //首页设置:后台首页参数设置
    'index'=>[
    //默认密码警告
    'password_warning'=>'1',
    //是否显示提示信息
    'show_notice'=>'1',
    //提示信息内容
    'notice_content'=>'欢迎来到使用本系统，左侧为菜单区域，右侧为功能区。',
],
    //安全设置:安全相关配置
    'safe'=>[
    //加密key
    'admin_key'=>'89ce3272dc949fc3698fe7108d1dbe37',
    //SessionKeyUid
    'store_uid_key'=>'admin_user_id',
    //SessionKeySign
    'store_sign_key'=>'admin_user_sign',
],
];