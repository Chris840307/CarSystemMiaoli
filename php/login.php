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
$acc = $str_json->{'acc'};
$pwd = $str_json->{'pwd'};
$name = '';

//取得使用者IP
if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
    $ip = $_SERVER['HTTP_CLIENT_IP'];
} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
} else {
    $ip = $_SERVER['REMOTE_ADDR'];
}

$conn = mysqli_connect($dbhost, $dbuser, $dbpass) or die('Error with MySQL connection');

mysqli_query($conn, 'SET NAMES utf8');
mysqli_select_db($conn, $dbname);

$sql = "SELECT * FROM `user_list` WHERE `acc`='$acc' AND `pwd`='$pwd'";
$result = mysqli_query($conn, $sql) or die('MySQL select error'.mysqli_error($conn));

if ($result->num_rows > 0) {
    $record = mysqli_fetch_array($result);

    $name = $record['name'];

    //紀錄登入log
    $sql = "INSERT INTO `sys_log`(`acc`, `name`, `class`, `ip`, `content`) VALUES ('$acc','$name','系統登入','$ip','登入成功')";
    $result = mysqli_query($conn, $sql) or die('MySQL select error'.mysqli_error($conn));

    echo json_encode(['messageType' => 'OK', 'auth' => $record['auth'], 'status' => $record['status'], 'acc' => $record['acc']]);
} else {
    //紀錄登入log
    $sql = "INSERT INTO `sys_log`(`acc`, `name`, `class`, `ip`, `content`) VALUES ('$acc','$name','系統登入','$ip','登入失敗')";
    $result = mysqli_query($conn, $sql) or die('MySQL select error'.mysqli_error($conn));

    echo json_encode(['messageType' => 'ERROR']);
}
