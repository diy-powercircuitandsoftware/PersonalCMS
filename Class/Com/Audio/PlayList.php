<?php

class Audio_PlayList {

    private $path;

    public function __construct(Audio_Database $ad) {
        $this->path = $ad->path;
    }

    public function GetMyPlayList($userid) {
        $arr = array();
        $pubdir = $this->path . $userid . "/Public/";
        $pridir = $this->path . $userid . "/Members/";
        if (is_dir($pubdir)) {
            foreach (scandir($pubdir) as $value) {
                if (strtolower(pathinfo($value, PATHINFO_EXTENSION))) {
                    $arr[] = basename($value, ".xml");
                }
            }
        }
        if (is_dir($pridir)) {
            foreach (scandir($pridir) as $value) {
                if (strtolower(pathinfo($value, PATHINFO_EXTENSION))) {
                    $arr[] = basename($value, ".xml");
                }
            }
        }
        return $arr;
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
        $xmlstring = "";
        $path = $dir . $name . ".xml";
        if (is_file($path)) {
            $xmlstring = file_get_contents($path);
        }
        $xml = new SimpleXMLElement();
        $xml->addChild('title', 'PHP2: More Parser Stories');
    }

}
