<?php

header('content-type:text/html;charset=utf-8');

$dbhost = 'localhost';
$dbuser = 'root';
$dbpass = '2u6u/ru8';
$dbname = 'car';

$txt_date1 = $_GET['txt_date1'];
$txt_date2 = $_GET['txt_date2'];
$txt_date1 = date('Y-m-d 00:00:00', strtotime($txt_date1));
$txt_date2 = date('Y-m-d 23:59:59', strtotime($txt_date2));

$conn = mysqli_connect($dbhost, $dbuser, $dbpass) or die('Error with MySQL connection'.mysql_error());

mysqli_query($conn, 'SET NAMES utf8');
mysqli_select_db($conn, $dbname);

//標題
$csv_export = '車種,次數'."\n";
$csv_export .= '';

$sql = "SELECT `carType`,COUNT(`carType`) AS count FROM `violation` WHERE `Datetime` BETWEEN '$txt_date1' AND '$txt_date2' GROUP BY `carType`";
// echo $sql;
$result = mysqli_query($conn, $sql) or die('MySQL select error'.mysqli_error($conn));

//產生csv
if ($result->num_rows > 0) {
    while ($record = mysqli_fetch_array($result)) {
        //塞資料
        switch ($record['carType']) {
            case 1:
                $carType = '機車';
                break;
            case 2:
                $carType = '汽車';
                break;
        }
        $csv_export .= $carType.','.$record['count']."\n";
    }
    $csv_export .= '';
}

   echo chr(0xEF).chr(0xBB).chr(0xBF);  // 解决乱码
   echo $csv_export;
