<?php

error_reporting(E_ERROR | E_WARNING | E_PARSE);
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=utf-8');

$dbhost = 'localhost';
$dbuser = 'root';
$dbpass = '2u6u/ru8';
$dbname = 'car';

$str = file_get_contents('php://input');
$str_json = json_decode($str);
$txt_date1 = $str_json->{'txt_date1'};
$txt_date2 = $str_json->{'txt_date2'};
$txt_date1 = date('Y-m-d 00:00:00', strtotime($txt_date1));
$txt_date2 = date('Y-m-d 23:59:59', strtotime($txt_date2));
$txt_date_hour = '';
$diff_hour = (strtotime($txt_date2) - strtotime($txt_date1)) / (60 * 60); //計算相差之小時數

$conn = mysqli_connect($dbhost, $dbuser, $dbpass) or die('Error with MySQL connection');

mysqli_query($conn, 'SET NAMES utf8');
mysqli_select_db($conn, $dbname);

$jSon['data'] = [];
$jSon['location'] = [];

//取得總共有幾個Location
$sql = 'SELECT `DetectLocation` FROM `violation` GROUP BY `DetectLocation`';
$result = mysqli_query($conn, $sql) or die('MySQL select error'.mysqli_error($conn));
if ($result->num_rows > 0) {
    while ($record = mysqli_fetch_array($result)) {
        $data_t = new stdClass();

        $data_t->DetectLocation = $record['DetectLocation'];

        array_push($jSon['location'], $data_t);
    }
}

for ($i = 0; $i < $diff_hour; ++$i) {
    $txt_date_hour = date('Y-m-d H:i:s', strtotime('+'.$i.' hour', strtotime($txt_date1)));
    $txt_date_hour_end = date('Y-m-d H:59:59', strtotime('+'.$i.' hour', strtotime($txt_date1)));

    //取得總共有幾個Location
    $sql2 = "SELECT `DetectLocation` FROM `violation` WHERE `status`='2' GROUP BY `DetectLocation`";
    $result2 = mysqli_query($conn, $sql2) or die('MySQL select error'.mysqli_error($conn));
    if ($result2->num_rows > 0) {
        $data_t = new stdClass();
        $data_t->date = $txt_date1;
        if (date('H:i:s', strtotime($txt_date_hour)) === '00:00:00') {
            $data_t->date = date('Y-m-d H:i:s', strtotime($txt_date_hour));
        } else {
            $data_t->date = date('H:i:s', strtotime($txt_date_hour));
        }
        $data_t->datetime = date('Y-m-d H:i:s', strtotime($txt_date_hour));
        $data_t->day = date('Y-m-d', strtotime($txt_date_hour));

        $sum = 0;
        while ($record2 = mysqli_fetch_array($result2)) {
            //取得該Location數量
            $DetectLocation = $record2['DetectLocation'];
            $sql = "SELECT count(*) AS count FROM `violation` WHERE `Datetime` BETWEEN '$txt_date_hour' AND '$txt_date_hour_end' AND `DetectLocation`='$DetectLocation' AND `status`='2'";
            // echo $sql;
            $result = mysqli_query($conn, $sql) or die('MySQL select error'.mysqli_error($conn));

            if ($result->num_rows > 0) {
                $record = mysqli_fetch_array($result);

                $DetectLocation = $record2['DetectLocation'];
                $data_t->$DetectLocation = $record['count'];

                $sum = $sum + $record['count'];
            }
        }
        $data_t->sum = $sum;
        array_push($jSon['data'], $data_t);
    }
}

    echo json_encode(['data' => $jSon['data'], 'location' => $jSon['location']]);
