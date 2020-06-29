<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Manager
 *
 * @author annopnod
 */
class FilesACLS_ShareList {
     private $fdb;

    public function __construct(FilesACLS_Database $fd) {
        $this->fdb = $fd;
    }
      public function GetShareList($userid) {
        $data = array();
        $stmt = $this->fdb->prepare('SELECT * FROM event WHERE userid=:userid AND id>:id  LIMIT 30; ');
        $stmt->bindValue(':userid', $userid, SQLITE3_INTEGER);
         $stmt->bindValue(':id', $startlist, SQLITE3_INTEGER);
        $results = $stmt->execute();
        while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
            $data[] = $row;
        }
        return $data;
    }
}
