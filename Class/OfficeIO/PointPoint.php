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
            $this->AddSlideData(new PointPointSlide(800, 600));
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

    function AddSlideData(PointPointSlide $pps) {
        $name = $this->GetSlidesCount();
        $this->zip->addFromString(self::Embed_Slides . "/" . $name, serialize($pps->ToArray()));
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

    function ReplaceSlide($Index, PointPointSlide $Data) {
        $Path = self::Embed_Slides . "/" . $Index;
        $this->zip->deleteName($Path);
        return $this->zip->addFromString($Path, serialize($Data->ToArray()));
    }

}

class PointPointSlide {

    private $Metadata = array();
    private $ObjectData = array();

    const File_Type_None = 0;
    const File_Type_Embed = 1;
    const File_Type_Url = 2;
    const File_Type_Resource = 3;
    const ObjectType_Text = "Text";
    const ObjectType_Image = "Image";
    const ObjectType_Video = "Video";

    function __construct($Width = 800, $Height = 600) {
        $this->Metadata = array(
            "Animation" => array(
                "Animation" => "",
                "AnimationTime" => 0
            ),
            "Audio" => array(
                "AudioPath" => "",
                "AudioType" => 0
            ),
            "Background" => "",
            "Dimension" => array(
                "Width" => $Width,
                "Height" => $Height
            ),
            "Name" => "Untitle",
        );
    }

    function AddImage($type, $path, $w, $h, $css) {
        $this->ObjectData[] = array(
            "Animation" => array(
                "Animation" => "",
                "AnimationTime" => self::File_Type_None
            ),
            "Audio" => array(
                "AudioPath" => "",
                "AudioType" => self::File_Type_None
            ),
            "Embed" => array(
                "FileType" => $type,
                "Path" => $path
            ), "Dimension" => array(
                "Width" => $w,
                "Height" => $h
            ),
            "Css" => $css,
            "ObjectType" => self::ObjectType_Image
        );
        return count($this->ObjectData) - 1;
    }

    function AddText($html, $css) {
        $this->ObjectData[] = array(
            "Animation" => array(
                "Animation" => "",
                "AnimationTime" => self::File_Type_None
            ),
            "Audio" => array(
                "AudioPath" => "",
                "AudioType" => self::File_Type_None
            ),
            "Code" => $html,
            "Css" => $css,
            "ObjectType" => self::ObjectType_Text
        );
        return count($this->ObjectData) - 1;
    }

    function SetAudio($path, $type) {
        $this->Metadata["Audio"] = array(
            "AudioPath" => $path,
            "AudioType" => intval($type)
        );
    }

    function SetAnimation($name, $time) {
        $this->Metadata["Animation"] = array(
            "Animation" => $name,
            "AnimationTime" => intval($time)
        );
    }

    function SetBackground($v) {
        $this->Metadata["Background"] = $v;
    }

    function SetObjectAnimation($index, $name, $time) {
        $this->ObjectData[$index]["Animation"] = array(
            "Animation" => $name,
            "AnimationTime" => $time
        );
    }

    function SetObjectAudio($index, $path, $type) {
        $this->ObjectData[$index]["Audio"] = array(
            "AudioPath" => $path,
            "AudioType" => intval($type)
        );
    }

    function ToArray() {
        return array("Metadata" => $this->Metadata, "ObjectData" => $this->ObjectData);
    }

}
