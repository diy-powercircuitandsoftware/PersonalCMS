<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

class Config extends SQLite3 {

    public function __construct() {
        $cfgpath = dirname(__FILE__) . '/Path.php';
        if (file_exists($cfgpath)) {
            require_once $cfgpath;
            if (!defined("Config_Data_Path")) {
                echo 'configuration mistakes';
                exit();
            }
        } else {
            define("Config_Data_Path", realpath($this->GetAppPath() . "/DefaultFiles/"));
        }

        $this->open($this->GetDataPath() . "/Config.DB");
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

    public function DownloadBigFile() {
        $results = $this->query("SELECT v FROM config WHERE k='dlbigfile';");
        $data = $results->fetchArray();
        if ($data) {
            return $data["v"];
        }
        return "0";
    }

    public function GetDataPath() {
        return Config_Data_Path;
    }

    public function GetAppPath() {
        return realpath(dirname(__FILE__) . "/../../../");
    }

    public function GetLocalConfigPath() {
        $com = $this->GetDataPath() . "/Com/";
        $core = $this->GetDataPath() . "/Core/";
        if (!is_dir($com)) {
            mkdir($com);
        }
        if (!is_dir($core)) {
            mkdir($core);
        }
        return array("Com" => $com, "Core" => $core);
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
        if (strlen(trim($rootname)) == 0 || strlen(trim($password)) == 0 || strlen(trim($datadir)) == 0) {
            return "string not empty";
        }
        $realpath = realpath($datadir);
        if (is_dir($realpath) && is_writable($realpath) && is_readable($realpath) && is_writable(dirname(__FILE__))) {
            file_put_contents(dirname(__FILE__) . '/Path.php', 
                '<?php define( "Config_Data_Path", "' . $realpath . '"); ?>');
            $hash = sha1(sha1("Transp" . $password . "arency"));
            $sql = ('CREATE TABLE IF NOT EXISTS config (
            k VARCHAR (256) PRIMARY KEY,
            v VARCHAR (1024));
            ');
            $sqlinstall = $this->exec($sql);
            $sqlinsert = $this->InsertValue("root", json_encode(array("name" => $rootname, "pw" => $hash)));
            if ($sqlinstall && $sqlinsert) {
                return "ok";
            }
            return "sqlite3 error";
        }

        return sprintf("Directory '%s' Can Not Edit", $realpath);
    }

    public function Installed() {
        $results = $this->query(" SELECT name FROM sqlite_master WHERE name='config';");
        return (!($results->fetchArray() === false)) && is_writable($this->GetDataPath()) && is_readable($this->GetDataPath());
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
