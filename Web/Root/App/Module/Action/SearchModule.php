<?php

session_start();
include_once '../../../../../Class/Core/Config/Config.php';
include_once '../../../../../Class/Core/Module/Database.php';
 
$config = new Config();
$md = new Module_Database($config);
if ($config->HasRootAuth(session_id())) {
     echo json_encode($md->SearchModule($_POST["name"]));
}
 $md->close();