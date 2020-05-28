<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of GetBlog
 *
 * @author annopnod
 */
class Event_Reader {

    private $ed;

    public function __construct(Event_Database $ed) {
        $this->ed = $ed;
    }

    public function GetComingEvent($mode) {
        $data = array();
        $stmt = null;
        if ($mode == Event_Database::Access_Member) {
            $stmt = $this->ed->prepare('SELECT id,name,description,latitude,longitude,startdate,stopdate FROM event WHERE  date("now")>=startdate AND date("now")<=stopdate AND enable=1 LIMIT 30; ');
        } else {
            $stmt = $this->ed->prepare('SELECT id,name,description,latitude,longitude,startdate,stopdate FROM event WHERE  date("now")>=startdate AND date("now")<=stopdate AND enable=1 AND public=1 LIMIT 30; ');
        }
        $results = $stmt->execute();
        while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
            $data[] = $row;
        }
        return $data;
    }

    public function ReadEvent($id, $mode) {
        $data = array();
        $stmt = null;
        if ($mode == Event_Database::Access_Member) {
            $stmt = $this->ed->prepare('SELECT * FROM event WHERE  id=:id ; ');
        } else {
            $stmt = $this->ed->prepare('SELECT * FROM event WHERE  id=:id AND public=1; ');
        }
        $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
        $results = $stmt->execute();
        return $results->fetchArray(SQLITE3_ASSOC);
    }

}
