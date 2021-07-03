<?php

include_once '../../../../../../Class/Core/Config/Config.php';
include_once '../../../../../../Class/Com/FilesACLS/Database.php';
include_once '../../../../../../Class/Com/FilesACLS/ShareList.php';
include_once '../../../../../../Class/FileIO/VirtualDirectory.php';
include_once '../../../../../../Class/Core/User/Database.php';
include_once '../../../../../../Class/FileIO/ImageThumbnail.php';
$config = new Config();
$acls = new FilesACLS_ShareList(new FilesACLS_Database($config));
$userdb = new User_Database($config);
if ($config->IsOnline()) {
    $out = array();
    $id = (int) filter_var($_GET["id"], FILTER_SANITIZE_NUMBER_INT);
    $rootpath = $acls->GetRootShare($id, FilesACLS_Database::Access_Public);
    if ($rootpath != null) {
        $vd = new VirtualDirectory($userdb->GetFilesPath($rootpath["userid"]));
        if ($vd->IsFile($rootpath["fullpath"]) && explode("/", mime_content_type($vd->DiskPath($rootpath["fullpath"])))[0] == "image") {
            CreateImageThumbnail($vd->DiskPath($rootpath["fullpath"]), 30, 30);
        } else if ($vd->IsFile($rootpath["fullpath"] . $_GET["path"]) && explode("/", mime_content_type($vd->DiskPath($rootpath["fullpath"] . $_GET["path"])))[0] == "image") {
            CreateImageThumbnail($vd->DiskPath($rootpath["fullpath"] . $_GET["path"]), 30, 30);
        }
    }
} else {
    echo '0';
}
$acls->Close();

