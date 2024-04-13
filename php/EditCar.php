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
$RowNo = $str_json->{'RowNo'};
$carNumber = $str_json->{'carNumber'};
$carType = $str_json->{'carType'};
$law = $str_json->{'law'};
$status = $str_json->{'status'};
$remark = $str_json->{'remark'};
$result = $str_json->{'result'};
$policeName = $str_json->{'policeName'};

$conn = mysqli_connect($dbhost, $dbuser, $dbpass) or die('Error with MySQL connection');

mysqli_query($conn, 'SET NAMES utf8');
mysqli_select_db($conn, $dbname);

$sql = "UPDATE `violation` SET `CarNumber`='$carNumber', `status`='$status', `Law`='$law', `remark`='$remark', `result`='$result', `PoliceName`='$policeName'";
if ($carType != null && $carType != '') {
    $sql = $sql . ",`carType`='$carType'";
}
$sql = $sql . " WHERE `RowNo` = '$RowNo'";
// echo $sql;
$result = mysqli_query($conn, $sql) or die('MySQL select error' . mysqli_error($conn));

echo json_encode(['messageType' => 'OK']);
