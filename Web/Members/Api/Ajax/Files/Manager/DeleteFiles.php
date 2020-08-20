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
if ($config->IsOnline() && isset($_POST["path"]) && isset($_SESSION["User"]) &&
        $session->Registered(session_id()) &&
        $userdata->CanWritable($_SESSION["User"]["id"]) 
) {
    $vd = new VirtualDirectory($userdb->GetFilesPath($_SESSION["User"]["id"]));
    if (isset($_POST["password"])&&$userdata->AuthByPassword($_SESSION["User"]["id"], $_POST["password"])) {
        foreach ($_POST["path"] as $value) {
            $vd->DeleteFile($value);
        }
    }
    else if (!isset($_POST["password"])&& !is_array($_POST["path"])){
          $vd->DeleteFile($_POST["path"]);
    }
    echo '1';
} else {
    echo '0';
}
$userdb->close();
$config->close();