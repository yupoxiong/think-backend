<?php
/**
 *
 * @author yupoxiong<i@yupoxiong.com>
 */

declare (strict_types=1);


namespace app\admin\model;


use think\model\relation\BelongsTo;
use think\model\relation\HasOne;

class AdminLog extends AdminBaseModel
{

    /**
     * 关联用户
     * @return BelongsTo
     */
    public function adminUser(): BelongsTo
    {
        return $this->belongsTo(AdminUser::class);
    }

    /**
     * 关联详情
     * @return HasOne
     */
    public function adminLogData(): HasOne
    {
        return $this->hasOne(AdminLogData::class);
    }

}