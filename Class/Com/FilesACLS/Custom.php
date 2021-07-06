<?php

class FilesACLS_Custom {

    public const Access_Public = 1;
    public const Access_Member = 0;

    private $xml = "";

    public function __construct() {
        $this->xml = new DOMDocument('1.0', 'utf-8');

        $Root = $this->xml->createElement("root");
        $Metadata = $this->xml->createElement("metadata");
        $Metadata->appendChild($this->xml->createElement("app", "FilesACLS_Custom"));
        $Metadata->appendChild($this->xml->createElement("version", "1"));
        $Metadata->appendChild($this->xml->createElement("date", date("Y-m-d")));
        $Root->appendChild($this->xml->createElement("access"));
        $Root->appendChild($Metadata);
        $this->xml->appendChild($Root);
    }

    public function AddShareList($Path, $AccessMode) {
        $dom = $this->xml->getElementsByTagName("access")->item(0);
        if ($AccessMode == self::Access_Public) {
            $dom->appendChild($this->xml->createElement("public", $Path));
        } else if ($AccessMode == self::Access_Member) {
            $dom->appendChild($this->xml->createElement("member", $Path));
        }
    }

    public function Exists($name, $accessmode) {



        $public = $this->xml->getElementsByTagName("public");
        for ($i = 0; $i < $public->count(); $i++) {
            if ($public->item($i)->nodeValue == $name) {
                return true;
            }
        }

        if ($accessmode == self::Access_Member) {
            $member = $this->xml->getElementsByTagName("member");
            if ($member->item($i)->nodeValue == $name) {
                return true;
            }
        }
        return false;
    }

    public function GetList($accessmode) {
        $output = array();

        $public = $this->xml->getElementsByTagName("public");
        for ($i = 0; $i < $public->count(); $i++) {
            $output[] = $public->item($i)->nodeValue;
        }

        if ($accessmode == self::Access_Member) {
            $member = $this->xml->getElementsByTagName("member");
            for ($i = 0; $i < $member->count(); $i++) {
                $output[] = $member->item($i)->nodeValue;
            }
        }

        return $output;
    }

    public function GetAllShareList() {
        $output = array();
        $public = $this->xml->getElementsByTagName("public");
        for ($i = 0; $i < $public->count(); $i++) {
            $output[] = array("name" => $public->item($i)->nodeValue, "mode" => self::Access_Public);
        }
        $member = $this->xml->getElementsByTagName("member");
        for ($i = 0; $i < $member->count(); $i++) {
            $output[] = array("name" => $member->item($i)->nodeValue, "mode" => self::Access_Member);
        }

        return $output;
    }

    public function RemoveShare($name) {
        
    }

    public function Load($path) {
        if (file_exists($path)) {
            $this->xml->load($path);
        }
    }

    public function Save($path) {
        return $this->xml->save($path);
    }

}
