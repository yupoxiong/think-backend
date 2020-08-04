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
}