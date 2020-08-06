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
class Blog_Manager {

    private $bd;

    public function __construct(Blog_Database $bd) {
        $this->bd = $bd;
    }

    public function AddBlog($userid, $array) {
        try {
            unset($array["id"]);
            $Prepare = $this->DataFilter($array);
            $q = ("INSERT INTO blog (id,userid," . implode(",", array_keys($Prepare)) . ") "
                    . "VALUES (null,:userid," . rtrim(str_pad("", count(array_keys($Prepare)) * 2, "?,"), ",") . ")");
            $stmt = $this->bd->prepare($q);
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

    public function AddBlogKeyword($blogid, $array) {
        try {

            $q = ("INSERT INTO blogcategory (blogid,categoryid) VALUES (blogid,categoryid)");
            $stmt = $this->bd->prepare($q);
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

    public function GetBlogMetadata($userid, $id) {
        $data = array();
        $stmt = $this->bd->prepare('SELECT * FROM blog WHERE userid=:userid AND id=id ;');
        $stmt->bindValue(':userid', $userid, SQLITE3_INTEGER);
        $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
        $results = $stmt->execute();
        if ($row = $results->fetchArray(SQLITE3_ASSOC)) {
            $data = $row;
        }
        return $data;
    }

    public function GetBlogList($userid, $startid) {
        $data = array();
        $stmt = $this->bd->prepare('SELECT * FROM blog WHERE userid=:userid AND id>:startid  LIMIT 30; ');
        $stmt->bindValue(':userid', $userid, SQLITE3_INTEGER);
        $stmt->bindValue(':startid', $startid, SQLITE3_INTEGER);
        $results = $stmt->execute();
        while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
            $data[] = $row;
        }
        return $data;
    }

    public function DataFilter($param) {
        $out = array();
        $results = $this->bd->query("PRAGMA table_info('blog')");
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

    public function DeleteBlogList($userid, $idlist) {

        $stmt = $this->bd->prepare('DELETE  FROM blog WHERE userid=:userid AND id IN (' . $this->FilterNumberSQL($idlist) . ') ');
        $stmt->bindValue(':userid', $userid, SQLITE3_INTEGER);
        $stmt->execute();



        $stmt = $this->bd->prepare('DELETE  FROM blog WHERE userid=:userid AND id IN (' . $this->FilterNumberSQL($idlist) . ') ');
        $stmt->bindValue(':userid', $userid, SQLITE3_INTEGER);
        $stmt->execute();
        /*
          DELETE FROM blogcategory
          WHERE EXISTS
          ( SELECT *
          FROM positions
          WHERE positions.position_id = employees.position_id );
         */

        //  $stmt = $this->bd->prepare('DELETE  FROM blogcategory WHERE userid=:userid AND blogid IN (' . $this->FilterNumberSQL($idlist) . ') ');
        //$stmt->execute();
    }

    public function FilterNumberSQL($obj) {
        $Arrprocess = array();
        if (is_array($obj)) {
            $Arrprocess = $obj;
        } else {
            $Arrprocess = explode(",", $obj);
        }

        $out = array();
        foreach ($Arrprocess as $value) {
            if (is_numeric($value)) {
                $out[] = $value;
            }
        }
        return join(",", $out);
    }

    public function LastInsertID() {
        return $this->bd->lastInsertRowID();
    }

}
