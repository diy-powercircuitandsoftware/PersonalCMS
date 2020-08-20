<?php

session_start();
include_once '../../../../../../Class/Core/Config/Config.php';
include_once '../../../../../../Class/FileIO/VirtualDirectory.php';
include_once '../../../../../../Class/Core/User/Session.php';
include_once '../../../../../../Class/Core/User/Member.php';
include_once '../../../../../../Class/Core/User/Database.php';
include_once '../../../../../../Class/OfficeIO/Blog.php';
$config = new Config();
$userdb = new User_Database($config);
$session = new User_Session($userdb);
$userdata = new User_Member($userdb);

if ($config->IsOnline() && isset($_SESSION["User"]) &&
        $session->Registered(session_id()) &&
        $userdata->CanWritable($_SESSION["User"]["id"])) {
    $vd = new VirtualDirectory($userdb->GetFilesPath($_SESSION["User"]["id"]));
    $blog = new OfficeIO_Blog($vd->DiskPath($_POST["Path"]));
    if (isset($_POST["ID"])) {
        echo json_encode($blog->GetStat(intval($_POST["ID"])));
    } else if (isset($_POST["Name"])) {
        echo json_encode($blog->GetStat($_POST["Name"]));
    }

    $blog->Close();
} else {
    echo '0';
}
$userdb->close();
$config->close();
