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
$txt_num = $str_json->{'txt_num'};
$status = $str_json->{'status'};

$conn = mysqli_connect($dbhost, $dbuser, $dbpass) or die('Error with MySQL connection'.mysql_error());

mysqli_query($conn, 'SET NAMES utf8');
mysqli_select_db($conn, $dbname);

$jSon['data'] = [];

$sql = "SELECT * FROM `white_list` WHERE `CarNumber` != ''";
if ($txt_num != null && $txt_num != 'undefined') {
    $sql = $sql." AND `CarNumber` = '$txt_num'";
}
if ($status != null && $status != 'undefined') {
    $sql = $sql." AND `status` = '$status'";
}
$sql = $sql.' ORDER BY CAST(`id` AS UNSIGNED INTEGER) ASC';
$result = mysqli_query($conn, $sql) or die('MySQL select error'.mysqli_error($conn));

if ($result->num_rows > 0) {
    while ($record = mysqli_fetch_array($result)) {
        $data_t = new stdClass();

        $data_t->id = $record['id'];
        $data_t->carNumber = $record['carNumber'];
        $data_t->startTime = $record['startTime'];
        $data_t->endTime = $record['endTime'];
        $data_t->statusVol = $record['status'];
        switch ($record['status']) {
            case 0:
                $data_t->status = '不啟用';
                break;
            case 1:
                $data_t->status = '啟用';
                break;
        }

        array_push($jSon['data'], $data_t);
    }

    echo json_encode(['data' => $jSon['data']]);
} else {
    echo json_encode(['messageType' => 'ERROR']);
}
