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
$d_edit_name = $str_json->{'d_edit_name'};
$d_edit_acc = $str_json->{'d_edit_acc'};
$d_edit_pwd = $str_json->{'d_edit_pwd'};
$d_edit_auth = $str_json->{'d_edit_auth'};
$d_edit_status = $str_json->{'d_edit_status'};

$conn = mysqli_connect($dbhost, $dbuser, $dbpass) or die('Error with MySQL connection');

mysqli_query($conn, 'SET NAMES utf8');
mysqli_select_db($conn, $dbname);

$sql = "SELECT * FROM `user_list` WHERE `acc` = '$d_edit_acc'";
$result = mysqli_query($conn, $sql) or die('MySQL select error' . mysqli_error($conn));
if ($result->num_rows > 1) {
    echo json_encode(['messageType' => 'Repeat']);
} else {
    if ($d_edit_pwd != null && $d_edit_pwd != 'undefined') {
        // 修改包含密碼的所有欄位
        $sql = "UPDATE `user_list` SET `name`='$d_edit_name',`acc`='$d_edit_acc',`pwd`='$d_edit_pwd',`auth`='$d_edit_auth',`status`='$d_edit_status' WHERE id = '$id'";
        $result = mysqli_query($conn, $sql) or die('MySQL select error' . mysqli_error($conn));

        echo json_encode(['messageType' => 'OK']);
    } else {
        // 修改除了密碼之外的欄位
        $sql = "UPDATE `user_list` SET `name`='$d_edit_name',`acc`='$d_edit_acc',`auth`='$d_edit_auth',`status`='$d_edit_status' WHERE id = '$id'";
        $result = mysqli_query($conn, $sql) or die('MySQL select error' . mysqli_error($conn));

        echo json_encode(['messageType' => 'OK']);
    }
}
