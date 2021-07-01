<?php

class OfficeIO_PointPoint {

    private $zip;
    private $metadata;

    const Embed_Audio = "Audio";
    const Embed_Image = "Image";
    const Embed_Slides = "Slides";
    const Embed_Video = "Video";

    function __construct($filename) {
        $this->zip = new ZipArchive();
        $this->metadata = new OfficeIO_PointPoint_Metadata();
        if (!file_exists($filename)) {
            $this->zip->open($filename, ZipArchive::CREATE);
            $this->zip->addEmptyDir(self::Embed_Audio);
            $this->zip->addEmptyDir(self::Embed_Image);
            $this->zip->addEmptyDir(self::Embed_Slides);
            $this->zip->addEmptyDir(self::Embed_Video);
            $this->zip->addFromString("Metadata.xml", $this->metadata->ToXML());
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

    function DeleteEmbed($Path) {
        return $this->zip->deleteName($Path);
    }

    function EditSlideData($index, $string) {

        $this->zip->addFromString(self::Embed_Slides . "/" . $index . ".xml", $string);
    }

    function GetAllSlides() {
        $Slides = [];
        for ($i = 0; $i < $this->zip->numFiles; $i++) {
            $path = $this->zip->getNameIndex($i);
            $exp = explode("/", $path);

            if (array_shift($exp) == self::Embed_Slides) {
                $exp = explode(".", end($exp));
                if (end($exp) == "xml") {
                    $Slides[] = $this->zip->getFromIndex($i);
                }
            }
        }
        return $Slides;
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
                $Dat[implode("/", $exp)] = $path;
            }
        }
        ksort($Dat);
        unset($Dat[""]);

        return $Dat;
    }

    function GetMetadata() {
        $dat = ($this->zip->getFromName("Metadata.xml"));
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

    function GetSlideData($index) {
        return $this->zip->getFromName(self::Embed_Slides . "/" . $index);
    }

}

class OfficeIO_PointPoint_Metadata {

    function __construct(...$args) {
        $this->xml = new DOMDocument('1.0', 'utf-8');
        if (count($args) == 0) {
            $Root = $this->xml->createElement("root");
            $Metadata = $this->xml->createElement("metadata");
            $Metadata->appendChild($this->xml->createElement("author", "PersonalCMS@AnnopNod"));
            $Metadata->appendChild($this->xml->createElement("app", "PointPoint"));
            $Metadata->appendChild($this->xml->createElement("version", "1"));
            $Metadata->appendChild($this->xml->createElement("date", date("Y-m-d")));
            $Root->appendChild($Metadata);
            $this->xml->appendChild($Root);
        } else {
            
        }
    }

    function ToXML() {
        return $this->xml->saveXML();
    }

}
