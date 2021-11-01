<?php
/**
 * 会员等级模型
*/

namespace app\common\model;

use think\model\concern\SoftDelete;

class MemberLevel extends CommonBaseModel
{
    use SoftDelete;
    // 自定义选择数据
    

    protected $name = 'member_level';
    protected $autoWriteTimestamp = true;

    // 可搜索字段
    public array $searchField = ['name','description',];

    // 可作为条件的字段
    public array $whereField = [];

    // 可做为时间
    public array $timeField = [];

    

    // 关联会员
    public function member()
    {
        return $this->hasMany(Member::class);
    }

}
