<?php


namespace app\admin\traits;

use think\Request;

trait AdminActionTrait
{
    protected $model;
    protected $request;

    public function index(): string
    {
        $request = $this->request;
        $model = $this->model;

        $param = $request->param();
        $data  = $model->scope('where', $param)
            ->paginate([
                'list_rows' => $this->admin['admin_list_rows'],
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
}