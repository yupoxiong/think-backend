<?php
/**
 *
 * @author yupoxiong<i@yupoxiong.com>
 */

declare (strict_types=1);


namespace app\admin\controller;

use app\admin\traits\AdminPhpOffice;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;


class TestController extends AdminBaseController
{



    public function index()
    {




        return $this->fetch();
    }

    public function index1()
    {

        $head = [];
        for ($i = 1; $i <= 30; $i++) {
            $head[] = '字段' . $i;
        }

        $body = [];
        for($j=1;$j<=100000;$j++){
            $data1 = [];
            for ($i = 1; $i <= 30; $i++) {
                $data1[$i] = $j.'-'.$i;
            }
            $body[] = $data1;
        }

        // Excel 表格头
        return $this->exportXlsx($head,$body,'aa');
    }

    public function index2()
    {

        $head = [];
        for ($i = 1; $i <= 30; $i++) {
            $head[] = '字段' . $i;
        }

        $body = [];
        for($j=1;$j<=100000;$j++){
            $data1 = [];
            for ($i = 1; $i <= 30; $i++) {
                $data1[$i] = $j.'-'.$i;
            }
            $body[] = $data1;
        }

        // Excel 表格头
        return $this->exportData($head,$body,'aa');
    }
}