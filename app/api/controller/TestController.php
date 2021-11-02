<?php
/**
 * 测试控制器
 */

namespace app\api\controller;

use app\admin\model\AdminMenu;
use think\response\Json;
use app\api\service\TestService;
use app\common\validate\TestValidate;
use app\api\exception\ApiServiceException;
use MatthiasMullie\Minify;

class TestController
{
    protected function getAllFile($dir, $suffix = '', &$results = array()): array
    {
        $files = scandir($dir);

        foreach ($files as $key => $value) {
            $path = realpath($dir . DIRECTORY_SEPARATOR . $value);
            if (!is_dir($path)) {
                if(!empty($suffix)){
                    if(strrchr($path, $suffix) === $suffix){
                        $results[] = $path;
                    }
                }

            } else if ($value !== "." && $value !== "..") {
                $this->getAllFile($path, $suffix,$results);
            }
        }

        return $results;
    }

    protected function isSuffix($path, $suffix = '.css'): bool
    {
        $pos = strrpos($path, '.');
        if ($pos > 0) {
            $str = substr($path, $pos);
            dump($str);
            return $str === $suffix;
        }
        return false;
    }


    public function index1()
    {

        $path = app()->getRootPath() . 'public/static/admin/';

        $css_list = $this->getAllFile($path,'.css');

        $css_minify   = new Minify\CSS();
        foreach ($css_list as $css){
            $css_minify->add($css);
        }

        $css_content =  $css_minify->minify();
        $this->putFile($css_content,'admin.min.css');

        $js_minify = new Minify\JS();
        $js_list = $this->getAllFile($path,'.js');

        foreach ($js_list as $js){
            $js_minify->add($js);
        }

        $js_content =  $js_minify->minify();
        $this->putFile($js_content,'admin.min.js');

    }
    protected function putFile($content,$file_name)
    {
        $static_path = app()->getRootPath().'public/static/';
        file_put_contents($static_path.$file_name,$content);
    }


    /**
     * 列表
     * @param TestService $service
     * @return Json
     */
    public function index(TestService $service)
    {
        $json = (new \app\admin\model\AdminMenu)->field('id,parent_id,name,url,icon,is_show,is_top,sort_number,log_method')->select();
        dump(json_encode($json));
    }

    /**
     * 添加
     *
     * @param TestValidate $validate
     * @param TestService $service
     * @return Json
     */
    public function add(TestValidate $validate, TestService $service): Json
    {
        $check = $validate->scene('api_add')->check($this->param);
        if (!$check) {
            return api_error($validate->getError());
        }

        $result = $service->createData($this->param);

        return $result ? api_success() : api_error();
    }

    /**
     * 详情
     *
     * @param TestValidate $validate
     * @param TestService $service
     * @return Json
     */
    public function info(TestValidate $validate, TestService $service): Json
    {
        $check = $validate->scene('api_info')->check($this->param);
        if (!$check) {
            return api_error($validate->getError());
        }

        try {

            $result = $service->getDataInfo($this->id);
            return api_success([
                'user_level' => $result,
            ]);

        } catch (ApiServiceException $e) {
            return api_error($e->getMessage());
        }
    }

    /**
     * 修改
     * @param TestService $service
     * @param TestValidate $validate
     * @return Json
     */
    public function edit(TestService $service, TestValidate $validate): Json
    {
        $check = $validate->scene('api_edit')->check($this->param);
        if (!$check) {
            return api_error($validate->getError());
        }

        try {
            $service->updateData($this->id, $this->param);
            return api_success();
        } catch (ApiServiceException $e) {
            return api_error($e->getMessage());
        }
    }

    /**
     * 删除
     * @param TestService $service
     * @param TestValidate $validate
     * @return Json
     */
    public function del(TestService $service, TestValidate $validate): Json
    {
        $check = $validate->scene('api_del')->check($this->param);
        if (!$check) {
            return api_error($validate->getError());
        }

        try {
            $service->deleteData($this->id);
            return api_success();
        } catch (ApiServiceException $e) {
            return api_error($e->getMessage());
        }
    }


}
