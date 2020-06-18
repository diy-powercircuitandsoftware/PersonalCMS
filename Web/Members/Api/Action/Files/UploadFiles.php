<?php
session_start();
include_once '../../../../../Class/Core/Config/Config.php';
include_once '../../../../../Class/Com/Files/Database.php';
include_once '../../../../../Class/FileIO/VirtualDirectory.php';
include_once '../../../../../Class/Core/User/Session.php';
include_once '../../../../../Class/Core/User/Member.php';
include_once '../../../../../Class/Core/User/Database.php';
$config = new Config();
$fd = new Files_Database($config);
$userdb = new User_Database($config);
$session = new User_Session($userdb);
$userdata = new User_Member($userdb);
if ($config->IsOnline() && isset($_SESSION["User"]) &&
        $session->Registered(session_id()) &&
        $userdata->CanWritable($_SESSION["User"]["id"])) {
    $tmppath = "PersonalCMS_" . $_SESSION["User"]["id"];

    if ($_POST["header"] == "206") {
        $tmpfname = sys_get_temp_dir() . "/" . $tmppath . "_" . ($_FILES["file"]["name"]);
        $handle = fopen($tmpfname, "a");
        fwrite($handle, file_get_contents($_FILES["file"]["tmp_name"]));
        fclose($handle);
    } else if ($_POST["header"] == "200" && isset($_POST["path"])) {
        $vd = new VirtualDirectory($fd->GetUserDIR($_SESSION["User"]["id"]));
        $dp = $vd->DiskPath($_POST["path"]);
        if (is_writable($dp)) {
           $name= ( $_POST["file"]);
            $tmpfname = (sys_get_temp_dir() . "/" . $tmppath . "_" . $name); 
            rename($tmpfname, $dp . "/" .$name);
        }
    }
} else {
    echo '0';
}
