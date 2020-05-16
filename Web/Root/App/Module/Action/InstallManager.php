<?php

session_start();
include_once '../../../../../Class/Core/Config/Config.php';
include_once '../../../../../Class/Core/Module/Database.php';
$config = new Config();
$module = new Module_Database($config);
if ($config->HasRootAuth(session_id())) {
   if ( $module->Install()){
       echo 'Install Complete';
   }
}
$module->close();
