<?php

session_start();
include_once '../../../../../../Class/Core/Config/Config.php';
include_once '../../../../../../Class/FileIO/VirtualDirectory.php';
include_once '../../../../../../Class/Core/User/Database.php';
include_once '../../../../../../Class/Core/User/Session.php';
include_once '../../../../../../Class/Core/User/Member.php';
$config = new Config();
$userdb = new User_Database($config);
$userdata = new User_Member($userdb);
$session = new User_Session($userdb);
if ($config->IsOnline() && isset($_SESSION["User"]) &&
        $session->Registered(session_id()) &&
        $userdata->CanWritable($_SESSION["User"]["id"])) {
    $playlist = new VirtualDirectory($userdb->GetRootPath($_SESSION["User"]["id"]));
    $savepath = "/Audio/" . $_POST["Name"];
    $out = preg_split("/\n|\r\n?/", $playlist->FileGetContents($savepath));
       $out= array_diff($out,  $_POST["Path"]);
    $playlist->FilePutContents($savepath, implode("\r\n", $out) );
}
$userdb->close();
$config->CloseDB();

