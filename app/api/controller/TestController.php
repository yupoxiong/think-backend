<?php
/**
 *
 * @author yupoxiong<i@yupoxiong.com>
 */

declare (strict_types=1);


namespace app\api\controller;


use app\common\model\Member;
use GatewayClient\Gateway;
use think\facade\Db;
use think\Request;
use util\jwt\Jwt;

class TestController extends ApiBaseController
{
    protected array $authExcept = [
        'index'
    ];

    public function index(Request $request)
    {
        $data = [];
        for ($i=1;$i<10000;$i++){
            $data[] = [
                'username'=>random_int(1,100000),
                'mobile'=>'1'.(random_int(5,9)).(random_int(100000000,999999999)),
                'nickname'=>random_int(1,99999999),
            ];
        }
        Db::name('member')->insertAll($data);

        return '<script>
function refreshPage() {
            window.location.reload();
        }
        setTimeout("refreshPage()",20000); //指定30秒刷新一次

</script>';
    }

}