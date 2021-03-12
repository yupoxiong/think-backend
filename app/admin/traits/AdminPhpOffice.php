<?php
/**
 * phpOffice相关操作
 * @author yupoxiong<i@yufuping.com>
 */

namespace app\admin\traits;

use Exception;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use think\exception\ValidateException;
use think\facade\Db;
use think\facade\Filesystem;
use XLSXWriter;

trait AdminPhpOffice
{

    /**
     * 普通导出，少于1万条数据可使用，1万+数据用exportXlsx
     * @param array $head
     * @param array $body
     * @param string $name
     * @param string $title
     * @return mixed
     */
    protected function exportData(array $head, array $body, $name = '', $title = '导出记录')
    {
        try {

            if (empty($name)) {
                $name = date('Y-m-d-H-i-s');
            }

            $spreadsheet = new Spreadsheet();
            $sheet       = $spreadsheet->setActiveSheetIndex(0);
            $char_index  = range('A', 'Z');

            // 处理超过26列
            $a = 'A';
            foreach ($char_index as $item) {
                $char_index[] = $a . $item;
            }

            // Excel 表格头
            foreach ($head as $key => $val) {
                $sheet->setCellValue($char_index[$key] . '1', $val);
            }

            // Excel body 部分
            foreach ($body as $key => $val) {
                $row = $key + 2;
                $col = 0;
                foreach ($val as $k => $v) {
                    $sheet->setCellValue($char_index[$col] . $row, $v);
                    $col++;
                }
            }

            $spreadsheet->getActiveSheet()->setTitle($title);

            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $name . '.xlsx"');
            header('Cache-Control: max-age=0');

            $objWriter = IOFactory::createWriter($spreadsheet, 'Xlsx');
            $objWriter->save('php://output');
            exit();
        } catch (Exception $e) {
            exit($e->getMessage() . '(' . $e->getFile() . '-' . $e->getLine() . ')');
        }
    }

    /**
     * 导入数据到数据库
     * @param string $name 文件名
     * @param string $table 表名(不含前缀)
     * @param array $field_list 字段列表
     * @param int $limit 批量插入数据库每次的条数，默认100
     * @param bool $del 导入成功后是否删除文件
     * @return bool|string
     */
    protected function importData(string $name, string $table, array $field_list, $limit = 100, $del = true)
    {
        $config = config('filesystem.disks.admin_import');

        $file = request()->file($name);

        try {
            validate($config['validate'])->check([$name => $file]);

            $file_name = Filesystem::disk('admin_import')->putFile($table, $file);
            if ($file_name === false) {
                return '上传文文件失败';
            }
        } catch (ValidateException $e) {
            return $e->getMessage();
        }

        $path = $config['root'] . '/' . $file_name;

        $spreadsheet = IOFactory::load($path);
        $excel_array = $spreadsheet->getActiveSheet()->toArray();
        array_shift($excel_array);

        Db::startTrans();
        try {

            $time     = time();
            $all_data = [];
            foreach ($excel_array as $key => $value) {

                $data = [];
                foreach ($field_list as $field_key => $field_value) {
                    if (!isset($value[$field_key])) {
                        throw new Exception('第 ' . ($key + 2) . ' 行第 ' . $field_key . ' 列缺少数据');
                    }
                    $data[$field_value] = $value[$field_key];
                }
                // 有创建时间和更新时间的处理
                $data['create_time'] = $time;
                $data['update_time'] = $time;

                $all_data[] = $data;
            }


            Db::name($table)
                ->limit($limit)
                ->insertAll($all_data);

            Db::commit();
            $result = true;
        } catch (Exception $exception) {
            Db::rollback();
            $result = '导入失败，信息：' . $exception->getMessage();
        }
        if ($del) {
            unlink($file);
        }

        return $result;
    }

    /**
     * 下载导入模版
     * @param array $field_name_list 字段名称列表
     * @param string $name 文件名
     */
    protected function downloadExample(array $field_name_list, string $name = ''): void
    {
        try {

            if (empty($name)) {
                $name = date('导入模版');
            }

            $spreadsheet = new Spreadsheet();
            $sheet       = $spreadsheet->setActiveSheetIndex(0);
            $char_index  = range('A', 'Z');

            // 处理超过26列
            $a = 'A';
            foreach ($char_index as $item) {
                $char_index[] = $a . $item;
            }

            // Excel 表格头
            foreach ($field_name_list as $key => $val) {
                $sheet->setCellValue($char_index[$key] . '1', $val);
            }

            $spreadsheet->getActiveSheet()->setTitle($name);

            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $name . '.xlsx"');
            header('Cache-Control: max-age=0');

            $objWriter = IOFactory::createWriter($spreadsheet, 'Xlsx');
            $objWriter->save('php://output');
            exit();
        } catch (Exception $e) {
            exit($e->getMessage() . '(' . $e->getFile() . '-' . $e->getLine() . ')');
        }
    }


    /**
     * 简单导出，占用内存低，导出1-10万条数据使用
     *
     * @param $header
     * @param $body
     * @param string $name
     */
    public function exportXlsx($header, $body, $name = ''): void
    {
        if (empty($name)) {
            $name = date('Y-m-d-H-i-s');
        }

        header('Content-disposition: attachment; filename="' . XLSXWriter::sanitize_filename($name) . '.xlsx"');
        header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
        header('Content-Transfer-Encoding: binary');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');

        $writer = new XLSXWriter();
        $writer->writeSheetRow('Sheet1', $header);
        foreach ($body as $row) {
            $row_data = [];
            foreach ($row as $item) {
                $row_data[] = $item;
            }
            $writer->writeSheetRow('Sheet1', $row_data);
        }
        $writer->writeToStdOut();
        exit();
    }

}
