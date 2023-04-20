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
$SN = $str_json->{'SN'};
$ViolationDate = $str_json->{'ViolationDate'};
$ViolationTime = $str_json->{'ViolationTime'};
$UnitId = $str_json->{'UnitId'};
$PoliceName = $str_json->{'PoliceName'};
$LicensePlate = $str_json->{'LicensePlate'};
$VehicleType = $str_json->{'VehicleType'};
$RuleId = $str_json->{'RuleId'};
$Road = $str_json->{'Road'};

$curl = curl_init();

curl_setopt_array($curl, array(
    CURLOPT_URL => 'http://api.mac.yh-tech.com.tw/X/bm',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_POSTFIELDS => array(
        'SN' => $SN,
        'ViolationDate' => $ViolationDate,
        'ViolationTime' => $ViolationTime,
        'UnitId' => $UnitId,
        'PoliceName' => $PoliceName,
        'LicensePlate' => $LicensePlate,
        'VehicleType' => $VehicleType,
        'RuleId' => $RuleId,
        'Road' => $Road
    ),
));

$response = curl_exec($curl);

curl_close($curl);


//寫入DB留紀錄
$conn = mysqli_connect($dbhost, $dbuser, $dbpass) or die('Error with MySQL connection');

mysqli_query($conn, 'SET NAMES utf8');
mysqli_select_db($conn, $dbname);

$obj = json_decode($response);
$No = $obj->No;
$Message = $obj->Message;

$sql = "INSERT INTO `case_verify`(`SN`, `No`, `Message`) VALUES ('$SN','$No','$Message')";
$result = mysqli_query($conn, $sql) or die('MySQL select error' . mysqli_error($conn));


echo $response;
// echo $str;
