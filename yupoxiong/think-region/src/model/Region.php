<?php
/**
 * @author yupoxiong<i@yufuping.com>
 */

namespace yupoxiong\region\model;


class Region extends Model
{
    /**
     * @param \think\Request $request
     * @return array|\think\Collection|\think\model\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getDataList($request)
    {
        $data = $request->param('parent_id')?$this->where('parent_id',$request->param('parent_id')):false;

        return $this->select();
    }
}