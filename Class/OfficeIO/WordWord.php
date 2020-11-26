<?php

class OfficeIO_WordWord {

    private $zip;

    function __construct($filename) {
        $this->zip = new ZipArchive();
        if (!file_exists($filename)) {
            $this->zip->open($filename, ZipArchive::CREATE);

            $this->zip->addEmptyDir("Image");
            $this->zip->addEmptyDir("Doc");
            $this->zip->addFromString("Metadata", serialize(array(
                "author" => "PersonalCMS@AnnopNod",
                "app" => "WordWord",
                "version" => "1",
                "date" => date("Y-m-d")
            )));
             
        } else {
            $this->zip->open($filename);
        }
    }

    function Close() {
        $this->zip->close();
    }

    function AddImageFile($name, $path) {

        $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
        if (in_array($ext, array(
                    'gif',
                    'jpg',
                    'jpeg',
                    'png'
                ))) {
            return $this->zip->addFile($path, "Image/" . $name);
        }
    }

    function AddDoc($html) {
        $name = $this->GetDocCount();
        $this->zip->addFromString("Doc/" . $name, $html);
    }

    function DeleteImage($name) {
        return $this->zip->deleteName("Image/" . $name);
    }

    function GetImageData($name) {
        return $this->zip->getFromName("Image/" . $name);
    }

    function GetImageList() {

        /*$Dat = array();
        for ($i = 0; $i < $this->zip->numFiles; $i++) {
            $path = $this->zip->getNameIndex($i);
            $fp = $this-><--------------------->($path);
            if ($fp == "Image") {
                $bn = basename($path);
                if ($bn !== $fp) {
                    $Dat[] = array("path" => $path, "name" => $bn);
                }
            }
        }
        return $Dat;*/
    }

    function GetDocCount() {
        $count = -1;
        for ($i = 0; $i < $this->zip->numFiles; $i++) {
            $path = $this->zip->getNameIndex($i);
            $exp = explode("/", $path);
            $as = array_shift($exp);
            if ($as == "Doc") {
                $count++;
            }
        }
        return $count;
    }

    

    function GetDoc($index) {
        return $this->zip->getFromName("Doc/" . $index);
    }

    function GetMetadata() {
        $dat = unserialize($this->zip->getFromName("Metadata"));
        $dat["DocCount"] = $this->GetDocCount();
        return $dat;
    }

    function ReplaceDoc($Index,$html) {
        $Path = "Doc/" . $Index;
        $this->zip->deleteName($Path);
        return $this->zip->addFromString($Path,$html);
    }

}
 
