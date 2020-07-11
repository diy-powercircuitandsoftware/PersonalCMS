<?php

session_start();
include_once '../../../../../Class/Core/Config/Config.php';
include_once '../../../../../Class/FileIO/VirtualDirectory.php';
include_once '../../../../../Class/Core/User/Database.php';
$config = new Config();
$userdb = new User_Database($config);
if ($config->IsOnline() && isset($_SESSION["User"])) {
    $playlist = new VirtualDirectory($userdb->GetRootPath($_SESSION["User"]["id"]));
    $filelist = new VirtualDirectory($userdb->GetFilesPath($_SESSION["User"]["id"]));
    $savepath = "/Audio/" . $_POST["Name"];
    $out = explode("\r\n", $playlist->FileGetContents($savepath));
    foreach ($_POST["Path"] as $value) {
        if ($filelist->IsFile($value)) {
            $out[] = $value;
        } else if ($filelist->IsDir($value)) {
            foreach ( $filelist->SearchFiles($value,".mp3,.wma,.ogg",true) as $value) {
                 $out[] = $value["fullpath"];
            }
        }
    }
    $playlist->FilePutContents($savepath, implode("\r\n", array_unique($out)));
}
$userdb->close();
$config->close();

