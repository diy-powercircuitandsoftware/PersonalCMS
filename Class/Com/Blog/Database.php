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
class Blog_Database extends SQLite3 {

    public const Access_Public = 0;
    public const Access_Member = 1;

    public function __construct(Config $cfg) {
        $path = $cfg->GetDataPath() . "/Blog/";
        if (!is_dir($path)) {
            mkdir($path);
        }

        $this->open($path . "Blog.db");
    }

    public function Install() {
        $install = array();
        $install[0] = ('
    CREATE TABLE IF NOT EXISTS blog (
    id             INTEGER PRIMARY KEY AUTOINCREMENT,
    title          VARCHAR (256) NOT NULL,
    userid         INTEGER NOT NULL,
    htmlfilepath   VARCHAR (512),  
    description    TEXT ,
    public     BOOLEAN,
    createdatetime DATE,
  
    enable   BOOLEAN);');
        $install[1] = ('
    CREATE TABLE IF NOT EXISTS blogcategory (
    id  INTEGER  NOT NULL PRIMARY KEY,
    blogid     INTEGER NOT NULL,
    categoryid INTEGER,
    keywordid  INTEGER,
    hashtag    INTEGER);');


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
            $this->exec("DROP TABLE blog;");
            $this->exec("DROP TABLE blogcategory;");

            $this->exec("VACUUM;");
            return $this->close();
        } catch (Exception $e) {
            return false;
        }
    }

}
