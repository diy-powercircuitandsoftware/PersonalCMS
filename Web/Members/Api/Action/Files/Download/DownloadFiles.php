<?php

session_start();
include_once '../../../../../../Class/Core/Config/Config.php';
include_once '../../../../../../Class/Core/User/Database.php';
include_once '../../../../../../Class/FileIO/VirtualDirectory.php';
include_once '../../../../../../Class/FileIO/FileDownloader.php';
include_once '../../../../../../Class/FileIO/TempFile.php';
$config = new Config();
$udb = new User_Database($config);
$fdownload = new FileDownloader();
$zip = new ZipArchive();
$tmpfile = new TempFile($config->GetDataPath());
if ($config->IsOnline() && isset($_GET["path"])) {
    $vd = new VirtualDirectory($udb->GetFilesPath($_SESSION["User"]["id"]));
    if ($vd->IsFile($_GET["path"])) {
        $fdownload->DownloadFile($vd->DiskPath($_GET["path"]));
    } else if ($vd->IsDir($_GET["path"])) {
        $dirpath = "PersonalCMS_Download_" . $_SESSION["User"]["id"] . "/";
        $tmpfile->mkdir($dirpath);
        $filepath = $tmpfile->realpathsimulation($dirpath . sha1($_GET["path"]) . ".zip");
        if (!file_exists($filepath)) {
            $zip->open($filepath, ZipArchive::CREATE);
            $files = new RecursiveIteratorIterator(
                    new RecursiveDirectoryIterator($vd->DiskPath($_GET["path"]), RecursiveDirectoryIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST
            );
            foreach ($files as $file) {
                if (is_file($file)) {
                    $zip->addFile($file, ($files->getSubPathName()));
                }
            }
            $zip->close();
        }
        $fdownload->DownloadFile($filepath);
    }
   else{
        header("HTTP/1.0 404 Not Found");
   }
} else {
    header("HTTP/1.0 404 Not Found");
}
$udb->close();
$config->close();