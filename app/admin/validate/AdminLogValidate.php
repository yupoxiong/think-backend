<?php
/**
 *
 * @author yupoxiong<i@yupoxiong.com>
 */

declare (strict_types=1);


namespace app\admin\validate;


class AdminLogValidate extends AdminBaseValidate
{

    protected $rule = [
        'name|名称'       => 'require',
        'status|状态'         => 'require',
    ];



    protected $scene = [
        'create' => ['username', 'password'],
    ];

    public function sceneLogin(): void
    {
        $this->only(['username', 'password'])
            ->remove('username', 'unique');
    }
}