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
        $session->Registered(session_id())  ) {
    $vd = new VirtualDirectory($userdb->GetFilesPath($_SESSION["User"]["id"]));
    if ($vd->IsFile($_POST["path"])) {
        $path = $vd->DiskPath($_POST["path"]);
        $point = new OfficeIO_PointPoint($path);
        echo json_encode($point->GetEmbedList($_POST["type"]));
        $point->Close();
    }
    else{
        echo json_encode(null);
    }
} else {
    echo '0';
}
$userdb->close();
$config->CloseDB();
