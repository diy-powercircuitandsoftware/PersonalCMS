<?php

session_start();
include_once '../../../../../Class/Core/Config/Config.php';
include_once '../../../../../Class/Core/User/Database.php';
include_once '../../../../../Class/Core/User/Register.php';
$config = new Config();
$user = new User_Register(new User_Database($config));
if ($config->HasRootAuth(session_id())) {
     echo json_encode($user->GetRegister($_POST["phone"]));
}
  $user->Close();