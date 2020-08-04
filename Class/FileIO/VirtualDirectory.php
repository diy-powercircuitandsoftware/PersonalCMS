<?php

class VirtualDirectory {

    private $DiskDirPath;
    private $RootPath = array("/", "\\");

    function __construct($path) {
        $this->DiskDirPath = realpath($path);
    }

    public function Copy($s, $d) {
        if (($s !== $d) && !in_array($this->Normalize($s), $this->RootPath)) {
            $src = $this->DiskPath($s);
            $dest = $this->DiskPath($d) . DIRECTORY_SEPARATOR;
            if (is_file($src)) {
                return copy($src, $dest . basename($src));
            } else if (is_dir($src)) {
                $dest = $dest . DIRECTORY_SEPARATOR . basename($src) . DIRECTORY_SEPARATOR;
                mkdir($dest);
                foreach ($iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($src, RecursiveDirectoryIterator::SKIP_DOTS),
                RecursiveIteratorIterator::SELF_FIRST) as $item) {
                    if ($item->isDir()) {
                        mkdir($dest . $iterator->getSubPathName());
                    } else {
                        copy($item, $dest . $iterator->getSubPathName());
                    }
                }
                rmdir($src);
            }
        }
        return false;
    }

    public function DeleteFile($s) {
        if (!in_array($this->Normalize($s), $this->RootPath)) {
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
                rmdir($src);
            } else if (is_file($src)) {
                unlink($src);
            }
        }
    }

    public function DiskPath($s) {
        return ($this->DiskDirPath . $this->Normalize($s));
    }

    public function FileExists($s) {
        return file_exists($this->DiskPath($s));
    }

    public function FileGetContents($s) {
        if ($this->FileExists($s)) {
            return file_get_contents($this->DiskPath($s));
        }
        return "";
    }

    public function FilePutContents($s, $data, int $flags = 0, $context = NULL) {
        return file_put_contents($this->DiskPath($s), $data, $flags, $context);
    }

    public function GetFilesList(...$args) {
        if (!is_dir($this->DiskPath($args[0]))) {
            return array();
        }
        $ExtensionList = null;
        if (count($args) >= 2 && is_array($args[1])) {
            $ExtensionList = $args[1];
        } else if (count($args) >= 2 && is_string($args[1])) {
            $ExtensionList = explode(",", $args[1]);
        }
        if ($ExtensionList!==null){
            foreach ($ExtensionList as &$value) {
                $value= strtolower($value);
            }
        }
        $Normalize = $this->Normalize($args[0]);
        $Path = $this->DiskDirPath . $Normalize;
        $FileList = array();
        $files = new DirectoryIterator($Path);
        foreach ($files as $file) {
            $ArrData = array();
            if ($file->isFile() && $ExtensionList == null) {
                $ArrData["name"] = ($file->getFilename());
                $ArrData["type"] = "FILE";
                $ArrData["size"] = $file->getSize();
                $ArrData["ext"] = $file->getExtension();
            } else if ($file->isFile() && in_array(strtolower($file->getExtension()), $ExtensionList)) {
                $ArrData["name"] = ($file->getFilename());
                $ArrData["type"] = "FILE";
                $ArrData["size"] = $file->getSize();
                $ArrData["ext"] = $file->getExtension();
            } else if ($file->isDir()) {
                $ArrData["name"] = ($file->getFilename());
                $ArrData["type"] = "DIR";
                $ArrData["size"] = "";
                $ArrData["ext"] = "";
            }
            if (isset($ArrData["name"])) {
                $ArrData["fullpath"] = $this->Normalize($Normalize . DIRECTORY_SEPARATOR . $file->getFilename());
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
            $a = array(
                "name" => ($info->getFilename()),
                "size" => $info->getSize(),
                "modified" => date("d-m-Y", $info->getMTime()),
                "type" => $info->isDir() ? "DIR" : "FILE",
                "fullpath" => $this->Normalize($s),
            );
            if ($info->isFile()) {
                $a["md5"] = md5_file($path);
                $a["sha1"] = sha1_file($path);
                $a["ext"] = $info->getExtension();
            } else {
                $a["md5"] = "";
                $a["sha1"] = "";
                $a["ext"] = "";
            }
            return $a;
        }
        return array();
    }

    public function GetTypeOFFile($s) {

        $ArrData = array();
        $file = new SplFileInfo($this->DiskPath($s));
        $ArrData["name"] = ($file->getFilename());
        if ($file->isFile()) {
            $ArrData["type"] = "FILE";
            $ArrData["size"] = $file->getSize();
            $ArrData["ext"] = $file->getExtension();
        } else if ($file->isDir()) {
            $ArrData["type"] = "DIR";
            $ArrData["size"] = "";
            $ArrData["ext"] = "";
        }
        $ArrData["modified"] = date("d-m-Y", $file->getMTime());

        $ArrData["fullpath"] = $this->Normalize($s);
        return $ArrData;
    }

    public function IsDir($s) {
        return is_dir($this->DiskPath($s));
    }

    public function IsFile($s) {
        return is_file($this->DiskPath($s));
    }

    public function Normalize($Path) {
        $ArrayOut = array();
        $ReFormat = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, ($Path));
        $Split = array_filter(explode(DIRECTORY_SEPARATOR, $ReFormat), 'strlen');
        foreach ($Split as $value) {
            if ($value == "..") {
                array_pop($ArrayOut);
            } else if ($value !== ".") {
                $ArrayOut[] = ($value);
            }
        }
        return DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $ArrayOut);
    }

    public function MkDIR($s) {
        if (!$this->IsDir($s)) {
            return mkdir($this->DiskPath($s));
        }
        return true;
    }

    public function MoveFiles($s, $d) {
        $isroot = in_array($this->Normalize($s), $this->RootPath) && is_dir($this->DiskPath($s));
        if (($s !== $d) && !$isroot) {
            $src = $this->DiskPath($s);
            $dest = $this->DiskPath($d);
            if (is_file($src)) {
                return rename($src, $dest . DIRECTORY_SEPARATOR . basename($src));
            } else if (is_dir($src)) {
                $dest = $dest . DIRECTORY_SEPARATOR . basename($src) . DIRECTORY_SEPARATOR;
                mkdir($dest);
                foreach ($iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($src, RecursiveDirectoryIterator::SKIP_DOTS),
                RecursiveIteratorIterator::SELF_FIRST) as $item) {
                    rename($item, $dest . DIRECTORY_SEPARATOR . $iterator->getSubPathName());
                }
                rmdir($src);
            }
        }

        return false;
    }

    public function RenameLast($s, $newname) {
        if (!in_array($this->Normalize($s), $this->RootPath) && !in_array($this->Normalize($newname), $this->RootPath)) {
            $src = $this->DiskPath($s);
            $parrent = dirname($src) . "/";
            if (is_file($src)) {
                $ext = pathinfo($src, PATHINFO_EXTENSION);
                return rename($src, $parrent . $newname . "." . $ext);
            } else if (is_dir($src)) {
                return rename($src, $parrent . $newname);
            }
        }
        return false;
    }

    public function ScanDIR($Path) {
        return scandir($this->DiskPath($Path));
    }

    function SearchFiles(...$args) {
        $path = $args[0];
        $nameorext = array();
        $searchall = false;
        $filelist = array();
        $normalize = $this->Normalize($path);
        $realpath = realpath($this->DiskDirPath . $normalize);
        $recursive = new DirectoryIterator($realpath);
        if (count($args) == 2 && is_dir($this->DiskPath($path))) {
            if (is_array($args[1])) {
                $nameorext = $args[1];
            } else if (is_string($args[1])) {
                $nameorext = explode(",", $args[1]);
            } else if (is_bool($args[1])) {
                $searchall = $args[1];
            }
        } else if (count($args) == 3 && is_dir($this->DiskPath($path))) {
            if (is_array($args[1])) {
                $nameorext = $args[1];
            } else if (is_string($args[1])) {
                $nameorext = explode(",", $args[1]);
            }
            $searchall = $args[2];
        } else {
            return array();
        }
        if (in_array("*.*", $nameorext)) {
            $nameorext = array("*.*");
        }
        foreach ($nameorext as &$sl) {
            $sl = strtolower($sl);
        }
        if ($searchall) {
            $recursive = new RecursiveIteratorIterator(
                    new RecursiveDirectoryIterator($realpath, RecursiveDirectoryIterator::SKIP_DOTS), RecursiveIteratorIterator::SELF_FIRST
            );
        }
        foreach ($recursive as $file) {
            $addtoarray = false;
            $decodebasename = $file->getBasename();
            foreach ($nameorext as $sl) {
                if ($sl == "*.*") {
                    $addtoarray = true;
                    break;
                } else if (substr($sl, 0, 2) == "*.") {
                    $addtoarray = ( strtolower($file->getExtension()) == substr($sl, 2));
                    break;
                } else if ($sl !== "") {
                    $addtoarray = ( strpos($decodebasename, $sl) !== false);
                    break;
                }
            }
            if ($addtoarray) {
                $ArrData = array();
                $ArrData["name"] = ($file->getFilename());
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
                if ($searchall) {
                    $ArrData["fullpath"] = $normalize . DIRECTORY_SEPARATOR . $recursive->getSubPathname();
                } else {
                    $ArrData["fullpath"] = $normalize . DIRECTORY_SEPARATOR . $file->getFilename();
                }
                $ArrData["fullpath"] = $this->Normalize($ArrData["fullpath"]);
                $filelist[] = $ArrData;
            }
        }
        usort($filelist, function($a, $b) {
            return strcmp($a["name"], $b["name"]);
        });
        return $filelist;
    }

}
