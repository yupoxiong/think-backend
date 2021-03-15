<?php
/**
 * 会员等级控制器
 */

namespace app\admin\controller;

use Exception;
use think\Request;
use think\response\Json;
use app\common\model\MemberLevel;

use app\common\validate\MemberLevelValidate;

class MemberLevelController extends AdminBaseController
{

    /**
     * 列表
     *
     * @param Request $request
     * @param MemberLevel $model
     * @return string
     * @throws Exception
     */
    public function index(Request $request, MemberLevel $model): string
    {
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

    /**
     * 添加
     *
     * @param Request $request
     * @param MemberLevel $model
     * @param MemberLevelValidate $validate
     * @return string|Json
     * @throws Exception
     */
    public function add(Request $request, MemberLevel $model, MemberLevelValidate $validate)
    {
        if ($request->isPost()) {
            $param           = $request->param();
            $validate_result = $validate->scene('add')->check($param);
            if (!$validate_result) {
                return admin_error($validate->getError());
            }
                        //处理图片上传
            $attachment_img = new \app\common\model\Attachment;
            $file_img       = $attachment_img->upload('img');
            if ($file_img) {
                $param['img'] = $file_img->url;
            } else {
                return admin_error($attachment_img->getError());
            }
            

            $result = $model::create($param);

            $url = URL_BACK;
            if (isset($param['_create']) && (int)$param['_create'] === 1) {
               $url = URL_RELOAD;
            }

            return $result ? admin_success('添加成功', [], $url) : admin_error();
        }

        

        return $this->fetch();
    }

    /**
     * 修改
     *
     * @param $id
     * @param Request $request
     * @param MemberLevel $model
     * @param MemberLevelValidate $validate
     * @return string|Json
     * @throws Exception
     */
    public function edit($id, Request $request, MemberLevel $model, MemberLevelValidate $validate)
    {
        $data = $model->findOrEmpty($id);
        if ($request->isPost()) {
            $param = $request->param();
            $check = $validate->scene('admin_edit')->check($param);
            if (!$check) {
                return admin_error($validate->getError());
            }
                        //处理图片上传
            if (!empty($_FILES['img']['name'])) {
                $attachment_img = new \app\common\model\Attachment;
                $file_img       = $attachment_img->upload('img');
                if ($file_img) {
                    $param['img'] = $file_img->url;
                }
            }
            

            $result = $data->save($param);

            return $result ? admin_success('修改成功', [], URL_BACK) : admin_error('修改失败');
        }

        $this->assign([
            'data' => $data,
            
        ]);

        return $this->fetch('add');
    }

    /**
     * 删除
     *
     * @param mixed $id
     * @param MemberLevel $model
     * @return Json
     */
    public function del($id, MemberLevel $model): Json
    {
        if (count($model->noDeletionIds) > 0) {
            if (is_array($id)) {
                if (array_intersect($model->noDeletionIds, $id)) {
                    return admin_error('ID为' . implode(',', $model->noDeletionIds) . '的数据无法删除');
                }
            }  else if (in_array((int)$id, $model->noDeletionIds, true)) {
                return admin_error('ID为' . $id . '的数据无法删除');
            }
        }

        if ($model->softDelete) {
            $result = $model->whereIn('id', $id)->useSoftDelete('delete_time', time())->delete();
        } else {
            $result = $model->whereIn('id', $id)->delete();
        }

        return $result ? admin_success('操作成功', URL_RELOAD) : admin_error();
    }

    
    /**
     * 启用
     * @param mixed $id
     * @param MemberLevel $model
     * @return Json
     */
    public function enable($id, MemberLevel $model): Json
    {
        $result = $model->whereIn('id', $id)->update(['status' => 1]);
        return $result ? admin_success('操作成功', [], URL_RELOAD) : admin_error();
    }


    /**
     * 禁用
     * @param mixed $id
     * @param MemberLevel $model
     * @return Json
     */
    public function disable($id, MemberLevel $model): Json
    {
        $result = $model->whereIn('id', $id)->update(['status' => 0]);
        return $result ? admin_success('操作成功', [], URL_RELOAD) : admin_error();
    }


        /**
     * 导出
     *
     * @param Request $request
     * @param MemberLevel $model
     * @return mixed
     * @throws Exception
     */
    public function export(Request $request, MemberLevel $model)
    {
        $param = $request->param();
        $data  = $model->scope('where', $param)->select();

        $header = ['ID','名称','简介','图片','是否启用','创建时间',];
        $body   = [];
        foreach ($data as $item) {
            $record                = [];
            $record['id'] = $item->id;
$record['name'] = $item->name;
$record['description'] = $item->description;
$record['img'] = $item->img;
        $record['status'] = $item->status_text;
$record['create_time'] = $item->create_time;


            $body[] = $record;
        }
        return $this->exportData($header, $body, 'member_level-' . date('YmdHis'));

    }

        /**
     * @param Request $request
     * @return Json
     */
    public function import(Request $request): Json
    {
        $param           = $request->param();
        $field_name_list = ['名称','简介','图片','是否启用',];
        if (isset($param['action']) && $param['action'] === 'download_example') {
            $this->downloadExample($field_name_list);
        }

        $field_list = ['name','description','img','status',];
        $result = $this->importData('file','member_level',$field_list);

        return true === $result ? admin_success('操作成功', [], URL_RELOAD) : admin_error($result);
    }
}
