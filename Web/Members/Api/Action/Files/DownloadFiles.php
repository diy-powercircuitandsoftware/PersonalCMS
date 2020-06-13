<?php

session_start();
include_once '../../../../../Class/Core/Config/Config.php';
include_once '../../../../../Class/Com/Files/Database.php';
include_once '../../../../../Class/FileIO/VirtualDirectory.php';
include_once '../../../../../Class/FileIO/FileDownloader.php';
$config = new Config();
$fd = new Files_Database($config);
$fdownload = new FileDownloader();
if ($config->IsOnline() && isset($_GET["path"])) {
    $vd = new VirtualDirectory($fd->GetUserDIR($_SESSION["User"]["id"]));
    if ($vd->IsFile($_GET["path"])) {
        $fdownload->DownloadFile($vd->DiskPath($_GET["path"]));
    }
} else {
    header("HTTP/1.0 404 Not Found");
}


   /* if (isset($_GET["option"]) && $_GET["option"] == "disable206") {
            header('Content-Description: File Transfer');
            header('Content-Transfer-Encoding: binary');
            header('Connection: Keep-Alive');
            header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
            header("Cache-Control: post-check=0, pre-check=0", false);
            header("Pragma: no-cache");
            header('Content-Disposition: attachment; filename=' . urldecode(pathinfo($path, PATHINFO_FILENAME)) . "." . pathinfo($path, PATHINFO_EXTENSION));
            header("Content-Type: " . mime_content_type($path));
            readfile($path);
        } else if (isset($_GET["option"]) && $_GET["option"] == "opendisable206") {
            header('Content-Transfer-Encoding: binary');
            header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
            header("Cache-Control: post-check=0, pre-check=0", false);
            header("Pragma: no-cache");
            header('Content-Disposition: inline; filename=' . urldecode(pathinfo($path, PATHINFO_FILENAME)) . "." . pathinfo($path, PATHINFO_EXTENSION));
            header("Content-Type: " . mime_content_type($path));
            readfile($path);*/
      //  } else {
           // 
        //}