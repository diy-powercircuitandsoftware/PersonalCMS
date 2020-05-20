<?php
session_start();
include_once '../../../../../Class/Core/Config/Config.php';
$config=new Config();

if ($config->HasRootAuth(session_id())) {
    echo  $config->ChRootPW($_POST["OldPassword"], $_POST["NewPassword"]);
}
 