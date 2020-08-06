<?php
/**
 * 后台用户模型
 * @author yupoxiong<i@yupoxiong.com>
 */

declare (strict_types=1);


namespace app\admin\model;

/**
 * Class AdminUser
 * @package app\admin\model
 * @property int $id
 * @property string $username
 * @property string $password
 * @property array $auth_url
 */
class AdminUser extends AdminBaseModel
{

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
     * @param $password
     * @return string
     */
    protected function setEncryptPassword($password): string
    {
        return base64_encode(password_hash($password, 1));
    }


    //获取当前用户已授权的显示菜单
    public function getShowMenu()
    {
        if ($this->id === 1) {
            return AdminMenu::where('is_show', 1)->order('sort_id', 'asc')->order('id', 'asc')->column('id,parent_id,name,url,icon,sort_id', 'id');
        }

        $role_urls = AdminRole::where('id', 'in', $this->role)->where('status', 1)->column('url');

        $url_id_str = '';
        foreach ($role_urls as $key => $val) {
            $url_id_str .= $key == 0 ? $val : ',' . $val;
        }

        $url_id = array_unique(explode(',', $url_id_str));
        return AdminMenu::where('id', 'in', $url_id)->where('is_show', 1)->order('sort_id', 'asc')->order('id', 'asc')->column('id,parent_id,name,url,icon,sort_id', 'id');
    }

}