<?php

session_start();
include_once '../../../../../../../Class/Core/Config/Config.php';
include_once '../../../../../../../Class/FileIO/VirtualDirectory.php';
include_once '../../../../../../../Class/Core/User/Session.php';
include_once '../../../../../../../Class/Core/User/Member.php';
include_once '../../../../../../../Class/Core/User/Database.php';
include_once '../../../../../../../Class/OfficeIO/PointPoint.php';
$config = new Config();
$userdb = new User_Database($config);
$session = new User_Session($userdb);
$userdata = new User_Member($userdb);

if ($config->IsOnline() && isset($_SESSION["User"]) &&
        $session->Registered(session_id()) &&
        $userdata->CanWritable($_SESSION["User"]["id"])) {
    $vd = new VirtualDirectory($userdb->GetFilesPath($_SESSION["User"]["id"]));
    $path = $vd->DiskPath($_POST["FullPath"]);
    $point = new OfficeIO_PointPoint($path);
    if (isset($_FILES["UploadFile"])) {
        $point->AddEmbedFile( $_FILES["UploadFile"]["name"], $_FILES["UploadFile"]["tmp_name"]);
    }
    echo $point->Close();
} else {
    echo '0';
}
$userdb->close();
$config->CloseDB();
