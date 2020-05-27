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
        $this->ed=$ed;
    }
    public function GetComingEvent() {
        $data = array();
        $stmt = $this->ed->prepare('SELECT id,name,description,latitude,longitude,startdate,stopdate FROM event WHERE  date("now")>=startdate AND date("now")<=stopdate AND enable=1 LIMIT 30; ');
      
        $results = $stmt->execute();
        while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
            $data[] = $row;
        }
        return $data;
    }
}
