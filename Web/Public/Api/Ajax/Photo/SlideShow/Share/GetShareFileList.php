<?php

session_start();
include_once '../../../../../../../Class/Core/Config/Config.php';
include_once '../../../../../../../Class/FileIO/VirtualDirectory.php';
include_once '../../../../../../../Class/Core/User/Database.php';
include_once '../../../../../../../Class/Core/User/Session.php';
include_once '../../../../../../../Class/Core/User/Member.php';
include_once '../../../../../../../Class/Com/FilesACLS/Custom.php';
include_once '../../../../../../../Class/Cryptography/AES.php';
$config = new Config();
$userdb = new User_Database($config);

if ($config->IsOnline()) {
    $aes = new Cryptography_AES(session_id() . $_SERVER['REMOTE_ADDR']);
    $path = new VirtualDirectory($userdb->GetRootPath($_GET["user"]));

    $acls = new FilesACLS_Custom();
    $acls->Load($path->DiskPath("/Photo/SlideShow/Share.xml"));
    if ($acls->Exists($_GET["name"], FilesACLS_Custom::Access_Public)) {
        $sha1file = sha1_file($path->DiskPath("/Photo/SlideShow/Share.xml"));
        $files = preg_split("/\n|\r\n?/", $path->FileGetContents("/Photo/SlideShow/" . $_GET["name"]));
        $image = array();
        $audio = array();
        for ($i = 0; $i < count($files); $i++) {
            $e = $aes->Encrypt($files[$i], $sha1file);
            $ext = strtolower(substr($files[$i], strrpos($files[$i], '.') + 1));
            if (in_array($ext, array("jpg","jpeg","png","gif"))) {
                $image[] = http_build_query(array(
                    "user" => $_GET["user"], "name" => $_GET["name"], "path" => $e
                ));
            } else if (in_array($ext, array("mp3","ogg","wma"))) {
                $audio[] = http_build_query(array(
                    "user" => $_GET["user"], "name" => $_GET["name"], "path" => $e
                ));
            }
        }
        echo json_encode(array(
            "audio"=> $audio,
            "image"=>$image
        ));
    }
}
$userdb->close();
$config->close();
