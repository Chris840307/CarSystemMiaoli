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

$conn = mysqli_connect($dbhost, $dbuser, $dbpass) or die('Error with MySQL connection'.mysql_error());

mysqli_query($conn, 'SET NAMES utf8');
mysqli_select_db($conn, $dbname);

$jSon['data'] = [];

    $sql = "SELECT * FROM `violation` WHERE `Datetime` BETWEEN '$txt_date1' AND '$txt_date2' AND `status`='2'";
    // echo $sql;
    $result = mysqli_query($conn, $sql) or die('MySQL select error'.mysqli_error($conn));

    if ($result->num_rows > 0) {
        while ($record = mysqli_fetch_array($result)) {
            $data_t = new stdClass();

            $data_t->RowNo = $record['RowNo'];
            $data_t->Datetime = $record['Datetime'];
            $data_t->CarNumber = $record['CarNumber'];
            $data_t->Location = $record['Location'];
            $data_t->VideoURL = $record['VideoURL'];
            $data_t->PhotoURL = $record['PhotoURL'];
            $data_t->DetectLocation = $record['DetectLocation'];
            $data_t->Datetime = $record['Datetime'];
            switch ($record['status']) {
                case 1:
                    $data_t->status = '未處理';
                    break;
                case 2:
                    $data_t->status = '已開單';
                    break;
                case 3:
                    $data_t->status = '未開單';
                    break;
            }

            array_push($jSon['data'], $data_t);
        }
    }

    echo json_encode(['data' => $jSon['data']]);
