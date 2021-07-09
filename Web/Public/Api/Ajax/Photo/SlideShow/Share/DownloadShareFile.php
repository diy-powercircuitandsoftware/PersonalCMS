<?php

session_start();
include_once '../../../../../../../Class/Core/Config/Config.php';
include_once '../../../../../../../Class/FileIO/VirtualDirectory.php';
include_once '../../../../../../../Class/Core/User/Database.php';
include_once '../../../../../../../Class/Core/User/Session.php';
include_once '../../../../../../../Class/Core/User/Member.php';
include_once '../../../../../../../Class/Com/FilesACLS/Custom.php';
include_once '../../../../../../../Class/Cryptography/AES.php';
include_once '../../../../../../../Class/FileIO/FileDownloader.php';
$config = new Config();
$userdb = new User_Database($config);
$fdownload = new FileDownloader();
if ($config->IsOnline()) {
    $aes = new Cryptography_AES(session_id() . $_SERVER['REMOTE_ADDR']);
    $vd0 = new VirtualDirectory($userdb->GetRootPath($_GET["user"]));
    $vd1 = new VirtualDirectory($userdb->GetFilesPath($_GET["user"]));
    $acls = new FilesACLS_Custom();
    $acls->Load($vd0->DiskPath("/Photo/SlideShow/Share.xml"));
    $sha1file = sha1_file($vd0->DiskPath("/Photo/SlideShow/Share.xml"));
    $d = $aes->Decrypt($_GET["path"], $sha1file);
    if ($vd1->IsFile($d)) {
        $fdownload->DownloadFile($vd1->DiskPath($d));
    }
}
$userdb->close();
$config->close();
return;
