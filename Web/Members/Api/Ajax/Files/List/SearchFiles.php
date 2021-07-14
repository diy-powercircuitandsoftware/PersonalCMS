<?php

session_start();
include_once '../../../../../../Class/Core/Config/Config.php';
include_once '../../../../../../Class/FileIO/VirtualDirectory.php';
include_once '../../../../../../Class/Core/User/Database.php';
$config = new Config();
$udb = new User_Database($config);
if ($config->IsOnline() && isset($_SESSION["User"])) {
    $vd = new VirtualDirectory($udb->GetFilesPath($_SESSION["User"]["id"]));
    if (isset($_POST["Path"])) {
        echo json_encode($vd->SearchFiles($_POST["Path"], $_POST["Name"], true));
    }
}
$udb->close();
$config->CloseDB();

