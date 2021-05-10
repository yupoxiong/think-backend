<?php
/**
 * 设置分组模型
 */

namespace app\common\model;

use think\model\Collection;
use think\model\concern\SoftDelete;
use think\model\relation\HasMany;

/**
 * Class SettingGroup
 * @package app\common\model
 * @property int $id
 * @property string $name
 * @property int $auto_create_file
 * @property int $auto_create_menu
 * @property string $code
 * @property string $module
 * @property string $description
 * @property Collection $setting
 * @property string $icon
 */
class SettingGroup extends CommonBaseModel
{
    use SoftDelete;
    protected $name = 'setting_group';
    protected $autoWriteTimestamp = true;

    public $noDeletionId  =[
        1,2,3,4,5,
    ];

    //可搜索字段
    protected array $searchField = ['name', 'description', 'code',];


    //关联设置
    public function setting(): HasMany
    {
        return $this->hasMany(Setting::class);
    }

    public function getAutoCreateMenuTextAttr($value,$data): string
    {
        return self::BOOLEAN_TEXT[$data['auto_create_menu']];
    }

    public function getAutoCreateFileTextAttr($value,$data): string
    {
        return self::BOOLEAN_TEXT[$data['auto_create_file']];
    }
}
