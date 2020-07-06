<?php
session_start();
include_once '../../../../../Class/Core/Config/Config.php';
include_once '../../../../../Class/FileIO/VirtualDirectory.php';
include_once '../../../../../Class/Core/User/Database.php';
$config = new Config();
$userdb = new User_Database($config);
if ($config->IsOnline() && isset($_SESSION["User"])) {
    $vd = new VirtualDirectory($userdb->GetRootPath($_SESSION["User"]["id"]));
    $vd->RenameLast("/Audio/".$_POST["Name"],$_POST["NewName"]);
    
}
$userdb->close();
$config->close();

