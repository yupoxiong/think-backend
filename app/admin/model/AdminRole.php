<?php
/**
 * 后台角色模型
 * @author yupoxiong<i@yufuping.com>
 */

namespace app\admin\model;

/**
 * Class AdminRole
 * @package app\admin\model
 * @property array $url
 */
class AdminRole extends AdminBaseModel
{

    /**
     * 角色初始权限
     * @param AdminRole $data
     * @return mixed|void
     */
    public static function onBeforeInsert($data)
    {
        $data->url = [1, 2, 18];
    }

    protected function getUrlAttr($value)
    {
        return $value !== '' ? explode(',', $value) : [];
    }

    protected function setUrlAttr($value)
    {
        return $value !== '' ? implode(',', $value) : [];
    }
}
