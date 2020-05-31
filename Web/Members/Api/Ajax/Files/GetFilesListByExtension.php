<?php
session_start();
include_once '../../../../../Class/Core/Config/Config.php';
include_once '../../../../../Class/Com/Files/Database.php';
 include_once '../../../../../Class/FileIO/VirtualDirectory.php';
$config = new Config();
$fd=new Files_Database($config);
if ($config->IsOnline() && isset($_SESSION["User"])) {
echo   $fd->GetUserDIR($_SESSION["User"]["id"]);
}
$fd->close();
 
 