<?php

session_start();
include_once '../../../../../../../Class/Core/Config/Config.php';
include_once '../../../../../../../Class/FileIO/VirtualDirectory.php';
include_once '../../../../../../../Class/Core/User/Database.php';
include_once '../../../../../../../Class/Core/User/Session.php';
include_once '../../../../../../../Class/Core/User/Member.php';
include_once '../../../../../../../Class/Com/FilesACLS/Custom.php';
$config = new Config();
$userdb = new User_Database($config);
$userdata = new User_Member($userdb);
$session = new User_Session($userdb);

if ($config->IsOnline() && isset($_SESSION["User"]) &&
        $session->Registered(session_id()) &&
        $userdata->CanWritable($_SESSION["User"]["id"])) {
    $path = new VirtualDirectory($userdb->GetRootPath($_SESSION["User"]["id"]));
    $realpath = $path->DiskPath("/Photo/SlideShow/Share.xml");
    $acls = new FilesACLS_Custom();
    $acls->Load($realpath);
    echo json_encode( $acls->GetAllShareList());
    
}
$userdb->close();
$config->close();
