<?php
/**
 * 会员等级控制器
 */

namespace app\api\controller;

use think\response\Json;
use app\api\service\MemberLevelService;
use app\common\validate\MemberLevelValidate;
use app\api\exception\ApiServiceException;

class MemberLevelController extends ApiBaseController
{
    /**
     * 列表
     * @param MemberLevelService $service
     * @return Json
     */
    public function index(MemberLevelService $service): Json
    {
        try {
            $data   = $service->getList($this->param, $this->page, $this->limit);
            $result = [
                'member_level' => $data,
            ];

            return api_success($result);
        } catch (ApiServiceException $e) {
            return api_error($e->getMessage());
        }
    }

    /**
     * 添加
     *
     * @param MemberLevelValidate $validate
     * @param MemberLevelService $service
     * @return Json
     */
    public function add(MemberLevelValidate $validate, MemberLevelService $service): Json
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
     * @param MemberLevelValidate $validate
     * @param MemberLevelService $service
     * @return Json
     */
    public function info(MemberLevelValidate $validate, MemberLevelService $service): Json
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
     * @param MemberLevelService $service
     * @param MemberLevelValidate $validate
     * @return Json
     */
    public function edit(MemberLevelService $service, MemberLevelValidate $validate): Json
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
     * @param MemberLevelService $service
     * @param MemberLevelValidate $validate
     * @return Json
     */
    public function del(MemberLevelService $service, MemberLevelValidate $validate): Json
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
     * @param MemberLevelService $service
     * @param MemberLevelValidate $validate
     * @return Json
     */
    public function disable(MemberLevelService $service, MemberLevelValidate $validate): Json
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
     * @param MemberLevelService $service
     * @param MemberLevelValidate $validate
     * @return Json
     */
    public function enable(MemberLevelService $service, MemberLevelValidate $validate): Json
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
