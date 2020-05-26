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
class Event_Database extends SQLite3 {

    public function __construct(Config $cfg) {
        $path = $cfg->GetDataPath() . "/Event/";
        if (!is_dir($path)) {
            mkdir($path);
        }

        $this->open($path . "Event.db");
    }

    public function Install() {
        $install = array();
        $install[0] = ('
    CREATE TABLE IF NOT EXISTS event (
    id          INTEGER       PRIMARY KEY,
    userid      INTEGER,
    name        VARCHAR (256),
    htmlcode    TEXT,
    latitude    TEXT,
    longitude    TEXT,
    startdate   DATE,
    stopdate    DATE,
    public      BOOLEAN,
    description TEXT
    createdatetime DATE,
    category      INTEGER,
    enable   BOOLEAN);');
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
