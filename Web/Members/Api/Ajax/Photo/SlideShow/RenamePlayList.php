<?php
session_start();
include_once '../../../../../../Class/Core/Config/Config.php';
include_once '../../../../../../Class/FileIO/VirtualDirectory.php';
include_once '../../../../../../Class/Core/User/Database.php';
include_once '../../../../../../Class/Core/User/Session.php';
include_once '../../../../../../Class/Core/User/Member.php';
$config = new Config();
$userdb = new User_Database($config);
$userdata = new User_Member($userdb);
$session = new User_Session($userdb);
if ($config->IsOnline() && isset($_SESSION["User"]) &&
        $session->Registered(session_id()) &&
        $userdata->CanWritable($_SESSION["User"]["id"])) {
    $vd = new VirtualDirectory($userdb->GetRootPath($_SESSION["User"]["id"]));
    $vd->RenameLast("/Photo/SlideShow/".$_POST["Name"],$_POST["NewName"]);
    
}
$userdb->close();
$config->CloseDB();

