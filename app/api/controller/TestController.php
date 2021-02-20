<?php
/**
 *
 * @author yupoxiong<i@yupoxiong.com>
 */

declare (strict_types=1);


namespace app\api\controller;


use GatewayClient\Gateway;

class TestController extends ApiBaseController
{

    public function __construct()
    {
        Gateway::$registerAddress = '127.0.0.1:1238';

        // 1v1房间
        $wait_room_1v1 = 'wait_room_1v1';
        $wait_room_2v2 = 'wait_room_2v2';

    }

    public function room()
    {
        return api_success([
            'room_list'=>[
                'wait_room_1v1',
                'wait_room_2v2',
            ],
        ]);
    }

    public function index()
    {
        Gateway::sendToClient('7f0000010b5400000001','dddd');
    }

    public function test()
    {
        $group1 = 'game1';

        Gateway::joinGroup('7f0000010b5500000004',$group1);
    }

    public function test2()
    {
        $group1 = 'game1';
        $all = Gateway::getClientIdListByGroup($group1);
        dump($all);

    }

    public function test3()
    {
        $group1 = 'game1';
        $all = Gateway::sendToGroup($group1,'{"type":"notice"}');
        dump($all);

    }

}