<?php

session_start();
include_once '../../../../../../../Class/Core/Config/Config.php';
include_once '../../../../../../../Class/FileIO/VirtualDirectory.php';
include_once '../../../../../../../Class/Core/User/Session.php';
include_once '../../../../../../../Class/Core/User/Member.php';
include_once '../../../../../../../Class/Core/User/Database.php';
include_once '../../../../../../../Class/OfficeIO/SQLite.php';
$config = new Config();
$userdb = new User_Database($config);
$session = new User_Session($userdb);
$userdata = new User_Member($userdb);

if ($config->IsOnline() && isset($_SESSION["User"]) &&
        $session->Registered(session_id())   ) {
    $arrout=array();
    $vd = new VirtualDirectory($userdb->GetFilesPath($_SESSION["User"]["id"]));
    $path = $vd->DiskPath($_POST["Path"]);
    $db = new OfficeIO_SQLite($path, SQLITE3_OPEN_READONLY);
     $tablesquery = $db->query($_POST["Query"]);

    while ($data = $tablesquery->fetchArray(SQLITE3_ASSOC)) {
        $arrout[]= $data ;
    }
    echo json_encode($arrout);
     $db->close();
} else {
    echo null;
}
$userdb->close();
$config->CloseDB();
