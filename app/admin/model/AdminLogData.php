<?php
/**
 *
 * @author yupoxiong<i@yupoxiong.com>
 */

declare (strict_types=1);


namespace app\admin\model;


use think\model\relation\BelongsTo;

class AdminLogData extends AdminBaseModel
{
//关联log
    public function adminLog(): BelongsTo
    {
        return $this->belongsTo(AdminLog::class);
    }


}