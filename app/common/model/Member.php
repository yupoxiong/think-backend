<?php
/**
 * 会员模型
*/

namespace app\common\model;

use think\model\concern\SoftDelete;

class Member extends CommonBaseModel
{
    use SoftDelete;
    // 自定义选择数据
    

    protected $name = 'member';
    protected $autoWriteTimestamp = true;

    // 可搜索字段
    public array $searchField = ['username','mobile','nickname',];

    // 可作为条件的字段
    public array $whereField = [];

    // 可做为时间
    public array $timeField = [];

    

    // 关联会员等级
    public function memberLevel()
    {
        return $this->belongsTo(MemberLevel::class);
    }

}
