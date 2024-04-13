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

$id = $str_json->{'id'};
$type = $str_json->{'type'};
$value = $str_json->{'value'};
$specialCar = $str_json->{'specialCar'};

$conn = mysqli_connect($dbhost, $dbuser, $dbpass) or die('Error with MySQL connection');

mysqli_query($conn, 'SET NAMES utf8');
mysqli_select_db($conn, $dbname);

$sql = "UPDATE `car_type` SET `type`='$type',`value`='$value',`specialCar`='$specialCar' WHERE `id`='$id'";
$result = mysqli_query($conn, $sql) or die('MySQL select error' . mysqli_error($conn));

echo json_encode(['messageType' => 'OK']);
