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
$image_url = $str_json->{'image_url'};
$SN = $str_json->{'SN'};
$No = $str_json->{'No'};


$conn = mysqli_connect($dbhost, $dbuser, $dbpass) or die('Error with MySQL connection');

mysqli_query($conn, 'SET NAMES utf8');
mysqli_select_db($conn, $dbname);

//jpg傳base64
$path = '/var/www/html/CarSystem' . $image_url;
$data = file_get_contents($path);
$base64 = base64_encode($data);


$curl = curl_init();
curl_setopt_array($curl, array(
    CURLOPT_URL => 'http://223.200.44.147/' . $No,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_POSTFIELDS => array('Data' => $base64),
));

$response = curl_exec($curl);

curl_close($curl);


//寫入DB留紀錄
$conn = mysqli_connect($dbhost, $dbuser, $dbpass) or die('Error with MySQL connection');

mysqli_query($conn, 'SET NAMES utf8');
mysqli_select_db($conn, $dbname);

$obj = json_decode($response);
$Message = $obj->Message;

$sql = "INSERT INTO `case_image`(`SN`, `No`, `PhotoURL`, `Message`) VALUES ('$SN','$No','$path','$Message')";
$result = mysqli_query($conn, $sql) or die('MySQL select error' . mysqli_error($conn));


echo $response;
// echo $str;
