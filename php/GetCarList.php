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
$txt_date1 = $str_json->{'txt_date1'};
$txt_date2 = $str_json->{'txt_date2'};
$txt_num = $str_json->{'txt_num'};
$txt_cartype = $str_json->{'txt_cartype'};
$txt_law = $str_json->{'txt_law'};
$txt_addr = $str_json->{'txt_addr'};
$status = $str_json->{'status'};

$conn = mysqli_connect($dbhost, $dbuser, $dbpass) or die('Error with MySQL connection'.mysql_error());

mysqli_query($conn, 'SET NAMES utf8');
mysqli_select_db($conn, $dbname);

$jSon['data'] = [];
$jSon['white_data'] = [];
$jSon['continuous_data'] = []; //存不舉發資料
$jSon['continuous_data_all'] = []; //存複數資料

//白名單
$sql = "SELECT * FROM `white_list` WHERE `status`='1'";
$result = mysqli_query($conn, $sql) or die('MySQL select error'.mysqli_error($conn));
if ($result->num_rows > 0) {
    while ($record = mysqli_fetch_array($result)) {
        $data_t = new stdClass();
        $data_t->carNumber = $record['carNumber'];
        $data_t->startTime = $record['startTime'];
        $data_t->endTime = $record['endTime'];

        array_push($jSon['white_data'], $data_t);
    }
}

//連續舉發
//取得複數超過2筆資料
$sql = 'SELECT `CarNumber`,COUNT(`CarNumber`) AS CarCount FROM `violation`';
if ($txt_date1 != null && $txt_date1 != 'undefined') {
    $sql = $sql." WHERE `Datetime` BETWEEN '$txt_date1' AND '$txt_date2'";
}
$sql = $sql.' GROUP BY `CarNumber`';
$result = mysqli_query($conn, $sql) or die('MySQL select error'.mysqli_error($conn));
if ($result->num_rows > 0) {
    while ($record = mysqli_fetch_array($result)) {
        if ($record['CarCount'] > '1' && $record['CarNumber'] != 'Unknown-1') {
            $data_t = new stdClass();
            $data_t->carNumber = $record['CarNumber'];

            array_push($jSon['continuous_data_all'], $data_t);
        }
    }
}
//篩選是否符合連續舉發條件 test:2583-KY
for ($i = 0; $i < count($jSon['continuous_data_all']); ++$i) {
    $temp_car_time = '';
    foreach ($jSon['continuous_data_all'][$i] as $key => $value) {
        $sql = "SELECT * FROM `violation` WHERE `CarNumber` = '$value'";
        $result = mysqli_query($conn, $sql) or die('MySQL select error'.mysqli_error($conn));
        while ($record = mysqli_fetch_array($result)) {
            if ($temp_car_time === '') {
                $temp_car_time = strtotime($record['Datetime']);
            } else {
                // 連續2小時不舉發
                if (((strtotime($record['Datetime']) - $temp_car_time) / (60 * 60)) < 2) {
                    $data_t = new stdClass();
                    $data_t->RowNo = $record['RowNo'];

                    array_push($jSon['continuous_data'], $data_t);
                } else {
                    $temp_car_time = strtotime($record['Datetime']);
                }
            }
        }
    }
}

//車輛查詢
$sql = "SELECT * FROM `violation` WHERE `status`='$status'";
if ($txt_num != null && $txt_num != 'undefined') {
    $sql = $sql." AND `CarNumber` = '$txt_num'";
}
if ($txt_cartype != null && $txt_cartype != 'undefined') {
    $sql = $sql." AND `carType` = '$txt_cartype'";
}
if ($txt_law != null && $txt_law != 'undefined') {
    $sql = $sql." AND `Law` = '$txt_law'";
}
if ($txt_addr != null && $txt_addr != 'undefined') {
    $sql = $sql." AND `DetectLocation` = '$txt_addr'";
}
if ($txt_date1 != null && $txt_date1 != 'undefined') {
    $sql = $sql." AND `Datetime` BETWEEN '$txt_date1' AND '$txt_date2'";
}
$sql = $sql.' ORDER BY CAST(`Datetime` AS UNSIGNED INTEGER) DESC';
// echo $sql;
$result = mysqli_query($conn, $sql) or die('MySQL select error'.mysqli_error($conn));

if ($result->num_rows > 0) {
    while ($record = mysqli_fetch_array($result)) {
        if ($record['CarNumber'] != 'Unknown-1') {
            $data_t = new stdClass();

            $data_t->RowNo = $record['RowNo'];
            $data_t->Datetime = $record['Datetime'];
            $data_t->CarNumber = $record['CarNumber'];
            $data_t->Location = $record['Location'];
            if (substr($record['VideoURL'], 0, 4) == '/var') {
                $data_t->VideoURL = '.'.substr($record['VideoURL'], 23);
            } else {
                $data_t->VideoURL = './'.str_replace('\\', '/', $record['VideoURL']);
            }
            if (substr($record['PhotoURL'], 0, 4) == '/var') {
                $data_t->PhotoURL = '.'.substr($record['PhotoURL'], 23);
            } else {
                $data_t->PhotoURL = './'.str_replace('\\', '/', $record['PhotoURL']);
            }
            $data_t->DetectLocation = $record['DetectLocation'];
            $data_t->Datetime = $record['Datetime'];
            $data_t->carTypeValue = $record['carType'];
            switch ($record['carType']) {
                        case '1':
                            $data_t->carType = '機車';
                            break;
                        case '2':
                            $data_t->carType = '汽車';
                            break;
                    }
            $data_t->LawValue = $record['Law'];
            switch ($record['Law']) {
                        case '5610102':
                            $data_t->Law = '在禁止臨時停車處所停車';
                            break;
                        case '5610402':
                            $data_t->Law = '在設有禁止停車標線之處所停車';
                            break;
                        case '5620002':
                            $data_t->Law = '併排停車';
                            break;
                        case '5510404':
                            $data_t->Law = '併排臨時停車';
                            break;
                    }
            $data_t->statusValue = $record['status'];
            switch ($record['status']) {
                        case 1:
                            $data_t->status = '未處理';
                            break;
                        case 2:
                            $data_t->status = '已開單';
                            break;
                        case 3:
                            $data_t->status = '未開單';
                            break;
                    }

            //連續兩小時不舉發
            $continuous_data_index = array_search($record['RowNo'], array_column($jSon['continuous_data'], 'RowNo'));
            if ($continuous_data_index === false) {
                // 未找到 需要舉發
                $continuous_flag = true;
            } else {
                // 找到 不舉發
                $continuous_flag = false;
            }

            if ($continuous_flag) {
                //查詢車號是否在白名單中
                $white_data_index = array_search($record['CarNumber'], array_column($jSon['white_data'], 'carNumber'));
                if ($white_data_index === false) {
                    //不在白名單中
                    array_push($jSon['data'], $data_t);
                } else {
                    //查詢車號是否在時段中
                    $car_datetime = strtotime($record['Datetime']);
                    $white_data_startime = strtotime($jSon['white_data'][$white_data_index]->startTime);
                    $white_data_endtime = strtotime($jSon['white_data'][$white_data_index]->endTime);

                    if ($car_datetime > $white_data_startime && $car_datetime < $white_data_endtime) {
                        //時段在白名單中不顯示
                    } else {
                        //時段不在白名單中可顯示
                        array_push($jSon['data'], $data_t);
                    }
                }
            }
        }
    }

    echo json_encode(['messageType' => 'OK', 'data' => $jSon['data']]);
} else {
    echo json_encode(['messageType' => 'ERROR']);
}
