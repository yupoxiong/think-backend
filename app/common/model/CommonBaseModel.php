<?php
/**
 * 公共基础模型
 * @author yupoxiong<i@yupoxiong.com>
 */

declare (strict_types=1);


namespace app\common\model;


use think\db\Query;
use think\Model;

class CommonBaseModel extends Model
{
    // 是否字段，使用场景：用户的是否冻结，文章是否为热门等等。
    public const BOOLEAN_TEXT = [0 => '否', 1 => '是'];

    protected $defaultSoftDelete = 0;

    public array $noDeletionIds = [];

    /**
     * 查询处理
     * @var Query $query
     * @var array $param
     */
    public function scopeWhere($query, $param): void
    {
        //关键词like搜索
        $keywords = $param['_keywords'] ?? '';
        if ('' !== $keywords && count($this->searchField) > 0) {
            $this->searchField = implode('|', $this->searchField);
            $query->where($this->searchField, 'like', '%' . $keywords . '%');
        }

        //字段条件查询
        if (count($this->whereField) > 0 && count($param) > 0) {
            foreach ($param as $key => $value) {
                if ($value !== '' && in_array((string)$key, $this->whereField, true)) {
                    $query->where($key, $value);
                }
            }
        }

        //时间范围查询
        if (count($this->timeField) > 0 && count($param) > 0) {
            foreach ($param as $key => $value) {
                if ($value !== '' && in_array((string)$key, $this->timeField, true)) {
                    $field_type = $this->getFieldsType($this->table, $key);
                    $time_range = explode(' - ', $value);
                    [$start_time, $end_time] = $time_range;
                    //如果是int，进行转换
                    if (false !== strpos($field_type, 'int')) {
                        $start_time = strtotime($start_time);
                        if (strlen($end_time) === 10) {
                            $end_time .= '23:59:59';
                        }
                        $end_time = strtotime($end_time);
                    }
                    $query->where($key, 'between', [$start_time, $end_time]);
                }
            }
        }

        //排序
        $order = $param['_order'] ?? '';
        $by    = $param['_by'] ?? 'desc';
        $query->order($order ?: 'id', $by ?: 'desc');
    }

    public function isNoDeletionIds($id)
    {
        if (count($this->noDeletionIds) > 0) {
            if (is_array($id)) {
                if (array_intersect($this->noDeletionIds, $id)) {
                    return implode(',', $id);
                }
            } else if (in_array((int)$id, $this->noDeletionIds, true)) {
                return $id;
            }
        }
        return false;
    }
}