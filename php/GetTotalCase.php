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

$jSon['white_data'] = [];
$jSon['continuous_data'] = []; //存不舉發資料
$jSon['continuous_data_all'] = []; //存複數資料

$conn = mysqli_connect($dbhost, $dbuser, $dbpass) or die('Error with MySQL connection'.mysql_error());

mysqli_query($conn, 'SET NAMES utf8');
mysqli_select_db($conn, $dbname);

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
$sql = 'SELECT `CarNumber`,COUNT(`CarNumber`) AS CarCount FROM `violation` WHERE `CarNumber` != NULL';
if ($txt_date1 != null && $txt_date1 != 'undefined') {
    $sql = $sql." AND `Datetime` BETWEEN '$txt_date1' AND '$txt_date2'";
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

//本日案件(總)
$sql = "SELECT * FROM `violation` WHERE `Datetime` BETWEEN '$toDay 00:00:00' AND '$toDay 23:59:59'";
$result = mysqli_query($conn, $sql) or die('MySQL select error'.mysqli_error($conn));
if ($result->num_rows > 0) {
    // $record = mysqli_fetch_array($result);
    // $dayCount = $record['count'];
    $dayCount = 0;
    while ($record = mysqli_fetch_array($result)) {
        if ($record['CarNumber'] != 'Unknown-1') {
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
                    ++$dayCount;
                } else {
                    //查詢車號是否在時段中
                    $car_datetime = strtotime($record['Datetime']);
                    $white_data_startime = strtotime($jSon['white_data'][$white_data_index]->startTime);
                    $white_data_endtime = strtotime($jSon['white_data'][$white_data_index]->endTime);

                    if ($car_datetime > $white_data_startime && $car_datetime < $white_data_endtime) {
                        //時段在白名單中不顯示
                    } else {
                        //時段不在白名單中可顯示
                        ++$dayCount;
                    }
                }
            }
        }
    }
}
//本日案件(已開單)
$sql = "SELECT count(*) AS count FROM `violation` WHERE `Datetime` BETWEEN '$toDay 00:00:00' AND '$toDay 23:59:59' AND `status` = '2'";
$result = mysqli_query($conn, $sql) or die('MySQL select error'.mysqli_error($conn));
if ($result->num_rows > 0) {
    $record = mysqli_fetch_array($result);
    $toDayHandled = $record['count'];   //已開單
}
//本日案件(未開單)
$sql = "SELECT count(*) AS count FROM `violation` WHERE `Datetime` BETWEEN '$toDay 00:00:00' AND '$toDay 23:59:59' AND `status` = '3'";
$result = mysqli_query($conn, $sql) or die('MySQL select error'.mysqli_error($conn));
if ($result->num_rows > 0) {
    $record = mysqli_fetch_array($result);
    $toNoHandled = $record['count'];   //未開單

    //扣除未處理
    $toDayNotHandle = $dayCount - $toDayHandled - $toNoHandled;
    $dayCount =  $dayCount - $toNoHandled;
}

//本月案件(總)
$sql = "SELECT * FROM `violation` WHERE `Datetime` BETWEEN '$toFirstDay 00:00:00' AND '$toDay 23:59:59'";
$result = mysqli_query($conn, $sql) or die('MySQL select error'.mysqli_error($conn));
if ($result->num_rows > 0) {
    // $record = mysqli_fetch_array($result);
    // $monthCount = $record['count'];
    $monthCount = 0;
    while ($record = mysqli_fetch_array($result)) {
        if ($record['CarNumber'] != 'Unknown-1') {
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
                    ++$monthCount;
                } else {
                    //查詢車號是否在時段中
                    $car_datetime = strtotime($record['Datetime']);
                    $white_data_startime = strtotime($jSon['white_data'][$white_data_index]->startTime);
                    $white_data_endtime = strtotime($jSon['white_data'][$white_data_index]->endTime);

                    if ($car_datetime > $white_data_startime && $car_datetime < $white_data_endtime) {
                        //時段在白名單中不顯示
                    } else {
                        //時段不在白名單中可顯示
                        ++$monthCount;
                    }
                }
            }
        }
    }
}
//本月案件(已開單)
$sql = "SELECT count(*) AS count FROM `violation` WHERE `Datetime` BETWEEN '$toFirstDay 00:00:00' AND '$toDay 23:59:59' AND `status` = '2'";
$result = mysqli_query($conn, $sql) or die('MySQL select error'.mysqli_error($conn));
if ($result->num_rows > 0) {
    $record = mysqli_fetch_array($result);
    $toMonthHandled = $record['count']; //已開單
}
//本月案件(未開單)
$sql = "SELECT count(*) AS count FROM `violation` WHERE `Datetime` BETWEEN '$toFirstDay 00:00:00' AND '$toDay 23:59:59' AND `status` = '3'";
$result = mysqli_query($conn, $sql) or die('MySQL select error'.mysqli_error($conn));
if ($result->num_rows > 0) {
    $record = mysqli_fetch_array($result);
    $toMonthNoHandled = $record['count']; //未開單

    //扣除未處理
    $toMonthNotHandle = $monthCount - $toMonthHandled - $toMonthNoHandled;
    $monthCount = $monthCount - $toMonthNoHandled;
}

    echo json_encode([
         'toDay' => $dayCount,
         'toDayNotHandle' => $toDayNotHandle,
         'toDayHandled' => $toDayHandled,
         'toMonth' => $monthCount,
         'toMonthNotHandle' => $toMonthNotHandle,
         'toMonthHandled' => $toMonthHandled,
        ]);
