<?php

header('content-type:text/html;charset=utf-8');

$dbhost = 'localhost';
$dbuser = 'root';
$dbpass = '2u6u/ru8';
$dbname = 'car';

$txt_date1 = $_GET['txt_date1'];
// $txt_date2 = $_GET['txt_date2'];
$txt_date1 = date('Y-m-d 00:00:00', strtotime($txt_date1));
// $txt_date2 = date('Y-m-d 23:59:59', strtotime($txt_date2));
$txt_date2 = date('Y-m-d 23:59:59', strtotime($txt_date1));
$txt_date_hour = '';
$diff_hour = (strtotime($txt_date2) - strtotime($txt_date1)) / (60 * 60); //計算相差之小時數

$conn = mysqli_connect($dbhost, $dbuser, $dbpass) or die('Error with MySQL connection'.mysql_error());

mysqli_query($conn, 'SET NAMES utf8');
mysqli_select_db($conn, $dbname);

//標題
$csv_export = '時間,';
//取得總共有幾個Location
$sql = 'SELECT `Location` FROM `violation` GROUP BY `Location`';
$result = mysqli_query($conn, $sql) or die('MySQL select error'.mysqli_error($conn));
if ($result->num_rows > 0) {
    while ($record = mysqli_fetch_array($result)) {
        $csv_export .= $record['Location'].',';
    }
}
$csv_export .= "總數\n";
$csv_export .= '';

for ($i = 0; $i < $diff_hour; ++$i) {
    $txt_date_hour = date('Y-m-d H:i:s', strtotime('+'.$i.' hour', strtotime($txt_date1)));
    $txt_date_hour_end = date('Y-m-d H:59:59', strtotime('+'.$i.' hour', strtotime($txt_date1)));

    //取得總共有幾個Location
    $sql2 = 'SELECT `Location` FROM `violation` GROUP BY `Location`';
    $result2 = mysqli_query($conn, $sql2) or die('MySQL select error'.mysqli_error($conn));
    if ($result2->num_rows > 0) {
        //塞資料
        $csv_export .= date('H:i:s', strtotime($txt_date_hour)).',';

        $sum = 0;
        while ($record2 = mysqli_fetch_array($result2)) {
            //取得該Location數量
            $Location = $record2['Location'];
            $sql = "SELECT count(*) AS count FROM `violation` WHERE `Datetime` BETWEEN '$txt_date_hour' AND '$txt_date_hour_end' AND `Location`='$Location'";
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
}

// for ($i = 0; $i < $diff_hour; ++$i) {
//     $txt_date_hour = date('Y-m-d H:i:s', strtotime('+'.$i.' hour', strtotime($txt_date1)));
//     $txt_date_hour_end = date('Y-m-d H:59:59', strtotime('+'.$i.' hour', strtotime($txt_date1)));

//     $sql = "SELECT count(*) AS count FROM `violation` WHERE `Datetime` BETWEEN '$txt_date_hour' AND '$txt_date_hour_end'";
//     // echo $sql;
//     $result = mysqli_query($conn, $sql) or die('MySQL select error'.mysqli_error($conn));

//     //產生csv
//     if ($result->num_rows > 0) {
//         while ($record = mysqli_fetch_array($result)) {
//             //塞資料
//             $csv_export .= $txt_date_hour.','.$record['count']."\n";
//         }
//         $csv_export .= '';
//     }
// }

   echo chr(0xEF).chr(0xBB).chr(0xBF);  // 解决乱码
   echo $csv_export;
