<?php

/**
 * Description of Database
 *
 * @author annopnod
 */
class Event_Database extends SQLite3 {

    public const Access_Public = 0;
    public const Access_Member = 1;

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
    id          INTEGER       PRIMARY KEY AUTOINCREMENT,
    userid      INTEGER       NOT NULL   ,
    name        VARCHAR (256) NOT NULL ,
    htmlcode    TEXT          NOT NULL ,
    latitude    TEXT,
    longitude    TEXT,
    startdate   DATE          NOT NULL ,
    stopdate    DATE          NOT NULL ,
    public      BOOLEAN,
    description TEXT,
    createdatetime DATE       DEFAULT current_timestamp ,
    category      INTEGER,
    enable   BOOLEAN          DEFAULT (1) );');
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
