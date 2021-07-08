<?php
/**
 * 会员控制器
 */

namespace app\admin\controller;

use Exception;
use think\Request;
use think\db\Query;
use think\response\Json;
use app\common\model\Member;
use app\common\model\MemberLevel;

use app\common\validate\MemberValidate;

class MemberController extends AdminBaseController
{

    /**
     * 列表
     *
     * @param Request $request
     * @param Member $model
     * @return string
     * @throws Exception
     */
    public function index(Request $request, Member $model): string
    {
        $param = $request->param();
        $data  = $model->with('member_level')->scope('AdminWhere', $param)
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

    /**
     * 添加
     *
     * @param Request $request
     * @param Member $model
     * @param MemberValidate $validate
     * @return string|Json
     * @throws Exception
     */
    public function add(Request $request, Member $model, MemberValidate $validate)
    {
        if ($request->isPost()) {
            $param           = $request->param();
            $validate_result = $validate->scene('add')->check($param);
            if (!$validate_result) {
                return admin_error($validate->getError());
            }
                     
            $result = $model::create($param);

            $url = URL_BACK;
            if (isset($param['_create']) && (int)$param['_create'] === 1) {
               $url = URL_RELOAD;
            }

            return $result ? admin_success('添加成功', [], $url) : admin_error();
        }

        $this->assign([
    'member_level_list' => MemberLevel::select(),

]);



        return $this->fetch();
    }

    /**
     * 修改
     *
     * @param $id
     * @param Request $request
     * @param Member $model
     * @param MemberValidate $validate
     * @return string|Json
     * @throws Exception
     */
    public function edit($id, Request $request, Member $model, MemberValidate $validate)
    {
        $data = $model->findOrEmpty($id);
        if ($request->isPost()) {
            $param = $request->param();
            $check = $validate->scene('admin_edit')->check($param);
            if (!$check) {
                return admin_error($validate->getError());
            }
                       
            $result = $data->save($param);

            return $result ? admin_success('修改成功', [], URL_BACK) : admin_error('修改失败');
        }

        $this->assign([
            'data' => $data,
            'member_level_list' => MemberLevel::select(),

        ]);

        return $this->fetch('add');
    }

    /**
     * 删除
     *
     * @param mixed $id
     * @param Member $model
     * @return Json
     */
    public function del($id, Member $model): Json
    {
        $check = $model->inNoDeletionIds($id);
        if (false !== $check) {
            return admin_error('ID为' . $check . '的数据不能被删除');
        }

        $result = $model::destroy(static function ($query) use ($id) {
            /** @var Query $query */
            $query->whereIn('id', $id);
        });

        return $result ? admin_success('删除成功', [], URL_RELOAD) : admin_error('删除失败');
    }

    

    

        /**
     * @param Request $request
     * @return Json
     */
    public function import(Request $request): Json
    {
        $param           = $request->param();
        $field_name_list = ['会员等级','账号','密码','手机号','昵称','图片','是否启用',];
        if (isset($param['action']) && $param['action'] === 'download_example') {
            $this->downloadExample($field_name_list);
        }

        $field_list = ['member_level_id','username','password','mobile','nickname','avatar','status',];
        $result = $this->importData('file','member',$field_list);

        return true === $result ? admin_success('操作成功', [], URL_RELOAD) : admin_error($result);
    }
}
