<?php
/**
 * 用户模型
*/

namespace app\common\model;
/**
 * Class User
 * @package app\common\model
 * @property int $status
 */
class User extends CommonBaseModel
{
    // 自定义选择数据
    


    protected $name = 'user';
        // 可搜索字段
    public array $searchField = [];

    // 可作为条件的字段
    public array $whereField = [];

    // 可做为时间
    public array $timeField = ['create_time',];

    //是否启用获取器
public function getStatusTextAttr($value, $data): string
{
    return self::BOOLEAN_TEXT[$data['status']];
}

    //关联用户等级
public function userLevel()
{
    return $this->belongsTo(UserLevel::class);
}

    
}
