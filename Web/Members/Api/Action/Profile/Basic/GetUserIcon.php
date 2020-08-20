<?php

include_once '../../../../../../Class/Core/Config/Config.php';
include_once '../../../../../../Class/Core/User/Database.php';
include_once '../../../../../../Class/FileIO/ImageThumbnail.php';
$config = new Config();
$ud = new User_Database($config);
if ($config->IsOnline() && isset($_GET["id"])) {
    $path = $ud->GetProfilePath($_GET["id"])."/ProfileImage";
    
    if (is_file($path) && explode("/", mime_content_type($path))[0] == "image") {
         CreateImageThumbnail($path, 30, 30);
    } else {
        header("Content-Type: image/png");
        $im = imagecreate(30, 30);
        $background_color = imagecolorallocate($im, rand(0, 255), rand(0, 255), rand(0, 255));
        imagepng($im);
        imagedestroy($im); 
    }
} else {
    header("HTTP/1.0 404 Not Found");
}