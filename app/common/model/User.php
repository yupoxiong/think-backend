<?php
/**
 * 用户模型
 */

namespace app\common\model;

use think\model\concern\SoftDelete;
use think\model\relation\BelongsTo;

/**
 * @property int $id
 * @property int $status
 * @property string $password
 */
class User extends CommonBaseModel
{
    use SoftDelete;

    // 自定义选择数据
    // 是否启用列表
    public const STATUS_LIST = [
        1 => '是',
        0 => '否',
    ];


    protected $name = 'user';
    protected $autoWriteTimestamp = true;

    // 可搜索字段
    public array $searchField = ['username', 'mobile', 'nickname',];

    // 可作为条件的字段
    public array $whereField = ['user_level_id', 'status',];

    // 可做为时间
    public array $timeField = [];

    // [FORM_NAME]获取器
    public function getStatusNameAttr($value, $data): string
    {
        return self::STATUS_LIST[$data['status']];
    }


    // 关联用户等级
    public function userLevel(): BelongsTo
    {
        return $this->belongsTo(UserLevel::class);
    }

}
