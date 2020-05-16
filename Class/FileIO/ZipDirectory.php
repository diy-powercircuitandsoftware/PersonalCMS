<?php

class ZipDirectory {

    function __construct() {
        
    }

    function Compress($path) {

        $tmpfile = tempnam("tmp", "zip");
        $zip = new ZipArchive();
        $zip->open($tmpfile, ZipArchive::CREATE);

        $files = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST
        );
        foreach ($files as $file) {
            if (is_file($file)) {
                $zip->addFile($file, urldecode($files->getSubPathName()));
            }
        }

        $zip->close();
        header('Content-Type: application/zip');
        header('Content-Length: ' . filesize($tmpfile));
        header("Content-Disposition: attachment; filename=" . basename($path) . ".zip");
        readfile($tmpfile);
        unlink($tmpfile);
    }

}
