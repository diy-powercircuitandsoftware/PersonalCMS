<?php
session_start();
include_once '../../../../../../Class/Core/Config/Config.php';
include_once '../../../../../../Class/Core/User/Database.php';
include_once '../../../../../../Class/FileIO/ImageThumbnail.php';
include_once '../../../../../../Class/FileIO/VirtualDirectory.php';
$config = new Config();
$udb = new User_Database($config);
if ($config->IsOnline() && isset($_GET["id"])) {
    $vd = new VirtualDirectory($udb->GetFilesPath($_SESSION["User"]["id"]));
    $path = $vd->DiskPath($_GET["id"]);
    if (is_file($path) && explode("/", mime_content_type($path))[0] == "image") {
      CreateImageThumbnail($path, 50, 50);
    }
} else {
    header("HTTP/1.0 404 Not Found");
}
$udb->close();
$config->CloseDB();
