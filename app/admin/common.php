<?php
/**
 * 后台公共函数文件
 * @author yupoxiong<i@yupoxiong.com>
 */

use app\admin\controller\SettingController;
use think\response\Json;
use app\common\model\SettingGroup;
use app\admin\model\AdminMenu;
/** 不做任何操作 */
const URL_CURRENT = 'url://current';
/** 刷新页面 */
const URL_RELOAD = 'url://reload';
/** 返回上一个页面 */
const URL_BACK = 'url://back';
/** 关闭当前layer弹窗 */
const URL_CLOSE_LAYER = 'url://close_layer';
/** 关闭当前弹窗并刷新父级 */
const URL_CLOSE_REFRESH = 'url://close_refresh';

if (!function_exists('admin_success')) {

    /**
     * 后台返回成功
     * @param string $msg
     * @param array $data
     * @param int $code
     * @param string $url
     * @param array $header
     * @param array $options
     * @return Json
     */
    function admin_success($msg = '操作成功', $data = [], $url = URL_CURRENT, $code = 200, array $header = [], $options = []): Json
    {
        return admin_result($msg, $data, $url, $code, $header, $options);
    }
}


if (!function_exists('admin_error')) {
    /**
     * 后台返回错误
     * @param string $msg
     * @param array $data
     * @param string $url
     * @param int $code
     * @param array $header
     * @param array $options
     * @return Json
     */
    function admin_error($msg = '操作失败', $data = [], $url = URL_CURRENT, $code = 500, array $header = [], $options = []): Json
    {
        return admin_result($msg, $data, $url, $code, $header, $options);
    }
}

if (!function_exists('admin_result')) {


    /**
     * 后台返回结果
     * @param array $data
     * @param string $msg
     * @param string $url
     * @param int $code
     * @param array $header
     * @param array $options
     * @return Json
     */
    function admin_result($msg = '', $data = [], $url = URL_CURRENT, $code = 500, $header = [], $options = []): Json
    {

        $data = [
            'msg'    => $msg,
            'code'   => $code,
            'data' => empty($data) ? (object)$data : $data,
            'url'    => $url,
        ];

        return json($data, 200, $header, $options);

    }
}



if (!function_exists('create_setting_file')) {
    /**
     * 生成配置文件
     * @param SettingGroup $data
     * @return bool
     */
    function create_setting_file($data): bool
    {
        $result = true;
        if ((int)$data->auto_create_file === 1) {
            $file = 'config/'.$data->code . '.php';
            if ($data->module !== 'app') {
                $file = 'app/'.$data->module .'/'. $file;
            }



            $setting   = $data->setting;
            $path      = app()->getRootPath() . $file;
            $file_code = "<?php\r\n\r\n/**\r\n* " .
                $data->name . ':' . $data->description .
                "\r\n* 此配置文件为自动生成，生成时间" . date('Y-m-d H:i:s') .
                "\r\n*/\r\n\r\nreturn [";
            foreach ($setting as $key => $value) {
                $file_code .= "\r\n    //" . $value['name'] . ':' . $value['description'] . "\r\n    '" . $value['code'] . "'=>[";
                foreach ($value->content as $content) {
                    $file_code .= "\r\n    //" . $content['name'] . "\r\n    '" .
                        $content['field'] . "'=>'" . $content['content'] . "',";
                }
                $file_code .= "\r\n],";
            }
            $file_code .= "\r\n];";
            $result    = file_put_contents($path, $file_code);
        }
        return $result ? true : false;
    }
}


if (!function_exists('create_setting_menu')) {
    /**
     * 生成配置文件
     * @param SettingGroup $data
     * @return bool
     */
    function create_setting_menu(SettingGroup $data): bool
    {

        $function = <<<EOF
    public function [GROUP_COED]()
    {
        return \$this->show([GROUP_ID]);
    }\n
}//append_menu
EOF;

        $result = true;
        if ((int)$data->auto_create_menu === 1) {
            $url  = get_setting_menu_url($data);
            /** @var AdminMenu $menu */
            $menu = (new app\admin\model\AdminMenu)->findOrEmpty(function ($query) use ($url) {
                $query->where('url', $url);
            });
            if (!$menu) {
                $result = AdminMenu::create([
                    'parent_id' => 43,
                    'name'      => $data->name,
                    'icon'      => $data->icon,
                    'is_show'   => 1,
                    'url'       => $url
                ]);
            } else {
                $menu->name = $data->name;
                $menu->icon = $data->icon;
                $menu->url  = $url;
                $result     = $menu->save();
            }

            $setting = new SettingController();
            if (!method_exists($setting, $data->code)) {

                $function = str_replace(array('[GROUP_COED]', '[GROUP_ID]'), array($data->code, $data->id), $function);

                $file_path = app()->getAppPath() . 'admin/controller/SettingController.php';
                $file      = file_get_contents($file_path);
                $file      = str_replace('}//append_menu', $function, $file);
                file_put_contents($file_path, $file);
            }
        }

        return $result ? true : false;
    }
}

if (!function_exists('create_setting_menu')) {
    /**
     * 获取菜单url
     * @param $data
     * @return string
     */
    function get_setting_menu_url($data): string
    {
        return 'admin/setting/'.$data->code;
    }
}