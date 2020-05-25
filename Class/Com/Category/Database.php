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
class Category_Database extends SQLite3 {

    public function __construct(Config $cfg) {
        $path = $cfg->GetDataPath() . "/Category/";
        if (!is_dir($path)) {
            mkdir($path);
        }

        $this->open($path . "Category.db");
    }

    public function GetAllCategory() {
        $data = array();

        $results = $this->query("SELECT * FROM category;");
        while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
            $data[] = $row;
        }
        return $data;
    }

    public function Install() {
        $install = array();
        $install[0] = ('CREATE TABLE category (
    id   INTEGER       PRIMARY KEY,
    name VARCHAR (256) 
);');
        $install[1] = ('CREATE TABLE hashtag (
    id   INTEGER       PRIMARY KEY,
    name VARCHAR (256) 
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
            $this->exec("DROP TABLE category;");
            $this->exec("DROP TABLE hashtag;");
            $this->exec("VACUUM;");
            return $this->close();
        } catch (Exception $e) {
            return false;
        }
    }

}
