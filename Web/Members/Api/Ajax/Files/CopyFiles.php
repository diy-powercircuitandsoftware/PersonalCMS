<?php

session_start();
include_once '../../../../../Class/Core/Config/Config.php';
include_once '../../../../../Class/Com/Files/Database.php';
include_once '../../../../../Class/FileIO/VirtualDirectory.php';
include_once '../../../../../Class/Core/User/Session.php';
include_once '../../../../../Class/Core/User/Member.php';
include_once '../../../../../Class/Core/User/Database.php';
$config = new Config();
$fd = new Files_Database($config);
$userdb = new User_Database($config);
$session = new User_Session($userdb);
$userdata = new User_Member($userdb);
if ($config->IsOnline() && isset($_POST["Path"]) && isset($_POST["Files"]) && isset($_SESSION["User"]) &&
        $session->Registered(session_id()) &&
        $userdata->CanWritable($_SESSION["User"]["id"])) {
    $vd = new VirtualDirectory($fd->GetUserDIR($userdb, $_SESSION["User"]["id"])."/Files/");
    $out = true;
    foreach (explode(",", $_POST["Files"]) as $value) {
        $out = $out && ($vd->Copy($value, $_POST["Path"]));
    }
    echo $out;
} else {
    echo '0';
}
