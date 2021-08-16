<?php
/**
 *
 * @author yupoxiong<i@yupoxiong.com>
 */

declare (strict_types=1);


namespace app\admin\controller;


use think\facade\Filesystem;
use think\Request;

class FileController extends AdminBaseController
{

    protected array $authExcept = [
        'upload',
        'img',
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
     * @return string
     * @throws \Exception
     */
    public function img(Request $request)
    {
        $min_file_count = $request->param('min_file_count') ?? 1;
        $max_file_count = $request->param('max_file_count') ?? 1;
        $file_type      = $request->param('file_type') ?? 'image';
        $dom_id         = $request->param('dom_id') ?? 'img';

        $multiple = $request->param('is_multiple') ? 'multiple="multiple"' : '';

        if ($request->isPost()) {

            $file = $request->file('file');
            $name = Filesystem::putFile('topic', $file);

            $url = config('filesystem.disks.public.url') . '/' . $name;

            //caption: "genqrcode.png"
            //downloadUrl: "/uploads/genqrcode.png"
            //exif: null
            //key: "genqrcode.png"
            //size: 943
            //url: "/site/file-delete"
            return json([
                'initialPreview'       => [$url],
                'initialPreviewAsData' => true,
                'initialPreviewConfig' => [
                    ['downloadUrl' => $url,
                     'key'         => $name,
                     'url'         => '/del',
                    ]
                ],
            ]);

            return admin_success('上传成功', [
                'url' => $url
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

    public function multiUpload(Request $request)
    {

    }
}