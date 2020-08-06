<?php

function CreateImageThumbnail($Img, $MaxW, $MaxH, $savefilepath = "") {

    $source = null;
    list($sw, $sh, $stype) = getimagesize($Img);
    $Ratio = min($MaxW / $sw, $MaxH / $sh);
    switch ($stype) {
        case IMAGETYPE_GIF:
            $source = imagecreatefromgif($Img);
            break;
        case IMAGETYPE_JPEG:
            $source = imagecreatefromjpeg($Img);
            break;
        case IMAGETYPE_PNG:
            $source = imagecreatefrompng($Img);
            break;
    }

    $thumb = imagecreatetruecolor($sw * $Ratio, $sh * $Ratio);
    imagecopyresized($thumb, $source, 0, 0, 0, 0, $sw * $Ratio, $sh * $Ratio, $sw, $sh);

    if ($savefilepath == "") {
       header('Content-Type: image/jpeg');
        imagejpeg($thumb);
    } else {
        imagejpeg($thumb, $savefilepath);
    }

    imagedestroy($thumb);
    imagedestroy($source);
}

function CreateStringImageThumbnail($ImgString, $MaxW, $MaxH, $savefilepath = "") {
    $source = imagecreatefromstring($ImgString);
    $Ratio = min($MaxW / imagesx($source), $MaxH / imagesy($source));
    $thumb = imagecreatetruecolor(imagesx($source) * $Ratio, imagesy($source) * $Ratio);
    imagecopyresized($thumb, $source, 0, 0, 0, 0, imagesx($source) * $Ratio, imagesy($source) * $Ratio, imagesx($source), imagesy($source));

    if ($savefilepath == "") {
        header('Content-Type: image/jpeg');
        imagejpeg($thumb);
    } else {

        imagejpeg($thumb, $savefilepath);
    }

    imagedestroy($thumb);
    imagedestroy($source);
}
