<?php

include_once '../../../../../Class/Core/Config/Config.php';
include_once '../../../../../Class/Com/FilesACLS/Database.php';
include_once '../../../../../Class/Com/FilesACLS/ShareList.php';
include_once '../../../../../Class/FileIO/VirtualDirectory.php';
include_once '../../../../../Class/Core/User/Database.php';
$config = new Config();
$acls = new FilesACLS_ShareList(new FilesACLS_Database($config));
$userdb = new User_Database($config);
if ($config->IsOnline() && isset($_POST["UserID"])) {
    $out = array();

    if ($_POST["Path"] == "...") {
        $vd = new VirtualDirectory($userdb->GetFilesPath($_POST["UserID"]));
        $sharelist = $acls->GetShareList($_POST["UserID"], FilesACLS_Database::Access_Public);
        foreach ($sharelist as $value) {
            $buffer = $vd->GetTypeOFFile($value["fullpath"]);
            $buffer["fullpath"] = http_build_query(array("fullpath" => $buffer["fullpath"], "id" => $value["id"]));
            $out[] = $buffer;
        }
    } else if (isset($_POST["Path"])) {
        $process=array();
        parse_str($_POST["Path"], $process);
        $rootpath=$acls->GetRootShare($process["id"], FilesACLS_Database::Access_Public);
       if ($rootpath!=null){
      $vd = new VirtualDirectory($userdb->GetFilesPath($rootpath["userid"] ).DIRECTORY_SEPARATOR.$rootpath["fullpath"]);
       $out= $vd->GetFilesList("/");
       //$process["fullpath"]
       }
    }
    echo json_encode($out);
    //id,//fullpath
} else {
    echo '0';
}
$acls->Close();
