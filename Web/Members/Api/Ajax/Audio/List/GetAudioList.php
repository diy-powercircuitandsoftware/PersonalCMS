<?php

session_start();
include_once '../../../../../../Class/Core/Config/Config.php';
include_once '../../../../../../Class/FileIO/VirtualDirectory.php';
include_once '../../../../../../Class/Core/User/Database.php';
$config = new Config();
$userdb = new User_Database($config);
if ($config->IsOnline() && isset($_SESSION["User"])) {
    $vd = new VirtualDirectory($userdb->GetRootPath($_SESSION["User"]["id"]));
    $out = array();
    foreach ($data = explode(PHP_EOL, $vd->FileGetContents("/Audio/" . $_GET["Name"])) as $value) {
      $tmp= explode('/', $value);
        $out[] = array("path" => $value, "name" => end($tmp));
    }
    echo json_encode($out);
}
$userdb->close();
$config->CloseDB();
