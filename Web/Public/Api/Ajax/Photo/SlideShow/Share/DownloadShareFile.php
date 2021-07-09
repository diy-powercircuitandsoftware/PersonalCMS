<?php
return;
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
    $aes = new Cryptography_AES(session_id().$_SERVER['REMOTE_ADDR']);
    $path = new VirtualDirectory($userdb->GetRootPath($_GET["user"]));

    $acls = new FilesACLS_Custom();
    $acls->Load($path->DiskPath("/Photo/SlideShow/Share.xml"));
    if ($acls->Exists($_GET["name"], FilesACLS_Custom::Access_Public)) {
        $sha1file = sha1_file($path->DiskPath("/Photo/SlideShow/Share.xml"));
        $files = preg_split("/\n|\r\n?/", $path->FileGetContents("/Photo/SlideShow/" . $_GET["name"]));
        for ($i = 0; $i < count($files); $i++) {
            $e = $aes->Encrypt($files[$i], $sha1file);
            $files[$i] = http_build_query(array(
                "name" => $_GET["name"], "path" => $e
            ));
        }
        echo json_encode($files);
    }
}
$userdb->close();
$config->close();
