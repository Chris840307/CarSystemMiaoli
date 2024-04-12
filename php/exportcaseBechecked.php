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

//取得下載資料
$sql = "SELECT A.* ,B.SerialNo FROM violation A JOIN parameter_list B ON B.SerialNo = A.SerialNo WHERE A.Datetime BETWEEN '$txt_date1' AND '$txt_date2'";
if ($txt_num != null && $txt_num != 'undefined') {
    $sql = $sql . " AND A.CarNumber = '$txt_num'";
}
if ($txt_cartype != null && $txt_cartype != 'undefined') {
    $sql = $sql . " AND A.carType = '$txt_cartype'";
}
if ($txt_addr != null && $txt_addr != 'undefined') {
    $sql = $sql . " AND A.DetectLocation = '$txt_addr'";
}
$sql = $sql . " AND A.status = '2'";
// echo $sql;
$result = mysqli_query($conn, $sql) or die('MySQL select error' . mysqli_error($conn));

//圖片處理
$Photo_arr = [];
$Time_arr = [];
$i = 0;

if ($result->num_rows > 0) {
    while ($record = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
        //依照日期建立資料夾
        $dirPath = '../export/' . date('Y-m-d', strtotime($record['Datetime']));
        if (!file_exists($dirPath)) {
            mkdir($dirPath, 0777, true);
        }

        //取得主機編號
        $key = array_search($record['DetectLocation'], array_column($jSon['data'], 'addr')) ? array_search($record['DetectLocation'], array_column($jSon['data'], 'addr')) : 'no';
        $SerialNo = $jSon['data'][$key]->SerialNo;

        //圖片處理
        $Photo_arr[$i] = $record['PhotoURL']; //路徑名稱也許要改,放到copy裡面
        $Time_arr[$i] = $record['Datetime'];
        // echo $Photo_arr[$i];
        // $name_carID = preg_split(' /[\\|\\/.,]/', $Photo_arr[$i]);
        $name_carID = $record['CarNumber'];
        $name_Datetime_temp = str_replace('-', '', $Time_arr[$i]);
        $name_Datetime_temp = str_replace(':', '', $name_Datetime_temp);
        $name_Datetime = str_replace(' ', '', $name_Datetime_temp);
        copy($Photo_arr[$i], '../export/' . $dirPath . '/' . $name_Datetime . '_' . $name_carID . '.jpg'); //苗栗路徑不一樣

        /* //txt檔案暫不出現
        //txt寫檔(ANSI編碼)
        $file = fopen('../export/'.$dirPath.'/'.$name_Datetime.'_'.$name_carID.'.txt', 'w'); //開啟txt檔案
        fwrite($file, mb_convert_encoding('證號='."\r\n","big5","utf-8"));
        fwrite($file, mb_convert_encoding('主機='."\r\n","big5","utf-8"));
        fwrite($file, mb_convert_encoding('地點='.$record['DetectLocation']."\r\n","big5","utf-8"));
        fwrite($file, mb_convert_encoding('速限=0km'."\r\n","big5","utf-8"));
        fwrite($file, mb_convert_encoding('違法編號=1'."\r\n","big5","utf-8"));
        fwrite($file, mb_convert_encoding('類型=違規停車'."\r\n","big5","utf-8"));
        fwrite($file, mb_convert_encoding('車道=車道1'."\r\n","big5","utf-8"));
        fwrite($file, mb_convert_encoding('編號='.$record['SerialNo']."\r\n","big5","utf-8"));
        fwrite($file, mb_convert_encoding('日期='.date('Y/m/d', strtotime($record['Datetime']))."\r\n","big5","utf-8"));
        fwrite($file, mb_convert_encoding('時間='.date('H:i:s', strtotime($record['Datetime']))."\r\n","big5","utf-8"));
        fwrite($file, mb_convert_encoding('紅燈=0.0秒'."\r\n","big5","utf-8"));
        fwrite($file, mb_convert_encoding('黃燈=0.0秒'."\r\n","big5","utf-8"));
        fwrite($file, mb_convert_encoding('間隔=0.0秒'."\r\n","big5","utf-8"));
        fwrite($file, mb_convert_encoding('線圈=0cm'."\r\n","big5","utf-8"));
        fwrite($file, mb_convert_encoding('範圍=1'."\r\n","big5","utf-8"));
        fwrite($file, mb_convert_encoding('車牌='.$record['CarNumber']."\r\n","big5","utf-8"));
        fwrite($file, mb_convert_encoding('操作者姓名=系統管理員'."\r\n","big5","utf-8"));
        fwrite($file, mb_convert_encoding('Title_1=test1'."\r\n","big5","utf-8"));
        fwrite($file, mb_convert_encoding('Title_2=test2'."\r\n","big5","utf-8"));
        fwrite($file, mb_convert_encoding('Title_3=test3'."\r\n","big5","utf-8"));
        fclose($file);
        */

        //ini寫檔(ANSI編碼)
        $file_ini = fopen('../export/' . $dirPath . '/' . $name_Datetime . '_' . $name_carID . '.ini', 'w'); //開啟ini檔案
        fwrite($file_ini, mb_convert_encoding('證號=' . "\r\n", "big5", "utf-8"));
        fwrite($file_ini, mb_convert_encoding('主機=' . "\r\n", "big5", "utf-8"));
        fwrite($file_ini, mb_convert_encoding('地點=' . $record['DetectLocation'] . "\r\n", "big5", "utf-8"));
        fwrite($file_ini, mb_convert_encoding('速限=0km' . "\r\n", "big5", "utf-8"));
        fwrite($file_ini, mb_convert_encoding('違法編號=1' . "\r\n", "big5", "utf-8"));
        fwrite($file_ini, mb_convert_encoding('類型=違規停車' . "\r\n", "big5", "utf-8"));
        fwrite($file_ini, mb_convert_encoding('車道=車道1' . "\r\n", "big5", "utf-8"));
        fwrite($file_ini, mb_convert_encoding('編號=' . $record['SerialNo'] . "\r\n", "big5", "utf-8"));
        fwrite($file_ini, mb_convert_encoding('日期=' . date('Y/m/d', strtotime($record['Datetime'])) . "\r\n", "big5", "utf-8"));
        fwrite($file_ini, mb_convert_encoding('時間=' . date('H:i:s', strtotime($record['Datetime'])) . "\r\n", "big5", "utf-8"));
        fwrite($file_ini, mb_convert_encoding('紅燈=0.0秒' . "\r\n", "big5", "utf-8"));
        fwrite($file_ini, mb_convert_encoding('黃燈=0.0秒' . "\r\n", "big5", "utf-8"));
        fwrite($file_ini, mb_convert_encoding('間隔=0.0秒' . "\r\n", "big5", "utf-8"));
        fwrite($file_ini, mb_convert_encoding('線圈=0cm' . "\r\n", "big5", "utf-8"));
        fwrite($file_ini, mb_convert_encoding('範圍=1' . "\r\n", "big5", "utf-8"));
        fwrite($file_ini, mb_convert_encoding('車牌=' . $record['CarNumber'] . "\r\n", "big5", "utf-8"));
        fwrite($file_ini, mb_convert_encoding('操作者姓名=系統管理員' . "\r\n", "big5", "utf-8"));
        fwrite($file_ini, mb_convert_encoding('Title_1=test1' . "\r\n", "big5", "utf-8"));
        fwrite($file_ini, mb_convert_encoding('Title_2=test2' . "\r\n", "big5", "utf-8"));
        fwrite($file_ini, mb_convert_encoding('Title_3=test3' . "\r\n", "big5", "utf-8"));
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

// echo $i;
if ($i != 0) {
    header('Content-type: application/txt');
    header('Content-Transfer-Encoding: binary');
    header('Content-Description: File Transfer');
    header('Cache-Control: must-revalidate');
    header('Location:downloadBechecked.php');
} else {
    header('Location:../noDataBechecked.html');
}
