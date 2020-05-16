<?php

session_start();
include_once '../../../../../Class/Core/Config/Config.php';
include_once '../../../../../Class/Core/Module/Database.php';
$config = new Config();
$module = new Module_Database($config);
if ($config->IsME(session_id(),$_POST["password"])) {
    if ($module->Uninstall()) {
        echo '1';
    }
}
else{
     echo 'Permission Denied';
}
 