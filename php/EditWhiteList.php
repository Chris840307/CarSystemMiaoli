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
$documentNumber = $str_json->{'documentNumber'};
$carNumber = $str_json->{'carNumber'};
$startTime = $str_json->{'startTime'};
$endTime = $str_json->{'endTime'};
$DetectLocation = $str_json->{'DetectLocation'};
$status = $str_json->{'status'};

$conn = mysqli_connect($dbhost, $dbuser, $dbpass) or die('Error with MySQL connection');

mysqli_query($conn, 'SET NAMES utf8');
mysqli_select_db($conn, $dbname);

$sql = "UPDATE `white_list` SET `documentNumber`='$documentNumber',`carNumber`='$carNumber',`startTime`='$startTime',`endTime`='$endTime',`DetectLocation`='$DetectLocation',`status`='$status' WHERE `id`='$id'";
$result = mysqli_query($conn, $sql) or die('MySQL select error' . mysqli_error($conn));

echo json_encode(['messageType' => 'OK']);
