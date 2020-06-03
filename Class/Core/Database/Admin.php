<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Root_Admin
 *
 * @author annopnod
 */
class Database_Admin {

    private $sqllite3;

    public function __construct(SQLite3 $sqllite3) {
        $this->sqllite3 = $sqllite3;
    }
   public function close(){
        $this->sqllite3->close();
   }
    public function GetTableFields($table) {
        $out = array();
        $results = $this->sqllite3->query("PRAGMA table_info('" . $table . "')");
        while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
            $out[] = $row;
        }
        return $out;
    }

    public function GetTableList() {
        $out = array();
        $results = $this->sqllite3->query(" SELECT name FROM sqlite_master WHERE type='table';");
        while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
            $out[] = $row["name"];
        }
        return $out;
    }

}
