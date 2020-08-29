<?php
/**
 *
 * @author yupoxiong<i@yupoxiong.com>
 */

declare (strict_types=1);


namespace app\admin\controller;


use think\Request;

class FileController extends BaseController
{

    protected $authExcept = [
        'upload',
        'index',
    ];

    /**
     * @param Request $request
     * @return string
     * @throws \Exception
     */
    public function upload(Request $request)
    {
        if($request->isPost()){

        }

        return $this->fetch();
    }

    public function multiUpload(Request $request)
    {

    }
}