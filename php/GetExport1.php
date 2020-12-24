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

$txt_date2 = date('Y-m-d', strtotime('+1 day', strtotime($txt_date2)));
// echo(strtotime($time1) - strtotime($time2)) / (60 * 60 * 24); //計算相差之天數

$conn = mysqli_connect($dbhost, $dbuser, $dbpass) or die('Error with MySQL connection'.mysql_error());

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

while (true) {
    if ($txt_date1 != $txt_date2) {
        //取得總共有幾個Location
        $sql2 = 'SELECT `DetectLocation` FROM `violation` GROUP BY `DetectLocation`';
        $result2 = mysqli_query($conn, $sql2) or die('MySQL select error'.mysqli_error($conn));
        if ($result2->num_rows > 0) {
            $data_t = new stdClass();
            $data_t->date = $txt_date1;

            $sum = 0;
            while ($record2 = mysqli_fetch_array($result2)) {
                //取得該Location數量
                $DetectLocation = $record2['DetectLocation'];
                $sql = "SELECT count(*) AS count FROM `violation` WHERE `Datetime` BETWEEN '$txt_date1 00:00:00' AND '$txt_date1 23:59:59' AND `DetectLocation`='$DetectLocation'";
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
        $txt_date1 = date('Y-m-d', strtotime('+1 day', strtotime($txt_date1)));
    // echo  $txt_date1;
    } else {
        break;
    }
}

    echo json_encode(['data' => $jSon['data'], 'location' => $jSon['location']]);
