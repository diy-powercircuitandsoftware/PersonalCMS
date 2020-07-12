<?php

include_once '../../../../../Class/Core/Config/Config.php';
include_once '../../../../../Class/Com/FilesACLS/Database.php';
include_once '../../../../../Class/Com/FilesACLS/ShareList.php';
include_once '../../../../../Class/FileIO/VirtualDirectory.php';
include_once '../../../../../Class/Core/User/Database.php';
$config = new Config();
$acls = new FilesACLS_ShareList(new FilesACLS_Database($config));
$userdb = new User_Database($config);
if ($config->IsOnline()) {
    $out = array();

    if ($_GET["path"] == "...") {
        $vd = new VirtualDirectory($userdb->GetFilesPath($_GET["userid"]));
        $sharelist = $acls->GetShareList($_GET["userid"], FilesACLS_Database::Access_Public);
        foreach ($sharelist as $value) {
            $buffer = $vd->GetTypeOFFile($value["fullpath"]);

            if ($buffer["type"] == "DIR") {
                $buffer["fullpath"] = http_build_query(array("path" => "/", "id" => $value["id"]));
            }
            else   if ($buffer["type"] == "FILE") {
                $buffer["fullpath"] = http_build_query(array("path" => $buffer["fullpath"], "id" => $value["id"]));
            }
            $out[] = $buffer;
        }
    } else if (isset($_GET["path"])) {

        $rootpath = $acls->GetRootShare($_GET["id"], FilesACLS_Database::Access_Public);
        if ($rootpath != null) {
            $vd = new VirtualDirectory($userdb->GetFilesPath($rootpath["userid"]) . DIRECTORY_SEPARATOR . $rootpath["fullpath"]);
            foreach ($vd->GetFilesList($_GET["path"]) as $value) {
                $value["fullpath"] = http_build_query(array("path" => $value["fullpath"], "id" => $_GET["id"]));
                $out[] = $value;
            }
        }
    }
    echo json_encode($out);
} else {
    echo '0';
}
$acls->Close();
