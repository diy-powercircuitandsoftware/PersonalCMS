<?php

session_start();
include_once '../../../../../Class/Core/Config/Config.php';
include_once '../../../../../Class/Core/Module/Database.php';
$config = new Config();
$module = new Module_Database($config);
if ($config->HasRootAuth(session_id())) {
    echo json_encode(array_values (array_diff(scandir($module->ModulePath), array('.', '..','Module.db','.htaccess'))));
}
  