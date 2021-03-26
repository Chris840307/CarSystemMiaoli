<?php

error_reporting(E_ERROR | E_WARNING | E_PARSE);
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=utf-8');

$dbhost = 'localhost';
$dbuser = 'root';
$dbpass = '2u6u/ru8';
$dbname = 'car';

// $str = file_get_contents('php://input');
// $str_json = json_decode($str);
// $txt_date1 = $str_json->{'txt_date1'};
// $txt_date2 = $str_json->{'txt_date2'};
// $txt_num = $str_json->{'txt_num'};

$conn = mysqli_connect($dbhost, $dbuser, $dbpass) or die('Error with MySQL connection'.mysql_error());

mysqli_query($conn, 'SET NAMES utf8');
mysqli_select_db($conn, $dbname);

$jSon['data'] = [];

$sql = 'SELECT `DetectLocation` FROM `violation` GROUP BY `DetectLocation`';
$result = mysqli_query($conn, $sql) or die('MySQL select error'.mysqli_error($conn));

if ($result->num_rows > 0) {
    while ($record = mysqli_fetch_array($result)) {
        if ($record['CarNumber'] != 'Unknown-1') {
            $data_t = new stdClass();

            $data_t->DetectLocation = $record['DetectLocation'];
            $data_t->DetectLocation = $record['DetectLocation'];

            array_push($jSon['data'], $data_t);
        }
    }

    echo json_encode(['messageType' => 'OK', 'data' => $jSon['data']]);
} else {
    echo json_encode(['messageType' => 'ERROR']);
}
