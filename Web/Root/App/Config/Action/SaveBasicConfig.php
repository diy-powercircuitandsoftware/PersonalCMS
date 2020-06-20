<?php

session_start();
include_once '../../../../../Class/Core/Config/Config.php';
$config = new Config();

if ($config->HasRootAuth(session_id())) {
    
    foreach ($_POST["data"] as $key => $value) {
          $config->InsertValue($key, $value);
    }
    
}
