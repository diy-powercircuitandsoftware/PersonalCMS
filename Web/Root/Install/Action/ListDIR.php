<?php

include_once '../../../../Class/Core/Config/Installer.php';
$installer = new Installer();
if (isset($_POST["Path"])) {
    $out = array();
    $path = "";
    if ($_POST["Path"] == "/") {
        $path = realpath($installer->GetAppPath());
    } else {
        $path = $_POST["Path"];
    }
    foreach (scandir($path) as $value) {
        $rpath = $path . "/" . $value;
        if (is_dir($rpath)) {
            $out[] = array("name" => $value, "realpath" => realpath($rpath));
        }
    }
    echo json_encode($out);
//
}