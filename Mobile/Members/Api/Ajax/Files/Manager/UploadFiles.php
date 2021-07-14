<?php

session_start();
include_once '../../../../../../Class/Core/Config/Config.php';
include_once '../../../../../../Class/FileIO/VirtualDirectory.php';
include_once '../../../../../../Class/Core/User/Session.php';
include_once '../../../../../../Class/Core/User/Member.php';
include_once '../../../../../../Class/Core/User/Database.php';
$config = new Config();
$userdb = new User_Database($config);
$session = new User_Session($userdb);
$userdata = new User_Member($userdb);
if ($config->IsOnline() && isset($_SESSION["User"]) &&
        $session->Registered(session_id()) &&
        $userdata->CanWritable($_SESSION["User"]["id"]) &&
        isset($_POST["path"])) {

    $vd = new VirtualDirectory($userdb->GetFilesPath($_SESSION["User"]["id"]));
    $dp = $vd->DiskPath($_POST["path"]);
    if (is_writable($dp)) {
        echo move_uploaded_file($_FILES['file']['tmp_name'], $dp . "/" . preg_replace('/\s/', '_', ($_FILES['file']['name'])));
    } else {
        echo '0';
    }
} else {
    echo '0';
}
$userdb->close();
$config->CloseDB();
