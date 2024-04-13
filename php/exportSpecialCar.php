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


$sql = "SELECT * FROM `violation` WHERE `Datetime` BETWEEN '$txt_date1' AND '$txt_date2'";
if ($txt_num != null && $txt_num != 'undefined') {
    $sql = $sql . " AND `CarNumber` = '$txt_num'";
}
if ($txt_cartype != null && $txt_cartype != 'undefined') {
    $sql = $sql . " AND `carType` = '$txt_cartype'";
}
if ($txt_addr != null && $txt_addr != 'undefined') {
    $sql = $sql . " AND `DetectLocation` = '$txt_addr'";
}
$sql = $sql . " AND `status` = '3'";
$result = mysqli_query($conn, $sql) or die('MySQL select error' . mysqli_error($conn));


// 创建新的 Spreadsheet 对象
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// 设置工作表的名称
$sheet->setTitle('違規清冊資料');

// 添加数据
$content_arr = [];
$data = [
    ["【表單名稱】：", "違規件數資料清冊"],
    ["【違規日期區間】：", $f_txt_date1, "~", $f_txt_date2],
    ["編號", "違規時間", "違規地點", "車號", "違規類型", "不舉發理由", "違規圖片顯示", "審查人員", "備註"],
    $content_arr
];
$i = 1;
if ($result->num_rows > 0) {
    while ($record = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
        $content_arr[0] = $i;
        $cDate = new DateTime($record['Datetime']);  // 解析日期字符串
        $content_arr[1] = $cDate->format("Y/m/d H:i:s");  // 格式化日期
        $content_arr[2] = $record['DetectLocation'];
        $content_arr[3] = $record['CarNumber'];
        switch ($record['Law']) {
            case '5610104':
                $cLaw = '在禁止臨時停車處所停車';
                break;
            case '5610105':
                $cLaw = '在公共汽車招呼站十公尺內停車';
                break;
            case '5610204':
                $cLaw = '在槽化線停車';
                break;
            case '5610501':
                $cLaw = '在顯有妨礙他車通行處所停車';
                break;
            case '5610904':
                $cLaw = '停車車種不依規定';
                break;
            case '5620002':
                $cLaw = '併排停車（104年7月1日以後適用）';
                break;
            case '7410402':
                $cLaw = '微型電動二輪車，不依規定停放車輛';
                break;
            case '5610402':
                $cLaw = '在設有禁止停車標誌、標線之處所停車';
                break;
            case '違規停車':
                $cLaw = '違規停車';
                break;
        }
        $content_arr[4] = $cLaw;
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
        $content_arr[5] = $cResult;
        $content_arr[7] = $record['people'];
        $content_arr[8] = $record['remark'];

        $data[$i + 2] = $content_arr; //從第三行開始

        // G3開始依序插入图片
        $imgIndex = $i + 3;
        $drawing = new Drawing();
        $drawing->setName('Image ' . $imgIndex);
        $drawing->setPath($record['PhotoURL']);  // 图片路径
        $drawing->setHeight(80);  // 图片高度
        $drawing->setCoordinates('G' . $imgIndex); // 图片放在每行的G列
        $drawing->setWorksheet($sheet);


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

        // 加粗第三行
        if ($rowKey == 2) {
            $cell->getStyle()->getFont()->setBold(true);
        }

        // 设置行高
        $index = $rowKey + 1;
        if ($rowKey > 2) {
            $sheet->getRowDimension($index)->setRowHeight(60);
        }
    }
}

// 自动调整列宽
foreach (range('A', 'I') as $columnID) {
    $sheet->getColumnDimension($columnID)->setAutoSize(true);
}

// 创建 Excel 文件写入器
$writer = new Xlsx($spreadsheet);

// 设置 HTTP 头信息用于下载
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="特種車輛.xlsx"');

// 直接输出到浏览器
if ($i != 1) {
    $writer->save('php://output');
} else {
    header('Location:../noDataSpecialCar.html');
}

exit;
