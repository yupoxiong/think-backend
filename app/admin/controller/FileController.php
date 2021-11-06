<?php
/**
 *
 * @author yupoxiong<i@yupoxiong.com>
 */

declare (strict_types=1);


namespace app\admin\controller;


use think\exception\ValidateException;
use think\facade\Filesystem;
use think\Request;
use think\response\Json;

class FileController extends AdminBaseController
{

    /**
     * 表单上传组建使用
     * @param Request $request
     * @return Json
     */
    public function upload(Request $request): Json
    {
        if ($request->isPost()) {
            $param = $request->param();
            $field = $param['file_field'] ?? 'file';
            $dir   = $param['file_dir'] ?? 'uploads';
            // 文件类型，默认图片
            $file_type = $param['file_type'] ?? 'image';
            // 上传到本地，可自行修改为oss之类的
            $config = config('filesystem.disks.public');

            $files = $request->file();

            try {
                validate([$field => $config['validate'][$file_type]])->check($files);
            } catch (ValidateException $e) {
                return json([
                    'message' => $e->getMessage()
                ], 500);
            }

            $file = $files[$field];

            $name = Filesystem::putFile($dir, $file);

            $url = $config['url'] . '/' . $name;

            return json([
                'code'                 => 200,
                'initialPreview'       => [$url],
                'initialPreviewAsData' => true,
                'showDownload'         => false,
                'initialPreviewConfig' => [
                    [
                        'downloadUrl' => $url,
                        'key'         => $file->getOriginalName(),
                        'caption'     => $file->getOriginalName(),
                        'url'         => url('admin/file/del', ['file' => $url])->build(),
                        'size'        => $file->getSize(),
                    ]
                ],
            ]);
        }

        return admin_error('非法访问');
    }

    public function editor(Request $request): Json
    {

        if ($request->isPost()) {
            $param = $request->param();
            $field = $param['file_field'] ?? 'file';
            $dir   = $param['file_dir'] ?? 'eitor';
            // 文件类型，默认图片
            $file_type = $param['file_type'] ?? 'image';
            // 上传到本地，可自行修改为oss之类的
            $config = config('filesystem.disks.public');

            $files = $request->file();

            try {
                validate([$field => $config['validate'][$file_type]])->check($files);
            } catch (ValidateException $e) {
                return json([
                    "errno"   => 1,
                    'message' => $e->getMessage()
                ]);
            }

            $file = $files[$field];

            $name = Filesystem::putFile($dir, $file);

            $url = $config['url'] . '/' . $name;

            return json([
                'errno' => 0,
                'data'  => [
                    [
                        'url'  => $url,
                        'href' => '',
                        'alt'  => $file->getOriginalName(),
                        'size' => $file->getSize(),
                    ],
                ]
            ]);
        }

        return json([
            "errno"   => 1,
            'message' => '非法访问'
        ]);


    }

    /**
     * 删除文件
     * @param Request $request
     * @return Json
     */
    public function del(Request $request): Json
    {
        $file = urldecode($request->param('file'));

        $path        = app()->getRootPath() . 'public' . $file;
        $true_delete = config('filesystem.form_true_delete');
        $result      = $true_delete ? @unlink($path) : true;
        return $result ? json(['message' => '成功',]) : json(['message' => '失败']);
    }

}