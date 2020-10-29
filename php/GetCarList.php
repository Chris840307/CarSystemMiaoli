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
$txt_num = $str_json->{'txt_num'};
$status = $str_json->{'status'};

$conn = mysqli_connect($dbhost, $dbuser, $dbpass) or die('Error with MySQL connection'.mysql_error());

mysqli_query($conn, 'SET NAMES utf8');
mysqli_select_db($conn, $dbname);

$jSon['data'] = [];

$sql = "SELECT * FROM `violation` WHERE `status`='$status'";
if ($txt_num != null && $txt_num != 'undefined') {
    $sql = $sql." AND `CarNumber` = '$txt_num'";
}
if ($txt_date1 != null && $txt_date1 != 'undefined') {
    $sql = $sql." AND `Datetime` BETWEEN '$txt_date1' AND '$txt_date2'";
}
$sql = $sql.' ORDER BY CAST(`RowNo` AS UNSIGNED INTEGER) ASC';
// echo $sql;
$result = mysqli_query($conn, $sql) or die('MySQL select error'.mysqli_error($conn));

if ($result->num_rows > 0) {
    while ($record = mysqli_fetch_array($result)) {
        if ($record['CarNumber'] != 'Unknown-1') {
            $data_t = new stdClass();

            $data_t->RowNo = $record['RowNo'];
            $data_t->Datetime = $record['Datetime'];
            $data_t->CarNumber = $record['CarNumber'];
            $data_t->Location = $record['Location'];
            $data_t->VideoURL = $record['VideoURL'];
            $data_t->PhotoURL = $record['PhotoURL'];
            $data_t->DetectLocation = $record['DetectLocation'];
            $data_t->Datetime = $record['Datetime'];
            $data_t->carTypeValue = $record['carType'];
            switch ($record['carType']) {
                case '1':
                    $data_t->carType = '機車';
                    break;
                case '2':
                    $data_t->carType = '汽車';
                    break;
            }
            $data_t->LawValue = $record['Law'];
            switch ($record['Law']) {
                case '5610102':
                    $data_t->Law = '在禁止臨時停車處所停車';
                    break;
                case '5610402':
                    $data_t->Law = '在設有禁止停車標線之處所停車';
                    break;
                case '5620002':
                    $data_t->Law = '併排停車';
                    break;
                case '5510404':
                    $data_t->Law = '併排臨時停車';
                    break;
            }
            $data_t->statusValue = $record['status'];
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

    echo json_encode(['messageType' => 'OK', 'data' => $jSon['data']]);
} else {
    echo json_encode(['messageType' => 'ERROR']);
}
