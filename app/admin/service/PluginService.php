<?php
/**
 *
 * @author yupoxiong<i@yupoxiong.com>
 */

declare (strict_types=1);


namespace app\admin\service;



class PluginService
{

    protected $server;

    public function __construct()
    {
        $this->server = config('plugin.server');
    }


    /**
     * 获取服务器上插件列表
     */
    public function getServerPluginList($page, $limit, $category_id = 0)
    {
        //$client = new Client()
    }
}