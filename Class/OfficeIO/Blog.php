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
        $normal = $this->Normalize($newname);
        $this->zip->addFile($path, $normal);
    }

    function AddHtml($path, $code) {
        $this->zip->addFromString($this->Normalize($path), $code);
    }

    function Get($path) {
        if (is_string($path)) {
            return $this->zip->getFromName($path);
        } elseif (is_int($path)) {
            return $this->zip->getFromIndex($path);
        }
    }
    
    function GetStat($path) {
        if (is_string($path)) {
            return $this->zip->statName($path);
        } elseif (is_int($path)) {
            return $this->zip->statIndex($path);
        }
    }

    function GetFilesList($path) {
        $out = array();
        $path = $this->Normalize($path);
        if (in_array($path, array("/", "\\", ""))) {
            for ($i = 0; $i < $this->zip->numFiles; $i++) {
                $stat = $this->zip->statIndex($i);
                $normalname = $this->Normalize($stat["name"]);
                $expname = explode(DIRECTORY_SEPARATOR, $normalname);
                $stat["fullpath"] = $expname[0];
                $stat["name"] = $expname[0];
                $stat["type"] = "file";
                if (isset($out[$expname[0]])) {
                    $stat["type"] = "dir";
                }
                $out[$expname[0]] = $stat;
            }
        } else {
            $exppath = explode(DIRECTORY_SEPARATOR, $path);
            $countexppath = count($exppath);
            for ($i = 0; $i < $this->zip->numFiles; $i++) {
                $stat = $this->zip->statIndex($i);
                $expname = explode(DIRECTORY_SEPARATOR, $this->Normalize($stat["name"]));
                if (count($expname) > $countexppath) {
                    $found = false;
                    for ($j = 0; $j < $countexppath; $j++) {
                        $found = $expname[$j] == $exppath[$j];
                    }
                    if ($found && $expname[$countexppath] !== "") {
                        $stat["name"] = $expname[$countexppath];
                        $stat["fullpath"] = $this->Normalize($path . "/" . $stat["name"]);
                        $stat["type"] = "file";
                        if (isset($out[$expname[$countexppath]])) {
                            $stat["type"] = "dir";
                        }
                        $out[$expname[$countexppath]] = $stat;
                    }
                }
            }
            $out["."] = array("fullpath" => $path, "name" => ".", "mtime" => "0", "size" => 0, "type" => "dir");
            array_pop($exppath);
            $out[".."] = array("fullpath" => implode(DIRECTORY_SEPARATOR, $exppath), "name" => "..", "mtime" => "0", "size" => 0, "type" => "dir");
        }
        $out = array_values($out);
        usort($out, function($a, $b) {
            return strcmp($a["name"], $b["name"]);
        });
        return $out;
    }

    public function Normalize($Path) {
        $ArrayOut = array();
        $ReFormat = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, ($Path));
        $Split = array_filter(explode(DIRECTORY_SEPARATOR, $ReFormat), 'strlen');
        foreach ($Split as $value) {
            if ($value == "..") {
                array_pop($ArrayOut);
            } else if ($value !== ".") {
                $ArrayOut[] = $value;
            }
        }
        return implode(DIRECTORY_SEPARATOR, $ArrayOut);
    }

}
