<?php
 
include_once '../../../../../../../Class/Core/Config/Config.php';
include_once '../../../../../../../Class/FileIO/VirtualDirectory.php';
include_once '../../../../../../../Class/Core/User/Database.php';
include_once '../../../../../../../Class/Core/User/Session.php';
include_once '../../../../../../../Class/Core/User/Member.php';
include_once '../../../../../../../Class/Com/FilesACLS/Custom.php';
$config = new Config();
$userdb = new User_Database($config);

 

if ($config->IsOnline() ) {
    $path = new VirtualDirectory($userdb->GetRootPath($_GET["user"]));
    $realpath = $path->DiskPath("/Photo/SlideShow/Share.xml");
    $acls = new FilesACLS_Custom();
    $acls->Load($realpath);
    echo json_encode( $acls->GetList(FilesACLS_Custom::Access_Public));
    
}
$userdb->close();
$config->close();
