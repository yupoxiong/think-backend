<?php
/**
 *
 * @author yupoxiong<i@yupoxiong.com>
 */

declare (strict_types=1);


namespace app\admin\controller;


use think\facade\Filesystem;
use think\Request;
use think\response\Json;

class FileController extends AdminBaseController
{

    protected array $authExcept = [
        'upload',
        'img',
        'index',
    ];

    /**
     * @param Request $request
     * @return string|Json
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
            $name = Filesystem::putFile('topic', $file);

            $url = config('filesystem.disks.public.url');

            return admin_success('上传成功', [
                'url' => $url . '/' . $name
            ]);
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

    /**
     * @param Request $request
     * @return Json
     */
    public function img(Request $request): Json
    {
        if ($request->isPost()) {
            $param = $request->param();
            $field = $param['file_field'] ?? 'file';
            $dir   = $param['file_dir'] ?? 'uploads';

            $file = $request->file($field);

            if (!$file) {
                return json([
                    'message' => '非法上传'
                ]);
            }

            $name = Filesystem::putFile($dir, $file);

            $url = config('filesystem.disks.public.url') . '/' . $name;

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

    /**
     * 删除文件
     * @param Request $request
     * @return Json
     */
    public function del(Request $request): Json
    {
        $file        = urldecode($request->param('file'));

        $path        = app()->getRootPath() . 'public' . $file;
        $true_delete = config('filesystem.form_true_delete');
        $result      = $true_delete ? @unlink($path) : true;
        return $result ? json(['message' => '成功',]) : json(['message' => '失败']);
    }

}