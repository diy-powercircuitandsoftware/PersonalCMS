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
if ($config->IsOnline() && isset($_POST["path"]) && isset($_SESSION["User"]) &&
        $session->Registered(session_id()) &&
        $userdata->CanWritable($_SESSION["User"]["id"])) {
    $vd = new VirtualDirectory($fd->GetUserDIR($_SESSION["User"]["id"]));
    $dp = $vd->DiskPath($_POST["path"]);
    if (is_writable($dp)) {
        var_dump($_FILES["Upload"]);
      //  $fp = fopen($TMPPath, 'a');
       // fwrite($fp, file_get_contents($_FILES["Upload"]["tmp_name"]));
       // fclose($fp);
    }
} else {
    echo '0';
}
