<?php
/**
 * 用户等级模型
*/

namespace app\common\model;

use think\model\concern\SoftDelete;

class UserLevel extends CommonBaseModel
{
    // 自定义选择数据
    

    use SoftDelete;
    public $softDelete = true;
    protected $name = 'user_level';
    protected $autoWriteTimestamp = true;

    //可搜索字段
    protected $searchField = [];

    //可作为条件的字段
    protected $whereField = [];

    //可做为时间
    protected $timeField = [];

    //是否启用获取器
public function getStatusTextAttr($value, $data)
{
    return self::BOOLEAN_TEXT[$data['status']];
}

    

    
}
