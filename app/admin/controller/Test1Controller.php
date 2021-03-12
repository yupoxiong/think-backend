<?php
/**
 *
 * @author yupoxiong<i@yupoxiong.com>
 */

declare (strict_types=1);


namespace app\admin\controller;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;


class Test1Controller
{

    /**
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function index()
    {


        $config = [
            'creator'     => 'creator3',
            'modify'      => 'modify3',
            'title'       => 'title3',
            'subject'     => 'subject3',
            'description' => 'description3',
            'keywords'    => '空格分割',
            'category'    => 'category3',
            'sheet_title' => 'Sheet1',
        ];

        $spreadsheet = new Spreadsheet();

        // Set document properties
        $spreadsheet->getProperties()
            ->setCreator($config['creator'])
            ->setLastModifiedBy($config['modify'])
            ->setTitle($config['title'])
            ->setSubject($config['subject'])
            ->setDescription($config['description'])
            ->setKeywords($config['keywords'])
            ->setCategory($config['category']);

        $char_index = range('A', 'Z');
        // 处理超过26列
        $a = 'A';
        foreach ($char_index as $item) {
            $char_index[] = $a . $item;
        }

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
        foreach ($head as $key => $val) {
            $spreadsheet->setActiveSheetIndex(0)
                ->setCellValue($char_index[$key] . '1', $val);
        }

        // Excel body 部分
        foreach ($body as $key => $val) {
            $row = $key + 2;
            $col = 0;
            foreach ($val as $k => $v) {
                $spreadsheet->getActiveSheet()->setCellValue($char_index[$col].$row, $v);
                $col++;
            }
        }

        // Rename worksheet
        $spreadsheet->getActiveSheet()->setTitle($config['sheet_title']);

        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $spreadsheet->setActiveSheetIndex(0);

        // Redirect output to a client’s web browser (Xlsx)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="01simple.xlsx"');
        header('Cache-Control: max-age=0');

        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');

        // If you're serving to IE over SSL, then the following may be needed
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header('Pragma: public'); // HTTP/1.0

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit();
    }
}