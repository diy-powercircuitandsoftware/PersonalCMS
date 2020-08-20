<?php

session_start();
include_once '../../../../../../Class/Core/Config/Config.php';
include_once '../../../../../../Class/Core/User/Session.php';
include_once '../../../../../../Class/Core/User/Member.php';
include_once '../../../../../../Class/Core/User/Database.php';
include_once '../../../../../../Class/Com/FilesACLS/Database.php';
include_once '../../../../../../Class/Com/FilesACLS/ShareList.php';
$config = new Config();
$userdb = new User_Database($config);
$session = new User_Session($userdb);
$userdata = new User_Member($userdb);
$acls = new FilesACLS_ShareList(new FilesACLS_Database($config));
if ($config->IsOnline() && isset($_POST["AccessList"]) && isset($_SESSION["User"]) &&
        $session->Registered(session_id()) &&
        $userdata->CanWritable($_SESSION["User"]["id"])) {
    $public = array();
    $member = array();
    $delete = array();
    foreach ($_POST["AccessList"] as $key => $value) {
        $cmd = intval($value);
        $id = intval($key);
        if ($cmd == FilesACLS_Database::Access_Public) {
            $public[] = $id;
        } else if ($cmd == FilesACLS_Database::Access_Member) {
            $member[] = $id;
        } else if ($cmd == -1) {
            $delete[] = $id;
        }
    }
    $acls->SetAccess($_SESSION["User"]["id"], $public, FilesACLS_Database::Access_Public);
    $acls->SetAccess($_SESSION["User"]["id"], $member, FilesACLS_Database::Access_Member);
    $acls->Delete($_SESSION["User"]["id"], $delete);
    echo '1';
} else {
    echo '0';
}
$userdb->close();
$config->close();
