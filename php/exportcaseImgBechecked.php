<?php

date_default_timezone_set('Asia/Taipei');

//若export資料夾不存在則建立
$dirPath = '../export';
if (!file_exists($dirPath)) {
    mkdir($dirPath, 0777, true);
}

$dbhost = 'localhost';
$dbuser = 'root';
$dbpass = '2u6u/ru8';
$dbname = 'car';

$txt_date1 = $_POST['txt_date1'];
$txt_date2 = $_POST['txt_date2'];
$txt_num = $_POST['txt_num'];
$txt_cartype = $_POST['txt_cartype'];
$txt_addr = $_POST['txt_addr'];

$conn = mysqli_connect($dbhost, $dbuser, $dbpass) or die('Error with MySQL connection');

mysqli_query($conn, 'SET NAMES utf8');
mysqli_select_db($conn, $dbname);

//判斷日期是否不一樣
// $sql="SELECT DATE_FORMAT(`Datetime`, '%Y%m%d') AS `Datetime` FROM `violation` WHERE `Datetime` BETWEEN '$txt_date1' AND '$txt_date2' AND `status`='2' GROUP BY DATE_FORMAT(`Datetime`, '%Y%m%d')";

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
$sql = $sql . " AND `status` = '2'";
$result = mysqli_query($conn, $sql) or die('MySQL select error' . mysqli_error($conn));

//圖片處理
$Photo_arr = [];
$Time_arr = [];
$i = 0;

while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
    if ($result->num_rows > 0) {
        //依照日期建立資料夾
        $dirPath = '../export/' . date('Y-m-d', strtotime($row['Datetime']));
        if (!file_exists($dirPath)) {
            mkdir($dirPath, 0777, true);
        }

        $Photo_arr[$i] = $row['PhotoURL']; //路徑名稱也許要改,放到copy裡面
        $Time_arr[$i] = $row['Datetime'];
        // echo $Photo_arr[$i];
        $name_carID = preg_split(' /[\\|\\/.,]/', $Photo_arr[$i]);
        $name_Datetime_temp = str_replace('-', '', $Time_arr[$i]);
        $name_Datetime_temp = str_replace(':', '', $name_Datetime_temp);
        $name_Datetime = str_replace(' ', '', $name_Datetime_temp);
        // copy("../" . $Photo_arr[$i], "../export/" . $name_Datetime."_". $name_carID[sizeof($name_carID) - 3] . ".jpg"); //複製到開創的資料夾
        copy($Photo_arr[$i], '../export/' . $dirPath . '/' . $name_Datetime . '_' . $name_carID[sizeof($name_carID) - 3] . '.jpg'); //苗栗路徑不一樣

        ++$i;
    }
}

//建立壓縮檔
//$rootPath = realpath("/var/www/html/CarSystem/export");
$rootPath = realpath('../export');

// Initialize archive object
$zip = new ZipArchive();

$r = $zip->open('/var/www/html/CarSystem/已開單案件.zip', ZipArchive::CREATE | ZipArchive::OVERWRITE);
var_dump($r);

// Create recursive directory iterator
/** @var SplFileInfo[] $files */
$files = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($rootPath),
    RecursiveIteratorIterator::LEAVES_ONLY
);
// print_r($files);

foreach ($files as $name => $file) {
    // Skip directories (they would be added automatically)
    if (!$file->isDir()) {
        // Get real and relative path for current file
        $filePath = $file->getRealPath();
        $relativePath = substr($filePath, strlen($rootPath) + 1);

        // Add current file to archive
        $r = $zip->addFile($filePath, $relativePath);
        //    var_dump($r);
    }
}

// Zip archive will be created only after closing object
$r = $zip->close();
// var_dump($r);//檢查用

echo $i;
if ($i != 0) {
    header('Location:download.php');
} else {
    header('Location:../noDataBechecked.html');
}
