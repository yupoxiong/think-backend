<?php
/**
 * 后台操作日志控制器
 * @author yupoxiong<i@yupoxiong.com>
 */

declare (strict_types=1);

namespace app\admin\controller;

use Exception;
use think\Request;
use app\admin\model\AdminLog;

class AdminLogController extends AdminBaseController
{

    /**
     * 列表
     *
     * @param Request $request
     * @param AdminLog $model
     * @return string
     * @throws Exception
     */
    public function index(Request $request, AdminLog $model): string
    {
        $param = $request->param();
        $data  = $model->scope('where', $param)
            ->paginate([
                'list_rows' => $this->admin['admin_per_page'],
                'var_page'  => 'page',
                'query'     => $request->get()
            ]);

        // 关键词，排序等赋值
        $this->assign($request->get());

        $this->assign([
            'data'  => $data,
            'page'  => $data->render(),
            'total' => $data->total(),
        ]);
        return $this->fetch();
    }

    /**
     * @param $id
     * @param AdminLog $model
     * @return string
     * @throws Exception
     */
    public function detail($id, AdminLog $model): string
    {
        $data = $model->with('adminLogData')->findOrEmpty($id);

        $this->assign([
            'data'=>$data,
        ]);

        return $this->fetch();
    }
}
