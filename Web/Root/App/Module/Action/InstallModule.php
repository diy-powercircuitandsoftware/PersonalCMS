<?php

session_start();
include_once '../../../../../Class/Core/Config/Config.php';
include_once '../../../../../Class/Core/Module/Database.php';
include_once '../../../../../Class/Interpreter/GetClassNameFromFile.php';
$config = new Config();
$module = new Module_Database($config);
if ($config->HasRootAuth(session_id())&&isset($_POST["FileName"])&&isset($_POST["Layout"])) {
    $modpath= '../../../../../Class/Module/'.$_POST["FileName"];
     
   if ( $module->InstallModule( $_POST["FileName"], GetClassNameFromFile($modpath), $_POST["Layout"])){
       echo 'Install Complete';
   }
}
$module->close();
