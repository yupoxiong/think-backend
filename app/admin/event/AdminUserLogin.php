<?php
/**
 *
 * @author yupoxiong<i@yupoxiong.com>
 */

declare (strict_types=1);


namespace app\admin\event;


class AdminUserLogin
{

    public function __construct(User $user)
    {
        $this->user = $user;
    }
}