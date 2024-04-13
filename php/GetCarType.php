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
$specialCar = $str_json->{'specialCar'};

$conn = mysqli_connect($dbhost, $dbuser, $dbpass) or die('Error with MySQL connection');

mysqli_query($conn, 'SET NAMES utf8');
mysqli_select_db($conn, $dbname);

$jSon['data'] = [];

$sql = "SELECT * FROM `car_type`";
if ($specialCar != null && $specialCar == "1") {
    $sql = $sql . " WHERE `specialCar` = '$specialCar'";
}
$sql = $sql . ' ORDER BY `id` ASC';
$result = mysqli_query($conn, $sql) or die('MySQL select error' . mysqli_error($conn));

if ($result->num_rows > 0) {
    while ($record = mysqli_fetch_array($result)) {
        $data_t = new stdClass();

        $data_t->id = $record['id'];
        $data_t->type = $record['type'];
        $data_t->value = $record['value'];
        $data_t->specialVol = $record['specialCar'];
        switch ($record['specialCar']) {
            case 1:
                $data_t->specialCar = '是';
                break;
            case 0:
                $data_t->specialCar = '否';
                break;
        }

        array_push($jSon['data'], $data_t);
    }

    echo json_encode(['data' => $jSon['data']]);
} else {
    echo json_encode(['messageType' => 'ERROR']);
}
