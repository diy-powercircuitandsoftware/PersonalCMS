<?php

session_start();
include_once '../../../../../../../Class/Core/Config/Config.php';
include_once '../../../../../../../Class/Core/User/Session.php';
include_once '../../../../../../../Class/Core/User/Member.php';
include_once '../../../../../../../Class/Core/User/Database.php';
include_once '../../../../../../../Class/Com/FilesACLS/Database.php';
include_once '../../../../../../../Class/Com/FilesACLS/ShareList.php';
$config = new Config();
$userdb = new User_Database($config);
$session = new User_Session($userdb);
$userdata = new User_Member($userdb);
$acls = new FilesACLS_ShareList(new FilesACLS_Database($config));
if ($config->IsOnline() && isset($_SESSION["User"]) &&
        $session->Registered(session_id())) {
    echo json_encode($acls->GetShareListManager($_SESSION["User"]["id"]));
} else {
    echo '0';
}
$userdb->close();
$config->close();
