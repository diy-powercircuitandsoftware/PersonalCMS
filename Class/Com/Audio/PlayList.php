<?php

class Audio_PlayList {

    private $path;

    public function __construct($path) {
        $this->path = $path;
    }

    public function Read() {
        $xmlstring = "";
        if (is_file($this->path)) {
            $xmlstring = file_get_contents($path);
        }
        $xml = new SimpleXMLElement($xmlstring);
        
    }

    public function AddPlayList($userid, $name, $file = array(), $accessmode) {
        $dir = "";
        if ($accessmode == Audio_Database::Access_Public) {
            $dir = $this->path . $userid . "/Public/";
        } else if ($accessmode == Audio_Database::Access_Member) {
            $dir = $this->path . $userid . "/Members/";
        }
        if (!is_dir($dir)) {
            mkdir($dir);
        }

        $path = $dir . $name . ".xml";

        $xml->addChild('title', 'PHP2: More Parser Stories');
    }

}
