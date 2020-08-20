<?php

session_start();
include_once '../../../../../Class/Core/Config/Config.php';
include_once '../../../../../Class/FileIO/VirtualDirectory.php';
include_once '../../../../../Class/FileIO/TempFile.php';
include_once '../../../../../Class/Core/User/Session.php';
include_once '../../../../../Class/Core/User/Member.php';
include_once '../../../../../Class/Core/User/Database.php';
$config = new Config();
$userdb = new User_Database($config);
$session = new User_Session($userdb);
$userdata = new User_Member($userdb);
$tmpfile = new TempFile($config->GetDataPath());
if ($config->IsOnline() && isset($_SESSION["User"]) &&
        $session->Registered(session_id()) &&
        $userdata->CanWritable($_SESSION["User"]["id"])) {
    $prefixname = "PersonalCMS_Upload_" . $_SESSION["User"]["id"];

    if ($_POST["header"] == "206") {
        $handle = $tmpfile->fopen($prefixname . "_" . ($_FILES["file"]["name"]), "a");
        $tmpfile->fwrite($handle, file_get_contents($_FILES["file"]["tmp_name"]));
        $tmpfile->fclose($handle);
        unlink($_FILES["file"]["tmp_name"]);
    } else if ($_POST["header"] == "200" && isset($_POST["path"])) {
        $vd = new VirtualDirectory($userdb->GetFilesPath($_SESSION["User"]["id"]));
        $dp = $vd->DiskPath($_POST["path"]);
        if (is_writable($dp)) {
            rename($tmpfile->getdiskpath($prefixname . "_" . ( $_POST["file"])), $dp . "/" . preg_replace('/\s/', '_', ($_POST["file"])));
        }
    }
} else {
    echo '0';
}
$userdb->close();
$config->close();
