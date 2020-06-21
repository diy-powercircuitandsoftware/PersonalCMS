<?php

session_start();
include_once '../../../../../Class/Core/Config/Config.php';
include_once '../../../../../Class/Com/Files/Database.php';
include_once '../../../../../Class/FileIO/VirtualDirectory.php';
include_once '../../../../../Class/FileIO/FileDownloader.php';
include_once '../../../../../Class/FileIO/TempFile.php';
$config = new Config();
$fd = new Files_Database($config);
$fdownload = new FileDownloader();
$zip = new ZipArchive();
$tmpfile = new TempFile($config->GetDataPath());
if ($config->IsOnline() && isset($_GET["path"])) {
    $vd = new VirtualDirectory($fd->GetUserDIR($_SESSION["User"]["id"]));
    if ($vd->IsFile($_GET["path"])) {
        $fdownload->DownloadFile($vd->DiskPath($_GET["path"]));
    } else if ($vd->IsDir($_GET["path"])) {
         $dirpath = "PersonalCMS_Download_" . $_SESSION["User"]["id"] . "/";
        $zipfilename = sha1($_GET["path"]) . ".zip";
        $tmpfile->mkdir($dirpath);
       echo $tmpfile->getdiskpath($dirpath);
        if (!$tmpfile->file_exists($dirpath . $zipfilename)) {
           /* $zip->open($tmpfile->getdiskpath($dirpath) . $zipfilename, ZipArchive::CREATE);
            $files = new RecursiveIteratorIterator(
                    new RecursiveDirectoryIterator($vd->DiskPath($_GET["path"]), RecursiveDirectoryIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST
            );
            foreach ($files as $file) {
                $zip->addFile($file, ($files->getSubPathName()));
            }
            $zip->close();*/
        }
        /* $zip->Add($vd->DiskPath($_GET["path"]));
          $zip->Zip();
          echo ;
         */
    }
} else {
    header("HTTP/1.0 404 Not Found");
}
