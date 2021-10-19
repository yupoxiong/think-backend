<?php
/**
 * 会员等级验证器
 */

namespace app\common\validate;

class MemberLevelValidate extends CommonBaseValidate
{
    protected $rule = [
            'name|名称' => 'require',
    'description|简介' => 'require',
    'img|图片' => 'require',
    'status|是否启用' => 'require',

    ];

    protected $message = [
            'name.require' => '名称不能为空',
    'description.require' => '简介不能为空',
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
