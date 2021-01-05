<?php

header('content-type:text/html;charset=utf-8');

$dbhost = 'localhost';
$dbuser = 'root';
$dbpass = '2u6u/ru8';
$dbname = 'car';

$txt_date1 = $_GET['txt_date1'];
$txt_date2 = $_GET['txt_date2'];
$txt_date2 = date('Y-m-d', strtotime('+1 day', strtotime($txt_date2)));

$conn = mysqli_connect($dbhost, $dbuser, $dbpass) or die('Error with MySQL connection'.mysql_error());

mysqli_query($conn, 'SET NAMES utf8');
mysqli_select_db($conn, $dbname);

//標題
$csv_export = '日期,';
//取得總共有幾個Location
$sql = 'SELECT `DetectLocation` FROM `violation` GROUP BY `DetectLocation`';
$result = mysqli_query($conn, $sql) or die('MySQL select error'.mysqli_error($conn));
if ($result->num_rows > 0) {
    while ($record = mysqli_fetch_array($result)) {
        $csv_export .= $record['DetectLocation'].',';
    }
}
$csv_export .= "總數\n";
$csv_export .= '';

while (true) {
    if ($txt_date1 != $txt_date2) {
        //取得總共有幾個Location
        $sql2 = "SELECT `DetectLocation` FROM `violation` WHERE `status`='2' GROUP BY `DetectLocation`";
        $result2 = mysqli_query($conn, $sql2) or die('MySQL select error'.mysqli_error($conn));
        if ($result2->num_rows > 0) {
            //塞資料
            $csv_export .= $txt_date1.',';

            $sum = 0;
            while ($record2 = mysqli_fetch_array($result2)) {
                //取得該Location數量
                $DetectLocation = $record2['DetectLocation'];
                $sql = "SELECT count(*) AS count FROM `violation` WHERE `Datetime` BETWEEN '$txt_date1 00:00:00' AND '$txt_date1 23:59:59' AND `DetectLocation`='$DetectLocation' AND `status`='2'";
                // echo $sql;
                $result = mysqli_query($conn, $sql) or die('MySQL select error'.mysqli_error($conn));
                if ($result->num_rows > 0) {
                    $record = mysqli_fetch_array($result);

                    $csv_export .= $record['count'].',';

                    $sum = $sum + $record['count'];
                }
            }

            $csv_export .= $sum."\n";
            $csv_export .= '';
        }
        $txt_date1 = date('Y-m-d', strtotime('+1 day', strtotime($txt_date1)));
    // echo  $txt_date1;
    } else {
        break;
    }
}

// while (true) {
//     if ($txt_date1 != $txt_date2) {
//         $sql = "SELECT count(*) AS count FROM `violation` WHERE `Datetime` BETWEEN '$txt_date1 00:00:00' AND '$txt_date1 23:59:59'";
//         $result = mysqli_query($conn, $sql) or die('MySQL select error'.mysqli_error($conn));

//         //產生csv
//         if ($result->num_rows > 0) {
//             while ($record = mysqli_fetch_array($result)) {
//                 //塞資料
//                 $csv_export .= $txt_date1.','.$record['count']."\n";
//             }
//             $csv_export .= '';
//         }
//         $txt_date1 = date('Y-m-d', strtotime('+1 day', strtotime($txt_date1)));
//     // echo  $txt_date1;
//     } else {
//         break;
//     }
// }

   echo chr(0xEF).chr(0xBB).chr(0xBF);  // 解决乱码
   echo $csv_export;
