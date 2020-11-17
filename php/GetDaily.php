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
$txt_date1 = $str_json->{'txt_date1'}.' 00:00:00';
$txt_date2 = $str_json->{'txt_date2'}.' 23:23:23';
$txt_account = $str_json->{'txt_account'};

$conn = mysqli_connect($dbhost, $dbuser, $dbpass) or die('Error with MySQL connection'.mysql_error());

mysqli_query($conn, 'SET NAMES utf8');
mysqli_select_db($conn, $dbname);

$jSon['data'] = [];

$sql = "SELECT * FROM `sys_log` WHERE `datetime` BETWEEN '$txt_date1' AND '$txt_date2'";
if ($txt_account != null && $txt_account != 'undefined') {
    $sql = $sql." AND `acc` = '$txt_account'";
}
$sql = $sql.' ORDER BY `id` DESC';
// echo $sql;
$result = mysqli_query($conn, $sql) or die('MySQL select error'.mysqli_error($conn));

if ($result->num_rows > 0) {
    while ($record = mysqli_fetch_array($result)) {
        $data_t = new stdClass();

        $data_t->id = $record['id'];
        $data_t->datetime = $record['datetime'];
        $data_t->acc = $record['acc'];
        $data_t->name = $record['name'];
        $data_t->class = $record['class'];
        $data_t->ip = $record['ip'];
        $data_t->content = $record['content'];

        array_push($jSon['data'], $data_t);
    }

    echo json_encode(['data' => $jSon['data']]);
} else {
    echo json_encode(['messageType' => 'ERROR']);
}
