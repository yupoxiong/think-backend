<?php
/**
 * 后台配置
 * @author yupoxiong<i@yupoxiong.com>
 */

declare (strict_types=1);


return [
    // 后台加密解密使用的key，可自行修改
    'admin_key'      => '89ce3272dc949fc3698fe7108d1dbe37',
    // 登录后session和cookie储存的用户key，可自行修改
    'store_uid_key'  => 'admin_user_id',
    // 登录后session和cookie储存的签名key，可自行修改
    'store_sign_key' => 'admin_user_sign',
];