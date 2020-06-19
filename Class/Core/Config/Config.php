<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

class Config extends SQLite3 {

    public $configdata = array();

    public function __construct() {
        $path = dirname(__FILE__) . "/Config.DB";
        $this->open($path);
    }

    /* public function Auth($sessionid, $password) {
      $hash = sha1(sha1("Transp" . $password . "arency"));
      if ($hash == $this->configdata["Password"]) {
      $this->configdata["SessionID"] = $sessionid;
      $this->configdata["AuthIP"] = $_SERVER['REMOTE_ADDR'];
      return $this->Save();
      }
      return false;
      }



      public function ChRootPW($password, $newpassword) {
      $hash = sha1(sha1("Transp" . $password . "arency"));
      if ($hash == $this->configdata["Password"]) {
      $this->configdata["Password"] = sha1(sha1("Transp" . $newpassword . "arency"));
      return $this->Save();
      }
      return false;
      }
     */

    public function GetDataPath() {
        $results = $this->query(" SELECT v FROM config WHERE k='data';");
        $data = $results->fetchArray();
        return $data["v"];
        
    }

    public function GetConfigDIRPath() {
        return realpath(dirname(__FILE__));
    }

    /*
      public function GetName() {

      if ($this->Installed() && isset($this->configdata["Name"])) {
      return $this->configdata["Name"];
      }
      return "";
      }

      public function GetPathRootApp() {
      return dirname(__FILE__) . "/../../../";
      }

      public function GetShortDataPath() {
      if (isset($this->configdata["DataPath"])) {
      return $this->configdata["DataPath"];
      }
      return null;
      }

      public function HasRootAuth($sessionid) {
      if ($this->Installed() && isset($this->configdata["SessionID"]) && isset($this->configdata["AuthIP"])) {
      return ($sessionid == $this->configdata["SessionID"]) && ( $_SERVER['REMOTE_ADDR'] == $this->configdata["AuthIP"]);
      }
      }
     */

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
            return $this->InsertValue("rootname", $rootname) && $this->InsertValue("pw", $hash) && $this->InsertValue("data", $datadir);
        }
        return false;
    }

    public function Installed() {
        $results = $this->query(" SELECT name FROM sqlite_master WHERE name='config';");
        return !($results->fetchArray() === false);
    }

    /*
      public function IsME($sessionid, $password) {
      $hash = sha1(sha1("Transp" . $password . "arency"));
      return ($hash == $this->configdata["Password"]) && $this->HasRootAuth($sessionid);
      }

      public function Logout() {
      unset($this->configdata["SessionID"]);
      unset($this->configdata["AuthIP"]);
      return $this->Save();
      }
     */

    public function IsOnline() {
        if ($this->Installed()) {
            $results = $this->query(" SELECT v FROM config WHERE k='online';");
            return ( $results->fetchArray() == "1");
        }
        return false;
    }

    /*


      public function SetValue($password, $data = array()) {
      $hash = sha1(sha1("Transp" . $password . "arency"));
      if ($hash == $this->configdata["Password"]) {
      foreach ($data as $key => $value) {
      $this->configdata[$key] = $value;
      }
      return $this->Save();
      }
      return false;
      }

      public function SimulationDataPath($datadir) {
      return realpath($this->GetPathRootApp() . $datadir . "/");
      }
     */
}
