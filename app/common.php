<?php
// 应用公共文件


use think\facade\Config;

if (!function_exists('setting')) {
    /**
     * 设置相关助手函数
     * @param string $name
     * @param null $value
     * @return array|bool|mixed|null
     */
    function setting($name = '', $value = null)
    {
        if ($value === null && is_string($name)) {
            //获取一级配置
            if ('.' === substr($name, -1)) {
                $result = Config::get(substr($name, 0, -1));
                if (count($result) === 0) {
                    //如果文件不存在，查找数据库
                    $result = get_database_setting(substr($name, 0, -1));
                }

                return $result;
            }
            //判断配置是否存在或读取配置
            if (0 === strpos($name, '?')) {
                $result = Config::has(substr($name, 1));
                if (!$result) {
                    //如果配置不存在，查找数据库
                    if ($name && false === strpos($name, '.')) {
                        return [];
                    }

                    if ('.' === substr($name, -1)) {

                        return get_database_setting(substr($name, 0, -1));
                    }

                    $name    = explode('.', $name);
                    $name[0] = strtolower($name[0]);

                    $result = get_database_setting($name[0]);
                    if ($result) {
                        $config = $result;
                        // 按.拆分成多维数组进行判断
                        foreach ($name as $val) {
                            if (isset($config[$val])) {
                                $config = $config[$val];
                            } else {
                                return null;
                            }
                        }

                        return $config;

                    }
                    return $result;
                }

                return $result;
            }

            $result = Config::get($name);
            if (!$result) {
                $result = get_database_setting($name);
            }
            return $result;
        }
        return Config::set($name, $value);
    }

}

if (!function_exists('get_database_setting')) {
    function get_database_setting($name)
    {
        $result = [];
        $group  = \app\common\model\SettingGroup::where('code', $name)->find();
        if ($group) {
            $result = [];
            foreach ($group->setting as $key => $setting) {
                $key_setting = [];
                foreach ($setting->content as $content) {
                    $key_setting[$content['field']] = $content['content'];
                }
                $result[$setting->code] = $key_setting;
            }
        }

        return $result;
    }
}
