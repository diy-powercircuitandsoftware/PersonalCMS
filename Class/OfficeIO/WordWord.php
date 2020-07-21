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
            $this->AddDoc(new WordWordDoc());
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

    function AddDoc(WordWordDoc $wwd) {
        $name = $this->GetDocCount();
        $this->zip->addFromString("Doc/" . $name, serialize($wwd->DocData));
    }

    function DeleteImage($name) {
        return $this->zip->deleteName("Image/" . $name);
    }

    function GetImageData($name) {
        return $this->zip->getFromName("Image/" . $name);
    }

    function GetImageList() {

        $Dat = array();
        for ($i = 0; $i < $this->zip->numFiles; $i++) {
            $path = $this->zip->getNameIndex($i);
            $fp = $this->GetFirstDirName($path);
            if ($fp == "Image") {
                $bn = basename($path);
                if ($bn !== $fp) {
                    $Dat[] = array("path" => $path, "name" => $bn);
                }
            }
        }
        return $Dat;
    }

    function GetDocCount() {
        $count = -1;
        for ($i = 0; $i < $this->zip->numFiles; $i++) {
            $path = $this->zip->getNameIndex($i);
            if ($this->GetFirstDirName($path) == "Doc") {
                $count++;
            }
        }
        return $count;
    }

    function GetFirstDirName($Text) {
        $exp = explode("/", $Text);
        if (substr($Text, 0, 1) == "/") {
            return $exp[1];
        } else {
            return $exp[0];
        }
    }

    function GetDoc($index) {
        return unserialize($this->zip->getFromName("Doc/" . $index));
    }

    function GetMetadata() {
        $dat = unserialize($this->zip->getFromName("Metadata"));
        $dat["DocCount"] = $this->GetDocCount();
        return $dat;
    }

    function ReplaceDoc($Index, PointPointSlide $Data) {
        $Path = "Doc/" . $Index;
        $this->zip->deleteName($Path);
        return $this->zip->addFromString($Path, serialize($Data->SlideData));
    }

}

class WordWordDoc {
   

    public $DocData = array();

    const File_Type_None = 0;
    const File_Type_Embed = 1;
    const File_Type_Url = 2;
    const Orientation_Landscape = -1;
    const Orientation_None = 0;
    const Orientation_Portrait = 1;

    function __construct() {
        $this->DocData = array(
            "Background" => "",
            "Html" => "",
            "Object" => array(),
            "Orientation" => self::Orientation_Portrait,
            "Size" => array(
                "Width" => 21.0,
                "Height" => 29.7,
                "Unit" => "cm",
            )
        );
    }

    function AddImage($type, $path, $w, $h, $css) {
        $this->DocData["Object"][] = array(
            "Css" => $css,
            "FileType" => $type,
            "Path" => $path,
            "Width" => $w,
            "Height" => $h,
            "ObjectType" => "Image"
        );
    }

    function SetText($html) {
          $this->DocData["Html"]= $html;
    }

    function SetBackground($v) {
        $this->DocData["Background"] = $v;
    }

}
