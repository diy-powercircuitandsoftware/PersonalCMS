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
        $path = $cfg->GetLocalConfigPath()["Com"] . "/Category/";
        if (!is_dir($path)) {
            mkdir($path);
        }

        $this->open($path . "Category.db");
    }

    public function FitterNumberArray($list) {
        $data = array();
        if (is_string($list)) {
            $list = explode(",", $list);
        }
        foreach ($list as $value) {
            $data[] = intval($value);
        }
        return implode(",", $data);
    }

    public function GetAllCategory() {
        $data = array();
        $results = $this->query("SELECT * FROM category;");
        while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
            $data[] = $row;
        }
        return $data;
    }

    public function GetKeywordDataByID($list) {
        $data = array();
        $stmt = $this->prepare("SELECT * FROM keyword WHERE id IN(".$this->FitterNumberArray($list).");");
        $results = $stmt->execute();
        while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
            $data[] = $row;
        }
        return $data;
    }

    public function SearchKeyword($k) {

        $data = array();
        $stmt = $this->prepare("SELECT * FROM keyword WHERE name LIKE :kw;");
        $stmt->bindValue(':kw', $k . "%", SQLITE3_TEXT);
        $results = $stmt->execute();
        while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
            $data[] = $row;
        }
        return $data;
    }

    public function Install() {
        $install = array();
        $install[0] = ('CREATE TABLE IF NOT EXISTS category (
    id   INTEGER       PRIMARY KEY AUTOINCREMENT,
    name VARCHAR (256) 
);');
        $install[1] = ('CREATE TABLE IF NOT EXISTS hashtag (
    id   INTEGER       PRIMARY KEY AUTOINCREMENT,
    name VARCHAR (256) 
);');
        $install[1] = ('CREATE TABLE IF NOT EXISTS keyword (
    id   INTEGER       PRIMARY KEY AUTOINCREMENT,
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
            $this->exec("DROP TABLE keyword;");
            $this->exec("VACUUM;");
            return $this->close();
        } catch (Exception $e) {
            return false;
        }
    }

}
