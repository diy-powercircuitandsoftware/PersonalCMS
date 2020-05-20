<?php

/**
 * Description of Database
 *
 * @author annopnod
 */
class User_Database extends SQLite3 {

    public $path = "";
    public $profilepath = "";

    public function __construct(Config $cfg) {
        $this->path = $cfg->GetDataPath() . "/User/";
        $this->profilepath = $cfg->GetDataPath() . "/User/Profiles/";
        if (!is_dir($this->path)) {
            mkdir($this->path);
        }
        if (!is_dir($this->profilepath)) {
            mkdir($this->profilepath);
        }
        $this->open($this->path . "User.db");
    }

    public function GetTableFields($table) {
        $out = array();
        $results = $this->query("PRAGMA table_info('" . $table . "')");
        while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
            $out[] = $row;
        }
        return $out;
    }

    public function GetTableList() {
        $out = array();
        $results = $this->query(" SELECT name FROM sqlite_master WHERE type='table';");
        while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
            $out[] = $row["name"];
        }
        return $out;
    }

    public function Install() {
        $install = array();
        $install[0] = ('
    CREATE TABLE IF NOT EXISTS user (
    id       INTEGER PRIMARY KEY AUTOINCREMENT,
    alias    VARCHAR (50),
    password VARCHAR (1000),
    icon     VARCHAR (512),
    phone    VARCHAR (20),
    email    VARCHAR (50),
    address  TEXT,
    addday   DATE,
    writable BOOLEAN,
    enable   BOOLEAN);');
        $install[1] = ('
    CREATE TABLE IF NOT EXISTS session (
    sessionid  VARCHAR (255)  NOT NULL PRIMARY KEY,
    ipaddress  VARCHAR (1000) NOT NULL,
    accesstime DATE           NOT NULL,
    useragent  VARCHAR (1000) NOT NULL,
    userid     INTEGER        NOT NULL);');
        $install[2] = ('
    CREATE TABLE IF NOT EXISTS register (
    phone  VARCHAR (255)    NOT NULL PRIMARY KEY,
    email  VARCHAR (1000)   NOT NULL,
    alias  VARCHAR (1000)   NOT NULL,
    password VARCHAR (1000) NOT NULL);');
        $install[3] = ('
    CREATE TABLE IF NOT EXISTS config (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    userid INTEGER,
    k VARCHAR (256)   NOT NULL,
    v VARCHAR (1000)  NOT NULL);');
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
            $this->exec("DROP TABLE user;");
            $this->exec("DROP TABLE session;");
            $this->exec("DROP TABLE register;");
            $this->exec("DROP TABLE config;");
            $this->exec("VACUUM;");
            return $this->close();
        } catch (Exception $e) {
            return false;
        }
    }

}
