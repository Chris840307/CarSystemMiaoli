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

$conn = mysqli_connect($dbhost, $dbuser, $dbpass) or die('Error with MySQL connection');

mysqli_query($conn, 'SET NAMES utf8');
mysqli_select_db($conn, $dbname);

$jSon['data'] = [];

    $sql = "SELECT `carType`,COUNT(`carType`) AS count FROM `violation` WHERE `Datetime` BETWEEN '$txt_date1' AND '$txt_date2' AND `status`='2' GROUP BY `carType`";
    // echo $sql;
    $result = mysqli_query($conn, $sql) or die('MySQL select error'.mysqli_error($conn));

    if ($result->num_rows > 0) {
        while ($record = mysqli_fetch_array($result)) {
            $data_t = new stdClass();

            $data_t->carTypeVol = $record['carType'];
            switch ($record['carType']) {
                case 1:
                    $data_t->carType = '機車';
                    break;
                case 2:
                    $data_t->carType = '汽車';
                    break;
            }
            $data_t->count = $record['count'];

            array_push($jSon['data'], $data_t);
        }
    }

    echo json_encode(['data' => $jSon['data']]);
