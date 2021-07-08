<?php
/**
 * 会员等级模型
*/

namespace app\common\model;

use think\model\concern\SoftDelete;

class MemberLevel extends CommonBaseModel
{
    // 自定义选择数据
    

    use SoftDelete;

    protected $name = 'member_level';
    protected $autoWriteTimestamp = true;

    // 可搜索字段
    public array $searchField = [];

    // 可作为条件的字段
    public array $whereField = [];

    // 可做为时间
    public array $timeField = [];

    // 是否启用获取器
public function getStatusTextAttr($value, $data): string
{
    return self::BOOLEAN_TEXT[$data['status']];
}

    

    
}
