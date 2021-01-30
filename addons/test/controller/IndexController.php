<?php
namespace addons\test\controller;

class IndexController
{
    public function link()
    {
        echo 'hello link';
    }

    public function testAction()
    {
        return request()->action();
    }
}