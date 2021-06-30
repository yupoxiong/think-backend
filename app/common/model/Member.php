<?php
/**
 * 会员模型
*/

namespace app\common\model;

use think\model\concern\SoftDelete;

class Member extends CommonBaseModel
{
    // 自定义选择数据
    

    use SoftDelete;

    protected $name = 'member';
    protected $autoWriteTimestamp = true;

    // 可搜索字段
    public array $searchField = ['username','mobile','nickname',];

    // 可作为条件的字段
    public array $whereField = [];

    // 可做为时间
    public array $timeField = [];

    //是否启用获取器
public function getStatusTextAttr($value, $data)
{
    return self::BOOLEAN_TEXT[$data['status']];
}

    //关联会员等级
public function memberLevel()
{
    return $this->belongsTo(MemberLevel::class);
}

    
}
