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
$txt_ip = $str_json->{'txt_ip'};
$txt_addr = $str_json->{'txt_addr'};

$conn = mysqli_connect($dbhost, $dbuser, $dbpass) or die('Error with MySQL connection');

mysqli_query($conn, 'SET NAMES utf8');
mysqli_select_db($conn, $dbname);

$jSon['data'] = [];

$sql = "SELECT * FROM `SyncTime` WHERE `ID` != 'null'";
if ($txt_ip != null && $txt_ip != 'undefined') {
    $sql = $sql." AND `deviceIP` = '$txt_ip'";
}
if ($txt_addr != null && $txt_addr != 'undefined') {
    $sql = $sql." AND `place` = '$txt_addr'";
}
$sql = $sql.' ORDER BY `ID` DESC';
$result = mysqli_query($conn, $sql) or die('MySQL select error'.mysqli_error($conn));

while ($record = mysqli_fetch_array($result)) {
    $data_t = new stdClass();

    $data_t->ID = $record['ID'];
    $data_t->deviceIP = $record['deviceIP'];
    $data_t->deviceTime = $record['deviceTime'];
    $data_t->place = $record['place'];
    $data_t->ServerTime = $record['ServerTime'];

    array_push($jSon['data'], $data_t);
}

echo json_encode(['data' => $jSon['data']]);
