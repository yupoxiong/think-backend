<?php
/**
 * 公共基础模型
 * @author yupoxiong<i@yupoxiong.com>
 */

declare (strict_types=1);


namespace app\common\model;


use think\Model;

class CommonBaseModel extends Model
{
    // 是否字段，使用场景：用户的是否冻结，文章是否为热门等等。
    public const BOOLEAN_TEXT = [0 => '否', 1 => '是'];

    protected $defaultSoftDelete = 0;
}