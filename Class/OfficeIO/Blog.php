<?php

class OfficeIO_Blog {

    private $zip;

    function __construct($filename) {
        $this->zip = new ZipArchive();
        if (file_exists($filename)) {
            $this->zip->open($filename);
        } else {
            $this->zip->open($filename, ZipArchive::CREATE);
        }
    }

    function Close() {
        return $this->zip->close();
    }

    function AddFile($path, $newname) {
        $this->zip->addFile($path, $newname);
    }

    function AddHtml($path, $code) {
        $this->zip->addFromString($path, $code);
    }

    function Get($path) {
        $this->zip->getFromName($path);
    }

    function GetFilesList($path) {
        $out = array();

        $path = $this->Normalize($path);

        if ($path == "/") {

            for ($i = 0; $i < $this->zip->numFiles; $i++) {
                $stat = $this->zip->statIndex($i);
                $expname = explode("/", $stat["name"]);
                $stat["name"] = $expname[0];
                $stat["type"] = "file";
                if (isset($out[$expname[0]])) {
                    $stat["type"] = "dir";
                }
                $out[$expname[0]] = $stat;
            }
        } else {
            $out[".."] = array("name" => "..", "mtime" => "0", "size" => 0,"type"=>"dir");
            $exppath = explode("/", $path);
            $countexppath = count($exppath);
            for ($i = 0; $i < $this->zip->numFiles; $i++) {
                $stat = $this->zip->statIndex($i);
                $expname = explode("/", $this->Normalize($stat["name"]));
                if (count($expname) > $countexppath) {
                    $found = false;
                    for ($j = 0; $j < $countexppath; $j++) {
                        $found = $expname[$j] == $exppath[$j];
                    }
                    if ($found && $expname[$countexppath] !== "") {
                        $stat["name"] = $expname[$countexppath];
                       $stat["type"] = "file";
                        if (isset($out[$expname[$countexppath]])) {
                            $stat["type"] = "dir";
                        }
                        $out[$expname[$countexppath]] = $stat;
                    }
                }
            }
        }

        return array_values($out);
    }

    public function Normalize($Path) {
        $ArrayOut = array();
        $ReFormat = str_replace(array('/', '\\'), "/", ($Path));
        $Split = array_filter(explode("/", $ReFormat), 'strlen');
        foreach ($Split as $value) {
            if ($value == "..") {
                array_pop($ArrayOut);
            } else if ($value !== ".") {
                $ArrayOut[] = ($value);
            }
        }
        return "/" . implode("/", $ArrayOut);
    }

}
