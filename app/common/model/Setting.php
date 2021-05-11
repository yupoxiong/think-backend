<?php
/**
 * 设置模型
 */

namespace app\common\model;

use think\model\concern\SoftDelete;
use think\model\relation\BelongsTo;

/**
 * Class Setting
 * @package app\common\model
 * @property int $id ID
 * @property int $name 名称
 */
class Setting extends CommonBaseModel
{
    use SoftDelete;

    protected $name = 'setting';
    protected $autoWriteTimestamp = true;

    public array $noDeletionIds = [
        1, 2, 3, 4, 5,
    ];

    //可搜索字段
    protected $searchField = ['name', 'description', 'code',];


    //关联设置分组
    public function settingGroup(): BelongsTo
    {
        return $this->belongsTo(SettingGroup::class);
    }


    public function setContentAttr($value)
    {
        return json_encode($value, JSON_THROW_ON_ERROR);
    }

    public function getContentAttr($value)
    {
        return json_decode($value, true, 512, JSON_THROW_ON_ERROR);
    }


}
