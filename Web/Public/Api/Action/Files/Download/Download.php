<?php

include_once '../../../../../../Class/Core/Config/Config.php';
include_once '../../../../../../Class/Com/FilesACLS/Database.php';
include_once '../../../../../../Class/Com/FilesACLS/ShareList.php';
include_once '../../../../../../Class/FileIO/VirtualDirectory.php';
include_once '../../../../../../Class/FileIO/FileDownloader.php';
include_once '../../../../../../Class/Core/User/Database.php';
$config = new Config();
$acls = new FilesACLS_ShareList(new FilesACLS_Database($config));
$userdb = new User_Database($config);
$fdownload = new FileDownloader();
if ($config->IsOnline()) {
    $out = array();

    $id = (int) filter_var($_GET["id"], FILTER_SANITIZE_NUMBER_INT);

    $rootpath = $acls->GetRootShare($id, FilesACLS_Database::Access_Public);
    if ($rootpath != null) {
        $vd = new VirtualDirectory($userdb->GetFilesPath($rootpath["userid"]));
       if ($vd->IsFile($rootpath["fullpath"])){
            $fdownload->DownloadFile($vd->DiskPath($rootpath["fullpath"]));
       }else  if ($vd->IsFile($rootpath["fullpath"].$_GET["path"])){
            $fdownload->DownloadFile($vd->DiskPath($rootpath["fullpath"].$_GET["path"]));
       }
      
       
    }
} else {
    echo '0';
}
$acls->Close();
