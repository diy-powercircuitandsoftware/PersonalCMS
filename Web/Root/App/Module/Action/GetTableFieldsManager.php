<?php

session_start();
include_once '../../../../../Class/Core/Config/Config.php';
include_once '../../../../../Class/Core/Module/Database.php';
$config = new Config();
$mod = new Module_Database($config);
if ($config->HasRootAuth(session_id())) {
    echo json_encode($mod->GetTableFields($_POST["name"]));
}
$mod->Close();
