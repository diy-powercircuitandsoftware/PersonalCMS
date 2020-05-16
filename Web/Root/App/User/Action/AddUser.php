<?php
session_start();
include_once '../../../../../Class/Core/Config/Config.php';
include_once '../../../../../Class/Core/User/Database.php';
include_once '../../../../../Class/Core/User/Manager.php';
$config=new Config();
$user=new User_Manager(new User_Database($config));
if ($config->HasRootAuth(session_id())&&isset($_POST["Alias"])&&isset($_POST["Password"])){
   echo $user->AddUser($_POST["Alias"],$_POST["Password"]);
}
$user->Close();