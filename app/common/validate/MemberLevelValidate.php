<?php
/**
 * 会员等级验证器
 */

namespace app\common\validate;

class MemberLevelValidate extends CommonBaseValidate
{
    protected $rule = [
            'img|图片' => 'require',
    'status|是否启用' => 'require',

    ];

    protected $message = [
            'img.require' => '图片不能为空',
    'status.require' => '是否启用不能为空',

    ];

    protected $scene = [
        'admin_add'     => ['name', 'description', 'img', 'status', ],
        'admin_edit'    => ['id', 'name', 'description', 'img', 'status', ],
        'admin_del'     => ['id', ],
        'admin_disable' => ['id', ],
        'admin_enable'  => ['id', ],
        'api_add'       => ['name', 'description', 'img', 'status', ],
        'api_info'      => ['id', ],
        'api_edit'      => ['id', 'name', 'description', 'img', 'status', ],
        'api_del'       => ['id', ],
        'api_disable'   => ['id', ],
        'api_enable'    => ['id', ],
    ];

    

}
