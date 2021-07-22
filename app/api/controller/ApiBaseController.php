<?php
/**
 * api基础控制器
 * @author yupoxiong<i@yupoxiong.com>
 */

declare (strict_types=1);


namespace app\api\controller;


use app\api\service\TokenService;
use app\api\exception\ApiServiceException;
use app\common\model\User;
use think\exception\HttpResponseException;
use think\response\Json;

class ApiBaseController
{
    /**
     * @var array 无需验证登录的url，禁止在此处修改
     */
    protected array $loginExcept = [];

    /**
     * @var array 无需验证权限的url，禁止在此处修改
     */
    protected array $authExcept = [];


    /**
     * 当前访问的用户
     * @var int
     */
    protected int $uid = 0;

    /**
     * @var User 当前用户
     */
    protected User $user;

    /** @var array 当前请求参数 */
    protected array $param;

    /** @var int 当前请求数据ID */
    protected int $id;
    /** @var int 当前页数 */
    protected int $page;
    /** @var int 当前每页数量 */
    protected int $limit;

    public function __construct()
    {
        // 处理跨域问题
        $this->crossDomain();
        // 检查登录
        $this->checkLogin();
        // 检查登录
        $this->checkAuth();
        // 初始化部分数据
        $this->initData();
    }

    /**
     * 处理跨域
     */
    protected function crossDomain(): void
    {
        if (request()->isOptions()) {
            $header = config('api.cross_domain.header');
            throw new HttpResponseException(json('', 200, $header));
        }
    }

    /**
     * 检查登录
     */
    protected function checkLogin(): void
    {
        $tokenService = new TokenService();

        if (!in_array(request()->action(), $this->authExcept, true)) {

            $token = request()->header('token');

            // 缺少token
            if (empty($token)) {
                throw new HttpResponseException(api_unauthorized('未登录'));
            }

            // 验证token
            try {
                $this->uid = $tokenService->checkToken($token);
            } catch (ApiServiceException $e) {
                throw new HttpResponseException(api_unauthorized('验证token失败，信息：' . $e->getMessage()));
            }

            /** @var User $user */
            $user = (new User)->findOrEmpty($this->uid);
            if ($user->isEmpty()) {
                throw new HttpResponseException(api_unauthorized('用户不存在'));
            }

            if ($user->status === 0) {
                throw new HttpResponseException(api_unauthorized('账号被冻结'));
            }

            $this->user = $user;
        }
    }

    /**
     * 检查权限
     */
    public function checkAuth(): void
    {
        // TODO::这里可以自定义权限检查

    }

    protected function initData(): void
    {
        $this->param = (array)request()->param();
        // 初始化基本数据
        $this->page  = (int)($this->param['page'] ?? 1);
        $this->limit = (int)($this->param['limit'] ?? 10);
        $this->id    = (int)($this->param['id'] ?? 0);
    }


    public function __call($name, $arguments): Json
    {
        return api_error_404();
    }



}