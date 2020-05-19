<?php

session_start();
include_once '../../../../../Class/Core/Config/Config.php';
include_once '../../../../../Class/Core/User/Database.php';
$config = new Config();
$user = new User_Database($config);
if ($config->IsME(session_id(),$_POST["password"])) {
    if ($user->Uninstall()) {
        echo '1';
    }
}
else{
     echo 'Permission Denied';
}
 