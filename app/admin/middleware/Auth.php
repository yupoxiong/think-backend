<?php
/**
 *
 * @author yupoxiong<i@yupoxiong.com>
 */

declare (strict_types=1);


namespace app\admin\middleware;


use app\admin\exception\AdminServiceException;
use app\admin\service\AuthService;
use think\Request;

class Auth
{

    /**
     * @param Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, \Closure $next)
    {

        try {
            $user = (new AuthService)->getAdminUserAuthInfo();
            $request->user = $user;
        } catch (AdminServiceException $e) {
            return $e->generateJSONResponse();
        }

        return $next($request);
    }
}