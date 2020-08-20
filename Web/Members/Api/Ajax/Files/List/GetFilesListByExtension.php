<?php

session_start();
include_once '../../../../../../Class/Core/Config/Config.php';
include_once '../../../../../../Class/FileIO/VirtualDirectory.php';
include_once '../../../../../../Class/Core/User/Database.php';
$config = new Config();
$userdb = new User_Database($config);
if ($config->IsOnline() && isset($_SESSION["User"])) {
    $out=array();
    $vd = new VirtualDirectory($userdb->GetFilesPath( $_SESSION["User"]["id"]));
    if (isset($_POST["Ext"])) {
        
       $out=($vd->GetFilesList($_POST["Path"], $_POST["Ext"]));
    } else {
       $out=($vd->GetFilesList($_POST["Path"]));
    }
    echo json_encode($out);
}
$userdb->close();
$config->close();

