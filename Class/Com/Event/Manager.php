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
class Event_Manager {

    private $ed;

    public function __construct(Event_Database $ed) {
        $this->ed = $ed;
    }

    public function AddEvent($array) {
        try {
            
            $stmt = $this->ed->prepare("INSERT INTO event (id,userid,name,htmlcode,latitude,longitude,startdate,stopdate,public,description,createdatetime,category,enable) "
                    . "VALUES (null,:userid,:name,:htmlcode,:latitude,:longitude,:startdate,:stopdate,:public,:description,:createdatetime,:category,1)");
            $stmt->bindValue(':userid', $userid, SQLITE3_INTEGER);
            $stmt->bindValue(':name', $name, SQLITE3_TEXT);
             $stmt->bindValue(':htmlcode', $htmlcode, SQLITE3_TEXT);
             
             
              $stmt->bindValue(':latitude', $latitude, SQLITE3_TEXT);
               $stmt->bindValue(':longitude', $longitude, SQLITE3_TEXT);
                $stmt->bindValue(':startdate', $startdate);
                 $stmt->bindValue(':stopdate', $stopdate);
            $stmt->execute();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function GetMyComingEvent($userid) {
        
    }
    
     public function PrepareArrayForInsert($param) {
        $out = array();
        $results = $this->ed->query("PRAGMA table_info('event')");
        while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
            $name = $row["name"];
            if (isset($param[$name]) && $param[$name] !== "") {
                $v = null;
                if ($row["type"] == "INTEGER") {
                    $v = intval($param[$name]);
                } else if ($row["type"] == "BOOLEAN") {
                    if (( strtolower($param[$name]) == "true") || (strtolower($param[$name]) == "1") || ($param[$name] === true)) {
                        $v = 1;
                    } else {
                        $v = 0;
                    }
                } else {
                    $v = $param[$name];
                }

                $out[$name] = $v;
            }
        }
        return $out;
    }
     public function PrepareArrayForUpdate($param) {
        $out = array();
        $results = $this->ud->query("PRAGMA table_info('user')");
        while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
            $name = $row["name"];
            if (isset($param[$name]) && $param[$name] !== "") {
                $v = null;
                if ($row["type"] == "INTEGER") {
                    $v = intval($param[$name]);
                } else if ($row["type"] == "BOOLEAN") {
                    if (( strtolower($param[$name]) == "true") || (strtolower($param[$name]) == "1") || ($param[$name] === true)) {
                        $v = 1;
                    } else {
                        $v = 0;
                    }
                } else {
                    $v = $param[$name];
                }

                $out[$name . "=:" . $name] = $v;
            }
        }
        return $out;
    }

}
