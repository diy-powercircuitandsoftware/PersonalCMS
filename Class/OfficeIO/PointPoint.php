<?php

class OfficeIO_PointPoint {

    private $zip;

    const Embed_Audio = "Audio";
    const Embed_Image = "Image";
    const Embed_Slides = "Slides";
    const Embed_Video = "Video";

    function __construct($filename) {
        $this->zip = new ZipArchive();
        if (!file_exists($filename)) {
            $this->zip->open($filename, ZipArchive::CREATE);
            $this->zip->addEmptyDir(self::Embed_Audio);
            $this->zip->addEmptyDir(self::Embed_Image);
            $this->zip->addEmptyDir(self::Embed_Slides);
            $this->zip->addEmptyDir(self::Embed_Video);
            $this->zip->addFromString("Metadata", serialize(array(
                "author" => "PersonalCMS@AnnopNod",
                "app" => "PointPoint",
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

    function AddEmbedFile($newname, $path) {
        $type = finfo_file(finfo_open(FILEINFO_MIME_TYPE), $path);
        $prefix = explode("/", $type);
        if ($newname == null || $newname == "") {
            $newname = basename($path);
        }
        if ($prefix[0] == "audio") {
            $this->zip->addFile($path, self::Embed_Audio . "/" . $newname);
        } else if ($prefix[0] == "image") {
            $this->zip->addFile($path, self::Embed_Image . "/" . $newname);
        } else if ($prefix[0] == "video") {
            $this->zip->addFile($path, self::Embed_Video . "/" . $newname);
        }
    }

    function AddSlideData($svgstring) {
        $name = $this->GetSlidesCount();
        $this->zip->addFromString(self::Embed_Slides . "/" . $name,$svgstring);
    }

    function DeleteEmbed($Path) {
        $protec = array(
            "Metadata", self::Embed_Audio, self::Embed_Image, self::Embed_Slides, self::Embed_Video
        );
        if (!in_array($Path, $protec)) {
            return $this->zip->deleteName($Path);
        }
        return FALSE;
    }

    function GetEmbedData($Path) {
        return $this->zip->getFromName($Path);
    }

    function GetEmbedList($EmbedType) {
        $Dat = array();
        for ($i = 0; $i < $this->zip->numFiles; $i++) {
            $path = $this->zip->getNameIndex($i);
            $exp = explode("/", $path);
            $as = array_shift($exp);
            if ($as == $EmbedType && count($exp) >= 1) {
                $Dat[ implode("/", $exp)] =$path;
            }
        }
        ksort($Dat);
        unset($Dat[""]);
       
        return $Dat;
    }

    function GetMetadata() {
        $dat = unserialize($this->zip->getFromName("Metadata"));
        $dat["slidescount"] = $this->GetSlidesCount();
        return $dat;
    }

    function GetSlidesCount() {
        $count = -1;
        for ($i = 0; $i < $this->zip->numFiles; $i++) {
            $path = $this->zip->getNameIndex($i);
            $exp = explode("/", $path);
            $as = array_shift($exp);
            if ($as == self::Embed_Slides) {
                $count++;
            }
        }
        return $count;
    }

    function GetSlideIndex($index) {
        return unserialize($this->zip->getFromName(self::Embed_Slides . "/" . $index));
    }

    function ReplaceSlide($Index, $svgstring) {
        $Path = self::Embed_Slides . "/" . $Index;
        $this->zip->deleteName($Path);
        return $this->zip->addFromString($Path,$svgstring);
    }

}
 
