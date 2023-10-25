<?php

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
    CURLOPT_URL => 'http://223.200.44.147/X/bm',
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
echo $response;
