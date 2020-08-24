<?php
/**
 * 后台菜单控制器
 * @author yupoxiong<i@yupoxiong.com>
 */

declare (strict_types=1);

namespace app\admin\controller;

use app\admin\model\AdminMenu;
use app\admin\validate\AdminMenuValidate;
use Exception;
use think\db\exception\DbException;
use think\db\Query;
use think\Request;
use think\Response;
use think\response\Json;

class AdminMenuController extends BaseController
{

    /**
     * 列表
     *
     * @param Request $request
     * @param AdminMenu $model
     * @return string
     * @throws DbException
     * @throws Exception
     */
    public function index(Request $request, AdminMenu $model): string
    {
        $param = $request->param();
        $data  = $model->scope('where', $param)
            ->paginate([
                'list_rows' => $this->admin['per_page'],
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
     * 添加
     *
     * @param Request $request
     * @param AdminMenu $model
     * @param AdminMenuValidate $validate
     * @return string|Json
     * @throws Exception
     */
    public function add(Request $request, AdminMenu $model, AdminMenuValidate $validate)
    {

        if ($request->isPost()) {
            $param = $request->param();
            $check = $validate->scene('admin_save')->check($param);
            if (!$check) {
                return admin_error($validate->getError());
            }

            $result   = $model::create($param);
            $redirect = isset($param['_create']) && (int)$param['_create'] === 1 ? URL_RELOAD : URL_BACK;

            return $result ? admin_success('添加成功', [], $redirect) : admin_error('添加失败');
        }
        return $this->fetch();
    }

    /**
     * 修改
     *
     * @param int $id
     * @param Request $request
     * @param AdminMenu $model
     * @param AdminMenuValidate $validate
     * @return string|Json
     * @throws Exception
     */
    public function edit($id, Request $request, AdminMenu $model, AdminMenuValidate $validate)
    {
        $data = $model->findOrEmpty($id);
        if ($request->isPost()) {
            $param = $request->param();
            $check = $validate->scene('admin_update')->check($param);
            if (!$check) {
                return admin_error($validate->getError());
            }

            $result = $model::update($param, $id);

            return $result ? admin_success('修改成功') : admin_error('修改失败');
        }

        $this->assign([
            'data' => $data,
        ]);

        return $this->fetch();
    }

    /**
     * 删除
     *
     * @param mixed $id
     * @param AdminMenu $model
     * @return Response
     */
    public function del($id, AdminMenu $model): Response
    {


        $result = $model::destroy(static function ($query) use ($id) {
            /** @var Query $query */
            $query->whereIn('id', $id);
        });

        return $result ? admin_success('删除成功', [], URL_RELOAD) : admin_error('删除失败');
    }
}