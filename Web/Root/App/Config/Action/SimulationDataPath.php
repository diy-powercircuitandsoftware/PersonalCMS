<?php
session_start();
include_once '../../../../../Class/Core/Config/Config.php';
$config=new Config();

if ($config->HasRootAuth(session_id())) {
    echo $config->SimulationDataPath($_POST["path"]);
}
