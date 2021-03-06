<?php
/**
 * 后台用户模型
 * @author yupoxiong<i@yupoxiong.com>
 */

declare (strict_types=1);


namespace app\admin\model;

use think\Model;
use think\model\concern\SoftDelete;

/**
 * Class AdminUser
 * @package app\admin\model
 * @property int $id
 * @property string $username
 * @property string $password
 * @property array $auth_url
 * @property array $role
 * @property int $status
 */
class AdminUser extends AdminBaseModel
{

    use SoftDelete;

    public array $noDeletionIds = [1];

    /**
     * @param AdminUser $data
     * @return mixed|void
     */
    public static function onBeforeInsert($data)
    {
        $data->password = (new self)->setEncryptPassword($data->password);
    }

    /**
     * @param AdminUser $data
     * @return mixed|void
     */
    public static function onBeforeUpdate($data)
    {
        $old = (new self())->where('id', '=', $data->id)->findOrEmpty();
        /**@var AdminUser $old */
        if ($data->password !== $old->password) {
            $data->password = (new self)->setEncryptPassword($data->password);
        }
    }


    /**
     * 角色获取器
     * @param $value
     * @return array
     */
    public function getRoleAttr($value): array
    {
        return explode(',', $value);
    }

    /**
     * 角色修改器
     * @param $value
     * @return string
     */
    public function setRoleAttr($value): string
    {
        return implode(',', $value);
    }

    /**
     * 获取用户的角色列表
     * @param $value
     * @param $data
     * @return array|Model
     */
    public function getRoleListAttr($value, $data)
    {
        return (new AdminRole)->whereIn('id', $data['role'])->selectOrFail();
    }


    /**
     * 获取当前用户已授权的显示菜单
     * @return array
     */
    public function getShowMenu(): array
    {

        if ($this->id === 1) {
            return (new AdminMenu)->where('is_show', '=', 1)
                ->order('sort_number', 'asc')
                ->order('id', 'asc')
                ->column('id,parent_id,name,url,icon,sort_number', 'id');
        }

        $role_urls = (new AdminRole)->whereIn('id', $this->role)
            ->where('status', '=', 1)
            ->column('url');

        $url_id_str = '';
        foreach ($role_urls as $key => $val) {
            $url_id_str .= $key === 0 ? $val : ',' . $val;
        }

        $url_id = array_unique(explode(',', $url_id_str));

        return (new AdminMenu)->whereIn('id', $url_id)
            ->where('is_show', '=', 1)
            ->order('sort_number', 'asc')
            ->order('id', 'asc')
            ->column('id,parent_id,name,url,icon,sort_number', 'id');
    }


    /**
     * 设置加密密码
     * @param $password
     * @return string
     */
    protected function setEncryptPassword($password): string
    {
        return base64_encode(password_hash($password, 1));
    }

    /**
     * 获取授权的URL
     * @param $value
     * @param $data
     * @return array
     */
    public function getAuthUrlAttr($value, $data): array
    {
        $role_urls  = (new AdminRole)->where('id', 'in', $data['role'])->where('status', 1)->column('url');
        $url_id_str = '';
        foreach ($role_urls as $key => $val) {
            $url_id_str .= $key === 0 ? $val : ',' . $val;
        }
        $url_id   = array_unique(explode(',', $url_id_str));
        $auth_url = [];
        if (count($url_id) > 0) {
            $auth_url = (new AdminMenu)->where('id', 'in', $url_id)->column('url');
        }
        return $auth_url;
    }
}