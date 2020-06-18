<?php

class VirtualDirectory {

    private $DiskDirPath;

    function __construct($path) {
        $this->DiskDirPath = realpath($path);
    }

    public function Copy($s, $d) {
        return copy($this->DiskPath($s), $this->DiskPath($d));
    }

    public function DeleteFile($s) {
        $src = $this->DiskPath($s);

        if (is_dir($src)) {
            $files = new RecursiveIteratorIterator(
                    new RecursiveDirectoryIterator($src, RecursiveDirectoryIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST
            );
            foreach ($files as $file) {
                if (is_dir($file)) {
                    rmdir($file);
                } else if (is_file($file)) {
                    unlink($file);
                }
            }
            if (!in_array("$src", array("/", "\\", ".", ".."))) {
                rmdir($src);
            }
        } else if (is_file($src)) {
            unlink($src);
        }
    }

    public function DiskPath($s) {
        return $this->DiskDirPath . $this->Normalize($s);
    }

    public function FileExists($s) {
        return file_exists($this->DiskPath($s));
    }

    public function GetFilesInformation(...$args) {
        if (!is_dir($this->DiskPath($args[0]))) {
            return array();
        }
        $ExtensionList = null;
        if (count($args) >= 2 && is_array($args[1])) {
            $ExtensionList = $args[1];
        } else if (count($args) >= 2 && is_string($args[1])) {
            $ExtensionList = explode(",", $args[1]);
        }
        $Normalize = $this->Normalize($args[0]);
        $Path = $this->DiskDirPath . $Normalize;
        $FileList = array();
        $files = new DirectoryIterator($Path);
        foreach ($files as $file) {
            $ArrData = array();
            if ($file->isFile() && $ExtensionList == null) {
                $ArrData["name"] = urldecode($file->getBasename());
                $ArrData["type"] = "FILE";
                $ArrData["size"] = $file->getSize();
                $ArrData["ext"] = $file->getExtension();
            } else if ($file->isFile() && in_array(strtolower($file->getExtension()), $ExtensionList)) {
                $ArrData["name"] = urldecode($file->getBasename());
                $ArrData["type"] = "FILE";
                $ArrData["size"] = $file->getSize();
                $ArrData["ext"] = $file->getExtension();
            } else if ($file->isDir()) {
                $ArrData["name"] = urldecode($file->getBasename());
                $ArrData["type"] = "DIR";
                $ArrData["size"] = "";
                $ArrData["ext"] = "";
            }
            if (isset($ArrData["name"])) {
                $ArrData["fullpath"] = $this->Normalize($Normalize . DIRECTORY_SEPARATOR . $file->getBasename());
                $ArrData["modified"] = date("d-m-Y", $file->getMTime());
                $FileList[] = $ArrData;
            }
        }
        usort($FileList, function($a, $b) {
            return strcmp($a["name"], $b["name"]);
        });
        return $FileList;
    }

    public function GetPropertiesFile($s) {
        $path = $this->DiskPath($s);
        if (file_exists($path)) {
            $info = new SplFileInfo($path);
            return array(
                "name" => urldecode($info->getFilename()),
                "size" => $info->getSize(),
                "modified" => date("d-m-Y", $info->getMTime()),
                "ext" => $info->getExtension(),
                "type" => $info->isDir() ? "DIR" : "FILE",
                "fullpath" => $this->Normalize($s),
                "md5" => md5_file($path),
                "sha1" => sha1_file($path)
            );
        }
        return array();
    }

    public function IsDir($s) {
        return is_dir($this->DiskPath($s));
    }

    public function IsFile($s) {
        return is_file($this->DiskPath($s));
    }

    public function Normalize($Path) {
        $ArrayOut = array();
        $ReFormat = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, urldecode($Path));
        $Split = array_filter(explode(DIRECTORY_SEPARATOR, $ReFormat), 'strlen');
        foreach ($Split as $value) {
            if ($value == "..") {
                array_pop($ArrayOut);
            } else if ($value !== ".") {
                $ArrayOut[] = $value;
            }
        }
        return DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $ArrayOut);
    }

    public function MkDIR($s) {
        return mkdir($this->DiskPath($s));
    }

    public function Rename($s, $d) {
        $src = $this->DiskPath($s);
        $dest = $this->DiskPath($d);
        return rename($src, $dest);
    }

    public function ScanDIR($Path) {
        return scandir($this->DiskPath($Path));
    }

    function SearchFiles(...$args) {
        if (count($args) < 2 || !is_dir($this->DiskPath($args[0]))) {
            return array();
        }
        $Normalize = $this->Normalize($args[0]);
        $Path = $this->DiskDirPath . $Normalize;
        $Recursive = new DirectoryIterator($Path);
        $RecursiveAll = count($args) == 3 && $args[2];
        $SearchList = array();
        $FileList = array();
        if (is_string($args[1])) {
            $SearchList = explode(",", $args[1]);
        } else if (is_array($args[1])) {
            $SearchList = $args[1];
        }

        if ($RecursiveAll) {
            $Recursive = new RecursiveIteratorIterator(
                    new RecursiveDirectoryIterator($Path, RecursiveDirectoryIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST
            );
        }
        if (in_array("*.*", $SearchList)) {
            $SearchList = array("*.*");
        }

        foreach ($SearchList as &$sl) {
            $sl = strtolower(urldecode($sl));
        }


        foreach ($Recursive as $file) {
            $addtoarray = false;
            $decodebasename = urlencode($file->getBasename());
            foreach ($SearchList as $sl) {
                if ($sl == "*.*") {
                    $addtoarray = true;
                    break;
                } else if (substr($sl, 0, 2) == "*.") {
                    $addtoarray = ( strtolower($file->getExtension()) == substr($sl, 2));
                    break;
                } else {
                    $addtoarray = ( strpos($decodebasename, $sl) !== false);
                    break;
                }
            }
            if ($addtoarray) {
                $ArrData = array();

                $ArrData["name"] = $file->getBasename();
                if ($file->isFile()) {
                    $ArrData["type"] = "FILE";
                    $ArrData["size"] = $file->getSize();
                    $ArrData["ext"] = $file->getExtension();
                } else {
                    $ArrData["type"] = "DIR";
                    $ArrData["size"] = "";
                    $ArrData["ext"] = "";
                }
                $ArrData["modified"] = date("d-m-Y", $file->getMTime());

                if ($RecursiveAll) {
                    $ArrData["fullpath"] = $Normalize . DIRECTORY_SEPARATOR . $Recursive->getSubPathname();
                } else {
                    $ArrData["fullpath"] = $Normalize . DIRECTORY_SEPARATOR . $file->getBasename();
                }

                $FileList[] = $ArrData;
            }
        }
        usort($FileList, function($a, $b) {
            return strcmp($a["name"], $b["name"]);
        });
        return $FileList;
    }

}
