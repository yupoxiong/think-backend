<?php
/**
 * 后台菜单模型
 * @author yupoxiong<i@yupoxiong.com>
 */

declare (strict_types=1);


namespace app\admin\model;


class AdminMenu extends AdminBaseModel
{
    /**
     * @var array 不允许被删除的菜单ID
     */
    public $noDeletionId = [
        1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20
    ];

}