<?php

session_start();
include_once '../../../../../Class/Core/Config/Config.php';
include_once '../../../../../Class/Core/User/Database.php';
include_once '../../../../../Class/Core/User/Manager.php';
$config = new Config();
$user=new User_Manager(new User_Database($config));
if ($config->HasRootAuth(session_id()) && isset($_POST["UserID"])) {
    $user->DeleteUser($_POST["UserID"]);
}
   $user->Close();