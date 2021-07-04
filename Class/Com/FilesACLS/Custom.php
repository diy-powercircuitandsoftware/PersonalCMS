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
        $Root->appendChild($this->xml->createElement("public"))->setAttribute("id", "public");
        $Root->appendChild($this->xml->createElement("member"))->setAttribute("id", "member");
        $Root->appendChild($Metadata);
        $this->xml->appendChild($Root);
    }

    public function AddShareList($Path, $AccessMode) {
        if ($AccessMode == self::Access_Public) {
            $this->xml->getElementById("public")->appendChild($this->xml->createElement("path", $Path));
        } else if ($AccessMode == self::Access_Member) {
            $this->xml->getElementById("member")->appendChild($this->xml->createElement("path", $Path));
        }
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
