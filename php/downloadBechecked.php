<?php

//壓縮檔下載
$file_name = '已開單案件.zip';
$file_path = '/var/www/html/CarSystem/已開單案件.zip';
$file_size = filesize($file_path);

header('Pragma: public');
header('Expires: 0');
header('Last-Modified: ' . gmdate('D, d M Y H:i ') . ' GMT');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Cache-Control: private', false);
// header('Content-Type: application/octet-stream');
header('Content-Type: application/zip');
//header("Content-Transfer-Encoding: Binary");
header('Content-Length: ' . $file_size);
header('Content-Disposition: attachment; filename="' . $file_name . '";');

readfile($file_path);

//清空資料夾函式和清空資料夾後刪除空資料夾函式的處理
function deldir($path)
{
    //如果是目錄則繼續
    if (is_dir($path)) {
        //掃描一個資料夾內的所有資料夾和檔案並返回陣列
        $p = scandir($path);
        foreach ($p as $val) {
            //排除目錄中的.和..
            if ($val != '.' && $val != '..') {
                //如果是目錄則遞迴子目錄，繼續操作
                if (is_dir($path . $val)) {
                    //子目錄中操作刪除資料夾和檔案
                    deldir($path . $val . '/');
                    //目錄清空後刪除空資料夾
                    @rmdir($path . $val . '/');
                } else {
                    //如果是檔案直接刪除
                    unlink($path . $val);
                }
            }
        }
    }
}
//呼叫函式，傳入路徑
$path = '../export/';
deldir($path);
