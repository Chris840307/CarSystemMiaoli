<?php
//壓縮檔下載
 $file_name = "已開單案件.zip";
 $file_path = "/var/www/html/CarSystem/已開單案件.zip";
//  $file_path = "/var/www/html/CarSystem/已開單案件.zip";
 $file_size = filesize($file_path);
 header('Pragma: public');
 header('Expires: 0');
 header('Last-Modified: ' . gmdate('D, d M Y H:i ') . ' GMT');
 header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
 header('Cache-Control: private', false);
// header('Content-Type: application/octet-stream');
header("Content-Type: application/zip");
//header("Content-Transfer-Encoding: Binary");
 header('Content-Length: ' . $file_size);
 header('Content-Disposition: attachment; filename="' . $file_name . '";');
// header('Content-Transfer-Encoding: binary');
 readfile($file_path);



$path = "../export";
function deleteDir($dir)
{
    if (!$handle = @opendir($dir)) {
        return false;
    }
    while (false !== ($file = readdir($handle))) {
        if ($file !== "." && $file !== "..") {       //排除当前目录与父级目录
            $file = $dir . '/' . $file;
            if (is_dir($file)) {
                deleteDir($file);
            } else {
                @unlink($file);
            }
        }
    }
    
}
deleteDir($path);

 ?>