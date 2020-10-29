<?php

error_reporting(E_ERROR | E_WARNING | E_PARSE);
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=utf-8');

$dbhost = 'localhost';
$dbuser = 'root';
$dbpass = '2u6u/ru8';
$dbname = 'car';



$conn = mysqli_connect($dbhost, $dbuser, $dbpass) or die('Error with MySQL connection' . mysql_error());

mysqli_query($conn, 'SET NAMES utf8');
mysqli_select_db($conn, $dbname);

$sql = "SELECT `RowNo`, `Datetime`,`CarNumber`,`Location`,`DetectLocation`,`LogDate` FROM `violation` WHERE `status`=2 ";
$result = mysqli_query($conn, $sql) or die('MySQL select error' . mysqli_error($conn));

?>