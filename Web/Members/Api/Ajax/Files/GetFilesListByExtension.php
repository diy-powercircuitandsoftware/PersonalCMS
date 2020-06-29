<?php

session_start();
include_once '../../../../../Class/Core/Config/Config.php';
include_once '../../../../../Class/FileIO/VirtualDirectory.php';
include_once '../../../../../Class/Core/User/Database.php';
$config = new Config();
$userdb = new User_Database($config);
if ($config->IsOnline() && isset($_SESSION["User"])) {
    
    $vd = new VirtualDirectory($userdb->GetFilesPath( $_SESSION["User"]["id"]));
    if (isset($_POST["Ext"])) {
        echo json_encode($vd->GetFilesList($_POST["Path"], $_POST["Ext"]));
    } else {
        echo json_encode($vd->GetFilesList($_POST["Path"]));
    }
}
$userdb->close();
$config->close();

