<?php
session_start();
include_once '../../../../../Class/Core/Config/Config.php';
include_once '../../../../../Class/Com/Files/Database.php';
include_once '../../../../../Class/FileIO/ImageThumbnail.php';
$config = new Config();
$fd = new Files_Database($config);
if ($config->IsOnline() && isset($_GET["id"])) {
    $path = $fd->GetUserDIR($_SESSION["User"]["id"]).$_GET["id"];
    if (is_file($path) && explode("/", mime_content_type($path))[0] == "image") {
        CreateImageThumbnail($path, 30, 30);
    }  
} else {
    header("HTTP/1.0 404 Not Found");
}