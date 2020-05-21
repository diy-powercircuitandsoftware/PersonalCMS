<?php

session_start();
include_once '../../../../../Class/Core/Config/Config.php';
$config = new Config();
if ($config->HasRootAuth(session_id())) {
    echo json_encode(array_values (array_diff(scandir("../../../../../Module/"), array('.', '..'))));
}
  