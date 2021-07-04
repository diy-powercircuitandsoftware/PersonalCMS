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
class Audio_Database  extends SQLite3 {

    public const Access_Public = 1;
    public const Access_Member = 0;
    public $path;
    public function __construct(Config $cfg) {
        $this->path = $cfg->GetLocalConfigPath()["Com"] . "/Audio/Share/";
        if (!is_dir( $this->path )) {
            mkdir( $this->path );
        }
        $this->open( $this->path  . "Share.db");
    }

    public function Install() {
        $install = array();
        $install[0] = (' CREATE TABLE IF NOT EXISTS
    playlist (
    id     INTEGER      PRIMARY KEY AUTOINCREMENT,
    userid INT,
    filename   VARCHAR (250),
    public BOOLEAN );');
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
            $this->exec("DROP TABLE playlist;");

            $this->exec("VACUUM;");
            return $this->close();
        } catch (Exception $e) {
            return false;
        }
    }

}
