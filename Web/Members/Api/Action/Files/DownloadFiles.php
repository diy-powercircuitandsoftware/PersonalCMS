<?php

session_start();
include_once '../../../../../Class/Core/Config/Config.php';
include_once '../../../../../Class/Com/Files/Database.php';
include_once '../../../../../Class/FileIO/VirtualDirectory.php';
include_once '../../../../../Class/FileIO/FileDownloader.php';
include_once '../../../../../Class/FileIO/ZipDirectory.php';
$config = new Config();
$fd = new Files_Database($config);
$fdownload = new FileDownloader();
$zip=new ZipDirectory();
if ($config->IsOnline() && isset($_GET["path"])) {
    $vd = new VirtualDirectory($fd->GetUserDIR($_SESSION["User"]["id"]));
    if ($vd->IsFile($_GET["path"])) {
        $fdownload->DownloadFile($vd->DiskPath($_GET["path"]));
    } else if ($vd->IsDir($_GET["path"])){
      $zip->Add($vd->DiskPath($_GET["path"]));
      $zip->Zip();
      echo ;
    }
    
} else {
    header("HTTP/1.0 404 Not Found");
}
