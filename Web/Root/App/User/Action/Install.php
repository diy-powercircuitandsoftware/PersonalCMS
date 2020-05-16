<?php

session_start();
include_once '../../../../../Class/Core/Config/Config.php';
include_once '../../../../../Class/Core/User/Database.php';
$config = new Config();
$user = new User_Database($config);
if ($config->HasRootAuth(session_id())) {
   if ( $user->Install()){
       echo 'Install Complete';
   }
}
$user->close();
