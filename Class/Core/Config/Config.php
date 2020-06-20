<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

class Config extends SQLite3 {

    public $configdata = array();

    public function __construct() {
        $path = dirname(__FILE__) . "/Config.DB";
        $this->open($path);
    }

    public function Auth($name, $password) {
        $hash = sha1(sha1("Transp" . $password . "arency"));
        $results = $this->query(" SELECT v FROM config WHERE k='root';");
        $data = $results->fetchArray();
        $v = json_decode($data["v"], true);
        return $v["name"] == $name && $v["pw"] == $hash;
    }

    public function CanAuth() {
        $results = $this->query(" SELECT v FROM config WHERE k='root';");
        return !($results->fetchArray() === false);
    }

    public function ChRootPW($password, $newpassword) {
        $hash = sha1(sha1("Transp" . $password . "arency"));
        $hashnew = sha1(sha1("Transp" . $newpassword . "arency"));
        $results = $this->query(" SELECT v FROM config WHERE k='root';");
        $data = $results->fetchArray();
        $v = json_decode($data["v"], true);
        if ($v["pw"] == $hash) {
            $v["pw"] = $hashnew;
            return $this->InsertValue("root", json_encode($v));
        }
        return false;
    }

    public function GetDataPath() {
        $results = $this->query(" SELECT v FROM config WHERE k='data';");
        $data = $results->fetchArray();
        if ($data) {
            return $data["v"];
        }
        return "";
    }

    public function GetConfigDIRPath() {
        return realpath(dirname(__FILE__));
    }

    public function GetName() {
        $results = $this->query(" SELECT v FROM config WHERE k='name';");
        $data = $results->fetchArray();
        if ($data) {
            return $data["v"];
        }
        return "PersonalCMS";
    }

    public function HasRootAuth($sessionid) {
        if ($this->Installed()) {
            $results = $this->query(" SELECT v FROM config WHERE k='session';");
            $data = $results->fetchArray();
            $v = json_decode($data["v"], true);
            return $v["session"] == $sessionid && $v["ip"] == $_SERVER['REMOTE_ADDR'];
        }
        return false;
    }

    public function InsertValue($key, $val) {
        try {
            $stmt = $this->prepare("INSERT OR REPLACE INTO config (k,v) Values (:k,:v);");
            $stmt->bindValue(':k', $key, SQLITE3_TEXT);
            $stmt->bindValue(':v', $val, SQLITE3_TEXT);
            $stmt->execute();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function Install($rootname, $password, $datadir) {
        if (is_writable($datadir)) {
            $hash = sha1(sha1("Transp" . $password . "arency"));
            $sql = ('CREATE TABLE IF NOT EXISTS config (
                k       VARCHAR (256)   PRIMARY KEY,
                v VARCHAR (1024));');
            $this->exec($sql);
            return $this->InsertValue("root", json_encode(array("name" => $rootname, "pw" => $hash))) && $this->InsertValue("data", $datadir);
        }
        return false;
    }

    public function Installed() {
        $results = $this->query(" SELECT name FROM sqlite_master WHERE name='config';");
        return !($results->fetchArray() === false);
    }

    public function Logout() {
        return $this->RemoveValue("session");
    }

    public function IsOnline() {
        if ($this->Installed()) {
            $results = $this->query(" SELECT v FROM config WHERE k='online';");
            $rs = $results->fetchArray();
            return $rs && $rs["v"] == "1";
        }
        return false;
    }

    public function RemoveValue($key) {
        try {
            $stmt = $this->prepare("DELETE FROM config  WHERE k=:k;");
            $stmt->bindValue(':k', $key, SQLITE3_TEXT);
            $stmt->execute();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function SessionRegister($sessionid) {
        if ($this->Installed()) {
            $data = array(
                "session" => $sessionid,
                "ip" => $_SERVER['REMOTE_ADDR']
            );
            return $this->InsertValue("session", json_encode($data));
        }
        return false;
    }

}
