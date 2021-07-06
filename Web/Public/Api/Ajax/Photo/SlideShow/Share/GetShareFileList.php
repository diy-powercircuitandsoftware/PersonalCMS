<?php

include_once '../../../../../../../Class/Core/Config/Config.php';
include_once '../../../../../../../Class/FileIO/VirtualDirectory.php';
include_once '../../../../../../../Class/Core/User/Database.php';
include_once '../../../../../../../Class/Core/User/Session.php';
include_once '../../../../../../../Class/Core/User/Member.php';
include_once '../../../../../../../Class/Com/FilesACLS/Custom.php';
$config = new Config();
$userdb = new User_Database($config);

if ($config->IsOnline()) {
    $path = new VirtualDirectory($userdb->GetRootPath($_GET["user"]));
    
    $acls = new FilesACLS_Custom();
    $acls->Load($path->DiskPath("/Photo/SlideShow/Share.xml"));
     if ($acls->Exists($_GET["name"],FilesACLS_Custom::Access_Public)){
          
        echo json_encode( preg_split("/\n|\r\n?/", $path->FileGetContents("/Photo/SlideShow/" .$_GET["name"])));
     }
}
$userdb->close();
$config->close();
