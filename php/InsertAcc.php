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
$d_add_name = $str_json->{'d_add_name'};
$d_add_acc = $str_json->{'d_add_acc'};
$d_add_pwd = $str_json->{'d_add_pwd'};
$d_add_auth = $str_json->{'d_add_auth'};
$d_add_status = $str_json->{'d_add_status'};

$conn = mysqli_connect($dbhost, $dbuser, $dbpass) or die('Error with MySQL connection');

mysqli_query($conn, 'SET NAMES utf8');
mysqli_select_db($conn, $dbname);

$sql = "SELECT * FROM `user_list` WHERE `acc` = '$acc'";
$result = mysqli_query($conn, $sql) or die('MySQL select error'.mysqli_error($conn));
if ($result->num_rows > 0) {
    echo json_encode(['messageType' => 'Repeat']);
} else {
    $sql = "INSERT INTO `user_list`(`name`, `acc`, `pwd`, `auth`, `status`) VALUES ('$d_add_name','$d_add_acc','$d_add_pwd','$d_add_auth','$d_add_status')";
    $result = mysqli_query($conn, $sql) or die('MySQL select error'.mysqli_error($conn));

    echo json_encode(['messageType' => 'OK']);
}
