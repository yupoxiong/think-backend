<?php
/**
 * 登录退出控制器
 * @author yupoxiong<i@yupoxiong.com>
 */

declare (strict_types=1);


namespace app\admin\controller;


use app\admin\exception\AdminServiceException;
use app\admin\service\AuthService;
use app\admin\validate\AdminUserValidate;
use Exception;
use think\captcha\facade\Captcha;
use think\exception\ValidateException;
use think\Request;
use think\Response;
use think\response\Json;
use think\response\Redirect;
use util\geetest\GeeTest;

class AuthController extends AdminBaseController
{

    protected array $loginExcept = [
        'admin/auth/login',
        'admin/auth/captcha',
        'admin/auth/geetest',
    ];

    protected array $authExcept = [
        'admin/auth/logout',
    ];

    /**
     * 登录
     * @param Request $request
     * @param AuthService $service
     * @param AdminUserValidate $validate
     * @return string|Json
     * @throws Exception
     */
    public function login(Request $request, AuthService $service, AdminUserValidate $validate)
    {

        $redirect = $request->param('redirect') ?? url('admin/index/index');

        $login_config = setting('admin.login');

        if ($request->isPost()) {
            $param = $request->param();
            try {

                $captcha = (int)$login_config['captcha'];

                if ($captcha === 1) {
                    if (!captcha_check($param['captcha'])) {
                        return admin_error('验证码错误');
                    }
                }

                $validate->scene('login')->failException(true)->check($param);

                $username = $param['username'];
                $password = $param['password'];
                $remember = $param['remember'] ?? 0;
                $redirect = $param['redirect'] ?? url('admin/index/index')->build();

                $admin_user = $service->login($username, $password);
                $service->setAdminUserAuthInfo($admin_user, $remember);

            } catch (ValidateException | AdminServiceException $e) {
                $msg = $e->getMessage();
                return admin_error(lang($msg));
            }

            return admin_success('登录成功', [], $redirect);
        }

        $geetest_config = setting('admin.login');
        $geetest_id     = $geetest_config['geetest_id'] ?? '';

        $this->assign([
            'redirect'     => $redirect,
            'login_config' => $login_config,
            'geetest_id'   => $geetest_id,
        ]);

        return $this->fetch();
    }


    /**
     * 退出
     * @param AuthService $service
     * @return Json
     */
    public function logout(AuthService $service): Json
    {
        $result = $service->logout($this->user);

        $data   = [
            'redirect' => url('admin/index/index')->build(),
        ];


        return $result ? admin_success('退出成功', $data) : admin_error();

    }

    /**
     * 图形验证码
     * @return Response
     */
    public function captcha(): Response
    {
        return Captcha::create();
    }

    //极验初始化
    public function geetest(Request $request)
    {

        $config  = setting('admin.login');
        $geeTest = new GeeTest($config['geetest_id'], $config['geetest_key']);

        $ip = $request->ip();

        $ug = $request->header('user-agent');

        $data = array(
            'gt_uid'      => md5($ip . $ug),
            'client_type' => 'web',
            'ip_address'  => $ip,
        );

        $status = $geeTest->preProcess($data);

        session('gt_server', $status);
        session('gt_uid', $data['gt_uid']);

        return admin_success($status, $geeTest->getResponse());
    }

}