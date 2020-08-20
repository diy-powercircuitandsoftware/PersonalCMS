<?php

session_start();
include_once '../../../../../../Class/Core/Config/Config.php';
include_once '../../../../../../Class/FileIO/VirtualDirectory.php';
include_once '../../../../../../Class/Core/User/Database.php';
$config = new Config();
$userdb = new User_Database($config);
if ($config->IsOnline() && isset($_SESSION["User"])) {
    $playlist = new VirtualDirectory($userdb->GetRootPath($_SESSION["User"]["id"]));
    $savepath = "/Audio/" . $_POST["Name"];
    $out = explode("\r\n", $playlist->FileGetContents($savepath));
       $out= array_diff($out,  $_POST["Path"]);
    $playlist->FilePutContents($savepath, implode("\r\n", $out) );
}
$userdb->close();
$config->close();

