<?php
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Style\Font;

date_default_timezone_set('Asia/Taipei');

$dbhost = 'localhost';
$dbuser = 'root';
$dbpass = '2u6u/ru8';
$dbname = 'car';

$txt_date1 = $_POST['txt_date1'];
$temp_date1 = new DateTime($txt_date1);  // 解析日期字符串
$f_txt_date1 = $temp_date1->format("Y/m/d H:i:s");  // 格式化日期
$txt_date2 = $_POST['txt_date2'];
$temp_date2 = new DateTime($txt_date2);  // 解析日期字符串
$f_txt_date2 = $temp_date2->format("Y/m/d H:i:s");  // 格式化日期
$txt_num = $_POST['txt_num'];
$txt_cartype = $_POST['txt_cartype'];
$txt_addr = $_POST['txt_addr'];

$conn = mysqli_connect($dbhost, $dbuser, $dbpass) or die('Error with MySQL connection');

mysqli_query($conn, 'SET NAMES utf8');
mysqli_select_db($conn, $dbname);


$sql = "SELECT * FROM `violation` JOIN `car_type` ON violation.carType = car_type.value WHERE violation.Datetime BETWEEN '$txt_date1' AND '$txt_date2'";
if ($txt_num != null && $txt_num != 'undefined') {
    $sql = $sql . " AND violation.CarNumber = '$txt_num'";
}
if ($txt_cartype != null && $txt_cartype != 'undefined') {
    $sql = $sql . " AND violation.carType = '$txt_cartype'";
}
if ($txt_addr != null && $txt_addr != 'undefined') {
    $sql = $sql . " AND violation.DetectLocation = '$txt_addr'";
}
$sql = $sql . " AND violation.status = '3'";
$result = mysqli_query($conn, $sql) or die('MySQL select error' . mysqli_error($conn));
// echo $sql;

// 创建新的 Spreadsheet 对象
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// 设置工作表的名称
$sheet->setTitle('不舉發單明細');

// 添加数据
$content_arr = [];
$data = [
    ["ID", "日期時間", "車牌號碼", "偵測地點名稱", "停放位置", "車種", "不舉發理由", "狀態", "備註"],
    $content_arr
];
$i = 1;
if ($result->num_rows > 0) {
    while ($record = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
        $content_arr[0] = $record['RowNo'];
        $cDate = new DateTime($record['Datetime']);  // 解析日期字符串
        $content_arr[1] = $cDate->format("Y/m/d H:i:s");  // 格式化日期
        $content_arr[2] = $record['CarNumber'];
        $content_arr[3] = $record['Location'];
        $content_arr[4] = $record['DetectLocation'];
        $content_arr[5] = $record['type'];
        switch ($record['result']) {
            case 0:
                $cResult = '未選擇';
                break;
            case 1:
                $cResult = '判別車號不完整';
                break;
            case 2:
                $cResult = '車號模糊無法辨識';
                break;
            case 3:
                $cResult = '特種車輛執行公務';
                break;
            case 4:
                $cResult = '車牌遮蔽無法辨識';
                break;
            case 5:
                $cResult = '車輛駛離無法辨識';
                break;
            case 6:
                $cResult = '工程、警用、救護車輛';
                break;
        }
        $content_arr[6] = $cResult;
        $content_arr[7] = "不舉發單";
        $content_arr[8] = $record['remark'];

        $data[$i] = $content_arr;
        ++$i;
    }
}

// 填充数据并设置样式
foreach ($data as $rowKey => $row) {
    foreach ($row as $columnKey => $value) {
        $cell = $sheet->getCellByColumnAndRow($columnKey + 1, $rowKey + 1);
        $cell->setValue($value);

        // 获取当前单元格坐标
        $coordinate = $sheet->getCellByColumnAndRow($columnKey + 1, $rowKey + 1)->getCoordinate();

        // 设置字体为新細明體和字体大小
        $styleArray = [
            'font' => [
                'name' => '新細明體',
                'size' => 14,
            ],
            // 'borders' => [
            //     'allBorders' => [
            //         'borderStyle' => Border::BORDER_THIN,
            //     ],
            // ],
        ];
        $cell->getStyle()->applyFromArray($styleArray);

        // 设置边框
        // $cell->getStyle()->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        // if ($rowKey > 2 && $columnKey == 6) {
        //     $sheet->getStyle($coordinate)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        // }
    }
}

// 自动调整列宽
foreach (range('A', 'H') as $columnID) {
    $sheet->getColumnDimension($columnID)->setAutoSize(true);
}

// 创建 Excel 文件写入器
$writer = new Xlsx($spreadsheet);

// 设置 HTTP 头信息用于下载
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="不舉發單明細.xlsx"');

// 直接输出到浏览器
if ($i != 1) {
    $writer->save('php://output');
} else {
    header('Location:../noDataUncheckList.html');
}

exit;
