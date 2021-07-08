<?php
/**
 * 会员控制器
 */

namespace app\api\controller;

use think\response\Json;
use app\api\service\MemberService;
use app\common\validate\MemberValidate;
use app\api\exception\ApiServiceException;

class MemberController extends ApiBaseController
{
    /**
     * 列表
     * @param MemberService $service
     * @return Json
     */
    public function index(MemberService $service): Json
    {
        try {
            $data   = $service->getList($this->param, $this->page, $this->limit);
            $result = [
                'member' => $data,
            ];

            return api_success($result);
        } catch (ApiServiceException $e) {
            return api_error($e->getMessage());
        }
    }

    /**
     * 添加
     *
     * @param MemberValidate $validate
     * @param MemberService $service
     * @return Json
     */
    public function add(MemberValidate $validate, MemberService $service): Json
    {
        $check = $validate->scene('api_add')->check($this->param);
        if (!$check) {
            return api_error($validate->getError());
        }

        $result = $service->createData($this->param);

        return $result ? api_success() : api_error();
    }

    /**
     * 详情
     *
     * @param MemberValidate $validate
     * @param MemberService $service
     * @return Json
     */
    public function info(MemberValidate $validate, MemberService $service): Json
    {
        $check = $validate->scene('api_info')->check($this->param);
        if (!$check) {
            return api_error($validate->getError());
        }

        try {

            $result = $service->getDataInfo($this->id);
            return api_success([
                'user_level' => $result,
            ]);

        } catch (ApiServiceException $e) {
            return api_error($e->getMessage());
        }
    }

    /**
     * 修改
     * @param MemberService $service
     * @param MemberValidate $validate
     * @return Json
     */
    public function edit(MemberService $service, MemberValidate $validate): Json
    {
        $check = $validate->scene('api_edit')->check($this->param);
        if (!$check) {
            return api_error($validate->getError());
        }

        try {
            $service->updateData($this->id, $this->param);
            return api_success();
        } catch (ApiServiceException $e) {
            return api_error($e->getMessage());
        }
    }

    /**
     * 删除
     * @param MemberService $service
     * @param MemberValidate $validate
     * @return Json
     */
    public function del(MemberService $service, MemberValidate $validate): Json
    {
        $check = $validate->scene('api_del')->check($this->param);
        if (!$check) {
            return api_error($validate->getError());
        }

        try {
            $service->deleteData($this->id);
            return api_success();
        } catch (ApiServiceException $e) {
            return api_error($e->getMessage());
        }
    }

    /**
     * 禁用
     * @param MemberService $service
     * @param MemberValidate $validate
     * @return Json
     */
    public function disable(MemberService $service, MemberValidate $validate): Json
    {
        $check = $validate->scene('api_disable')->check($this->param);
        if (!$check) {
            return api_error($validate->getError());
        }

        try {
            $service->disableData($this->id);
            return api_success();
        } catch (ApiServiceException $e) {
            return api_error($e->getMessage());
        }
    }


    /**
     * 启用
     * @param MemberService $service
     * @param MemberValidate $validate
     * @return Json
     */
    public function enable(MemberService $service, MemberValidate $validate): Json
    {
        $check = $validate->scene('api_enable')->check($this->param);
        if (!$check) {
            return api_error($validate->getError());
        }

        try {
            $service->enableData($this->id);
            return api_success();
        } catch (ApiServiceException $e) {
            return api_error($e->getMessage());
        }
    }
}
