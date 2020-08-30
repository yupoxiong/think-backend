<?php
/**
 *
 * @author yupoxiong<i@yupoxiong.com>
 */

declare (strict_types=1);


namespace app\admin\controller;


use think\facade\Filesystem;
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
        $min_file_count = $request->param('min_file_count') ?? 1;
        $max_file_count = $request->param('max_file_count') ?? 1;
        $file_type      = $request->param('file_type') ?? 'image';
        $dom_id         = $request->param('dom_id') ?? 'img';

        $multiple = $request->param('is_multiple') ? 'multiple="multiple"' : '';

        if ($request->isPost()) {
            $file = $request->file('file');
            $name = Filesystem::putFile( 'topic', $file);
            return admin_success('上传成功',['url'=>$name]);
        }

        $this->assign([
            'min_file_count' => $min_file_count,
            'max_file_count' => $max_file_count,
            'multiple'       => $multiple,
            'file_type'      => $file_type,
            'dom_id'         => $dom_id,
        ]);
        return $this->fetch();
    }

    public function multiUpload(Request $request)
    {

    }
}