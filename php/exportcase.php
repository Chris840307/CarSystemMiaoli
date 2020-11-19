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
$txt_law = $_POST['txt_law'];
$txt_addr = $_POST['txt_addr'];

$conn = mysqli_connect($dbhost, $dbuser, $dbpass) or die('Error with MySQL connection'.mysql_error());

mysqli_query($conn, 'SET NAMES utf8');
mysqli_select_db($conn, $dbname);

//取得下載資料
$sql = "SELECT A.* ,B.SerialNo FROM violation A JOIN parameter_list B ON B.addr = A.DetectLocation WHERE A.Datetime BETWEEN '$txt_date1' AND '$txt_date2'";
if ($txt_num != null && $txt_num != 'undefined') {
    $sql = $sql." AND A.CarNumber = '$txt_num'";
}
if ($txt_cartype != null && $txt_cartype != 'undefined') {
    $sql = $sql." AND A.carType = '$txt_cartype'";
}
if ($txt_law != null && $txt_law != 'undefined') {
    $sql = $sql." AND A.Law = '$txt_law'";
}
if ($txt_addr != null && $txt_addr != 'undefined') {
    $sql = $sql." AND A.DetectLocation = '$txt_addr'";
}
$sql = $sql." AND A.status = '2'";
// echo $sql;
$result = mysqli_query($conn, $sql) or die('MySQL select error'.mysqli_error($conn));

//圖片處理
$Photo_arr = [];
$Time_arr = [];
$i = 0;

while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
    if ($result->num_rows > 0) {
        //依照日期建立資料夾
        $dirPath = '../export/'.date('Y-m-d', strtotime($row['Datetime']));
        if (!file_exists($dirPath)) {
            mkdir($dirPath, 0777, true);
        }

        //取得主機編號
        $key = array_search($row['DetectLocation'], array_column($jSon['data'], 'addr')) ? array_search($row['DetectLocation'], array_column($jSon['data'], 'addr')) : 'no';
        $SerialNo = $jSon['data'][$key]->SerialNo;

        //圖片處理
        $Photo_arr[$i] = $row['PhotoURL']; //路徑名稱也許要改,放到copy裡面
        $Time_arr[$i] = $row['Datetime'];
        // echo $Photo_arr[$i];
        // $name_carID = preg_split(' /[\\|\\/.,]/', $Photo_arr[$i]);
        $name_carID = $row['CarNumber'];
        $name_Datetime_temp = str_replace('-', '', $Time_arr[$i]);
        $name_Datetime_temp = str_replace(':', '', $name_Datetime_temp);
        $name_Datetime = str_replace(' ', '', $name_Datetime_temp);
        copy($Photo_arr[$i], '../export/'.$dirPath.'/'.$name_Datetime.'_'.$name_carID.'.jpg'); //苗栗路徑不一樣

        //寫入txt檔
        $file = fopen('../export/'.$dirPath.'/'.$name_Datetime.'_'.$name_carID.'.txt ', 'w'); //開啟txt檔案
        $file_ini = fopen('../export/'.$dirPath.'/'.$name_Datetime.'_'.$name_carID.'.ini ', 'w'); //開啟ini檔案

        //txt寫檔
        fwrite($file, '證號='."\n");
        fwrite($file, '主機='."\n");
        fwrite($file, '地點='.$row['DetectLocation']."\n");
        fwrite($file, '速限='."\n");
        fwrite($file, '路口='."\n");
        fwrite($file, '編號='.$row['SerialNo']."\n");
        fwrite($file, '類型=違規停車'."\n");
        fwrite($file, '日期='.date('Y/m/d', strtotime($row['Datetime']))."\n");
        fwrite($file, '時間='.date('H:i:s', strtotime($row['Datetime']))."\n");
        fwrite($file, '車速='."\n");
        fwrite($file, '方向='."\n");
        fwrite($file, '車道='."\n");
        fwrite($file, '操作者姓名=交通員警'."\n");
        fwrite($file, 'Title_1='."\n");
        fwrite($file, 'Title_2='."\n");
        fwrite($file, 'Title_3='."\n");
        // fwrite($file, '車牌='.$row['CarNumber']."\n");
        fclose($file);

        //ini寫檔
        fwrite($file_ini, '證號='."\n");
        fwrite($file_ini, '主機='."\n");
        fwrite($file_ini, '地點='.$row['DetectLocation']."\n");
        fwrite($file_ini, '速限='."\n");
        fwrite($file_ini, '路口='."\n");
        fwrite($file_ini, '編號='.$row['SerialNo']."\n");
        fwrite($file_ini, '類型=違規停車'."\n");
        fwrite($file_ini, '日期='.date('Y/m/d', strtotime($row['Datetime']))."\n");
        fwrite($file_ini, '時間='.date('H:i:s', strtotime($row['Datetime']))."\n");
        fwrite($file_ini, '車速='."\n");
        fwrite($file_ini, '方向='."\n");
        fwrite($file_ini, '車道='."\n");
        fwrite($file_ini, '操作者姓名=交通員警'."\n");
        fwrite($file_ini, 'Title_1='."\n");
        fwrite($file_ini, 'Title_2='."\n");
        fwrite($file_ini, 'Title_3='."\n");
        // fwrite($file_ini, '車牌='.$row['CarNumber']."\n");
        fclose($file_ini);

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
    header('Location:../bechecked.html');
}
