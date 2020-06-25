<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Database
 *
 * @author annopnod
 */
class Audio_Database {

    public const Access_Public = 0;
    public const Access_Member = 1;
    public $path;
    public function __construct(Config $cfg) {
        $this->path = $cfg->GetDataPath() . "/Audio/";
        if (!is_dir( $this->path )) {
            mkdir( $this->path );
        }
        $this->open( $this->path  . "Audio.db");
    }

    public function Install() {
        $install = array();
        $install[0] = (' CREATE TABLE IF NOT EXISTS
    playlist (
    id     INTEGER      PRIMARY KEY AUTOINCREMENT,
    userid INT,
    name   VARCHAR (50),
    public BOOLEAN );');
         $install[1] = (' CREATE TABLE IF NOT EXISTS
    playlistfile (
     id         INTEGER PRIMARY KEY AUTOINCREMENT,
    playlistid INTEGER,
    filepath   TEXT );');
        try {
            foreach ($install as $value) {
                $this->exec($value);
            }
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function Uninstall() {
        try {
            $this->exec("DROP TABLE event;");

            $this->exec("VACUUM;");
            return $this->close();
        } catch (Exception $e) {
            return false;
        }
    }

}
