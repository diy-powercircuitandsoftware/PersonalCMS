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
class Files_Database extends SQLite3 {

    public const Access_Public = 0;
    public const Access_Member = 1;

    private $path = "";

    public function __construct(Config $cfg) {
        $this->path = $cfg->GetDataPath() . "/Files/";
        if (!is_dir($this->path)) {
            mkdir($this->path);
        }
        $this->open($this->path . "Files.db");
    }

    public function GetUserDIR($userid) {
      $p=  $this->path.$userid."/";
      if (!is_dir($p)){
          mkdir($p);
      }
        return $p;
    }

    public function Install() {
        $install = array();
        $install[0] = ('
    CREATE TABLE IF NOT EXISTS Files (
    id       INTEGER        PRIMARY KEY,
    userid   INTEGER,
    public   BOOLEAN,
    fullpath VARCHAR (1024) 
);');

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
            $this->exec("DROP TABLE Files;");
            $this->exec("VACUUM;");
            return $this->close();
        } catch (Exception $e) {
            return false;
        }
    }

}
