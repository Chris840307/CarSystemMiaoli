<?php
date_default_timezone_set("Asia/Taipei");





$dbhost = 'localhost';
$dbuser = 'root';
$dbpass = '2u6u/ru8';
$dbname = 'car';



$conn = mysqli_connect($dbhost, $dbuser, $dbpass) or die('Error with MySQL connection' . mysql_error());

mysqli_query($conn, 'SET NAMES utf8');
mysqli_select_db($conn, $dbname);



$sql = "SELECT `RowNo`, `Datetime`,`CarNumber`,`Location`,`PhotoURL`,`DetectLocation`,`LogDate` FROM `violation` WHERE `status`=2 ";
$result = mysqli_query($conn, $sql) or die('MySQL select error' . mysqli_error($conn));







//圖片處理

$Photo_arr = array();
$i = 0;

while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
    if (strtotime($row[LogDate]) >= strtotime($_POST['txt_date1']) && strtotime($row[LogDate]) <= strtotime($_POST['txt_date2'])) {
        $Photo_arr[$i] = $row['PhotoURL']; //路徑名稱也許要改,放到copy裡面
        // echo $Photo_arr[$i];
        $name = preg_split(" /[\\|\\/.,-]/", $Photo_arr[$i]);
        copy("../" . $Photo_arr[$i], "../export/" . $name[sizeof($name) - 2] . ".jpg"); //複製到開創的資料夾
       


        //寫入txt檔
        $file = fopen("../export/" . $name[sizeof($name) - 2] . ".txt ", "w"); //開啟txt檔案
        $file_ini = fopen("../export/" . $name[sizeof($name) - 2] . ".ini ", "w"); //開啟ini檔案


        //txt寫檔
        fwrite($file, "證號=" . "\n");
        fwrite($file, "主機=" . "\n");
        fwrite($file, "地點=$row[Location]" . "\n");
        fwrite($file, "地點=$row[DetectLocation]" . "\n");
        fwrite($file, "速限=" . "\n");
        fwrite($file, "路口=" . "\n");
        fwrite($file, "編號=" . "\n");
        fwrite($file, "類型=" . "\n");
        fwrite($file, "日期=$row[Datetime]" . "\n");
        fwrite($file, "時間=$row[Datetime]" . "\n");
        fwrite($file, "車速=" . "\n");
        fwrite($file, "方向=" . "\n");
        fwrite($file, "車道=" . "\n");
        fwrite($file, "操作者姓名=" . "\n");
        fwrite($file, "Title_1=" . "\n");
        fwrite($file, "Title_2=" . "\n");
        fwrite($file, "Title_3=" . "\n");
        fwrite($file, "編號=$row[RowNo]" . "\n");
        fwrite($file, "車牌=$row[CarNumber]" . "\n");
        fwrite($file, "處理時間=$row[LogDate]" . "\n");
        fclose($file);

        //ini寫檔
        fwrite($file_ini, "證號=" . "\n");
        fwrite($file_ini, "主機=" . "\n");
        fwrite($file_ini, "地點=$row[Location]" . "\n");
        fwrite($file_ini, "地點=$row[DetectLocation]" . "\n");
        fwrite($file_ini, "速限=" . "\n");
        fwrite($file_ini, "路口=" . "\n");
        fwrite($file_ini, "編號=" . "\n");
        fwrite($file_ini, "類型=" . "\n");
        fwrite($file_ini, "日期=$row[Datetime]" . "\n");
        fwrite($file_ini, "時間=$row[Datetime]" . "\n");
        fwrite($file_ini, "車速=" . "\n");
        fwrite($file_ini, "方向=" . "\n");
        fwrite($file_ini, "車道=" . "\n");
        fwrite($file_ini, "操作者姓名=" . "\n");
        fwrite($file_ini, "Title_1=" . "\n");
        fwrite($file_ini, "Title_2=" . "\n");
        fwrite($file_ini, "Title_3=" . "\n");
        fwrite($file_ini, "編號=$row[RowNo]" . "\n");
        fwrite($file_ini, "車牌=$row[CarNumber]" . "\n");
        fwrite($file_ini, "處理時間=$row[LogDate]" . "\n");
        fclose($file_ini);

        $i++;
    }
    
}


//建立壓縮檔
//$rootPath = realpath("/var/www/html/CarSystem/export");
$rootPath = realpath("../export");


// Initialize archive object
$zip = new ZipArchive();

$r = $zip->open("/var/www/html/CarSystem/已開單案件.zip", ZipArchive::CREATE | ZipArchive::OVERWRITE);
var_dump($r);


// Create recursive directory iterator
/**  @var SplFileInfo[] $files */
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
        $r =  $zip->addFile($filePath, $relativePath);
        //    var_dump($r);
    }
}

// Zip archive will be created only after closing object
$r = $zip->close();
// var_dump($r);//檢查用


echo $i;
if($i!=0){
header('Location:download.php');
}
else{
    header('Location:../bechecked.html');
}

?>