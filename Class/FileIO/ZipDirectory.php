<?php

class ZipDirectory {

    private $tmpfile;
    private $zip;

    function __construct() {
        $this->tmpfile = tempnam("tmp", "zip");
        $this->zip = new ZipArchive();
        $this->zip->open($this->tmpfile, ZipArchive::CREATE);
    }

    public function Add($path) {
        $files = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST
        );
        foreach ($files as $file) {
            $this->zip->addFile($file, ($files->getSubPathName()));
        }
    }

    public function GetTempFile() {
        return $this->tmpfile;
    }

    public function Zip() {
        $this->zip->close;
    }

}
