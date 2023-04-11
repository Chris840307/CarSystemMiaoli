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
$auth = $str_json->{'auth'};
$txt_acc = $str_json->{'txt_acc'};
$txt_status = $str_json->{'txt_status'};

$conn = mysqli_connect($dbhost, $dbuser, $dbpass) or die('Error with MySQL connection');

mysqli_query($conn, 'SET NAMES utf8');
mysqli_select_db($conn, $dbname);

$jSon['data'] = [];

if ($auth == '1') {
    //使用者不可看到管理員帳號
    $sql = "SELECT * FROM `user_list` WHERE `auth` != '0'";
} else {
    $sql = "SELECT * FROM `user_list` WHERE `auth` != '3'";
}

if ($txt_acc != null && $txt_acc != 'undefined') {
    $sql = $sql." AND `acc` = '$txt_acc'";
}
if ($txt_status != null && $txt_status != 'undefined') {
    if ($txt_status == '停權') {
        $sql = $sql." AND `status` = '0'";
    } elseif ($txt_status == '使用中') {
        $sql = $sql." AND `status` = '1'";
    }
}
$sql = $sql.' ORDER BY `id`';
// echo $sql;
$result = mysqli_query($conn, $sql) or die('MySQL select error'.mysqli_error($conn));

if ($result->num_rows > 0) {
    while ($record = mysqli_fetch_array($result)) {
        $data_t = new stdClass();

        $data_t->id = $record['id'];
        $data_t->name = $record['name'];
        $data_t->acc = $record['acc'];
        if ($record['auth'] == '0') {
            $data_t->auth = '管理員';
        }
        if ($record['auth'] == '1') {
            $data_t->auth = '使用者';
        }
        $data_t->auth_id = $record['auth'];
        if ($record['status'] == '0') {
            $data_t->status = '停權';
        }
        if ($record['status'] == '1') {
            $data_t->status = '使用中';
        }
        $data_t->status_id = $record['status'];

        array_push($jSon['data'], $data_t);
    }

    echo json_encode(['data' => $jSon['data']]);
} else {
    echo json_encode(['messageType' => 'ERROR']);
}
