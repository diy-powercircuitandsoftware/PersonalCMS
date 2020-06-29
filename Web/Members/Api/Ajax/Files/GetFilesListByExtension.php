<?php

session_start();
include_once '../../../../../Class/Core/Config/Config.php';
include_once '../../../../../Class/Com/Files/Database.php';
include_once '../../../../../Class/FileIO/VirtualDirectory.php';
include_once '../../../../../Class/Core/User/Database.php';
$config = new Config();
$fd = new Files_Database($config);
$userdb = new User_Database($config);
if ($config->IsOnline() && isset($_SESSION["User"])) {
    $path = $fd->GetUserDIR($userdb, $_SESSION["User"]["id"]) . "/Files/";
    if (!is_dir($path)) {
        mkdir($path);
    }
    $vd = new VirtualDirectory($path);
    if (isset($_POST["Ext"])) {
        echo json_encode($vd->GetFilesList($_POST["Path"], $_POST["Ext"]));
    } else {
        echo json_encode($vd->GetFilesList($_POST["Path"]));
    }
}
$fd->close();

