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

    public function AddEvent($userid, $array) {
        try {
            unset($array["id"]);
            $Prepare = $this->DataFilter($array);
              $q = ("INSERT INTO event (id,userid," . implode(",", array_keys($Prepare)) . ") "
            . "VALUES (null,:userid," . rtrim(str_pad("", count(array_keys($Prepare)) * 2, "?,"), ",") . ")"); 
            $stmt = $this->ed->prepare($q);
            $stmt->bindParam(':userid', $userid, SQLITE3_INTEGER);
            $val = array_values($Prepare);
            for ($i = 0; $i < count($val); $i++) {
                $stmt->bindParam($i + 2, $val[$i]);
            }
            $stmt->execute();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function DataFilter($param) {
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

    public function GetMyComingEvent($userid) {
        
    }

}
