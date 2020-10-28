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
$toDay = $str_json->{'toDay'};
$toFirstDay = $str_json->{'toFirstDay'};

$dayCount = 0;
$toDayHandled = 0;
$toMonthHandled = 0;

$conn = mysqli_connect($dbhost, $dbuser, $dbpass) or die('Error with MySQL connection'.mysql_error());

mysqli_query($conn, 'SET NAMES utf8');
mysqli_select_db($conn, $dbname);

//本日案件(總)
$sql = "SELECT count(*) AS count FROM `violation` WHERE `LogDate` BETWEEN '$toDay 00:00:00' AND '$toDay 23:23:23'";
$result = mysqli_query($conn, $sql) or die('MySQL select error'.mysqli_error($conn));
if ($result->num_rows > 0) {
    $record = mysqli_fetch_array($result);
    $dayCount = $record['count'];
}
//本日案件(未處理)
$sql = "SELECT count(*) AS count FROM `violation` WHERE `LogDate` BETWEEN '$toDay 00:00:00' AND '$toDay 23:23:23' AND `status`='1'";
$result = mysqli_query($conn, $sql) or die('MySQL select error'.mysqli_error($conn));
if ($result->num_rows > 0) {
    $record = mysqli_fetch_array($result);
    $toDayNotHandle = $record['count'];

    //已處理
    $toDayHandled = $dayCount - $toDayNotHandle;
}

//本月案件(總)
$sql = "SELECT count(*) AS count FROM `violation` WHERE `LogDate` BETWEEN '$toFirstDay 00:00:00' AND '$toDay 23:23:23'";
$result = mysqli_query($conn, $sql) or die('MySQL select error'.mysqli_error($conn));
if ($result->num_rows > 0) {
    $record = mysqli_fetch_array($result);
    $monthCount = $record['count'];
}
//本月案件(未處理)
$sql = "SELECT count(*) AS count FROM `violation` WHERE `LogDate` BETWEEN '$toFirstDay 00:00:00' AND '$toDay 23:23:23' AND `status`='1'";
$result = mysqli_query($conn, $sql) or die('MySQL select error'.mysqli_error($conn));
if ($result->num_rows > 0) {
    $record = mysqli_fetch_array($result);
    $toMonthNotHandle = $record['count'];

    //已處理
    $toMonthHandled = $monthCount - $toMonthNotHandle;
}
    echo json_encode([
         'toDay' => $dayCount,
         'toDayNotHandle' => $toDayNotHandle,
         'toDayHandled' => $toDayHandled,
         'toMonth' => $monthCount,
         'toMonthNotHandle' => $toMonthNotHandle,
         'toMonthHandled' => $toMonthHandled,
        ]);
