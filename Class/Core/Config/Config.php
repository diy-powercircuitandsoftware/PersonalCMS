<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

/**
 * Description of Config
 *
 * @author annopnod
 */
class Config {

    public $configdata = array();
    public $SuperuserVocabulary = array(
        "admin", "administrator", "root", "su", "sudo", "supervisor"
    );

    public function __construct() {
        $path = dirname(__FILE__) . "/Config.Dat";
        if (file_exists($path) && is_readable($path)) {
            $this->configdata = unserialize(file_get_contents($path));
        }
    }

    public function Auth($sessionid, $password) {
        $hash = sha1(sha1("Transp" . $password . "arency"));
        if ($hash == $this->configdata["Password"]) {
            $this->configdata["SessionID"] = $sessionid;
            $this->configdata["AuthIP"] = $_SERVER['REMOTE_ADDR'];
            return $this->Save();
        }
        return false;
    }

    public function CanWritable() {
        return array(
            array(
                "Name" => "Config",
                "ShortPath" => dirname(__FILE__),
                "Writable" => is_writable(dirname(__FILE__)),
                "Path" => realpath(dirname(__FILE__))
            ),
            array(
                "Name" => "Data",
                "ShortPath" => $this->GetShortDataPath(),
                "Writable" => is_writable($this->GetDataPath()),
                "Path" => $this->GetDataPath()
        ));
    }

    public function ChRootPW($password, $newpassword) {
        $hash = sha1(sha1("Transp" . $password . "arency"));
        if ($hash == $this->configdata["Password"]) {
            $this->configdata["Password"] = sha1(sha1("Transp" . $newpassword . "arency"));
            return $this->Save();
        }
        return false;
    }

    public function GetDataPath() {
        if ($this->GetShortDataPath() !== null) {
            return realpath($this->GetPathRootApp() . $this->GetShortDataPath() . "/");
        }
        return null;
    }

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

    public function Install($password, $datadir) {
        $path = $this->GetPathRootApp() . $datadir . "/";
        if (is_writable($path) && is_dir($path)) {
            $this->configdata["Password"] = sha1(sha1("Transp" . $password . "arency"));
            $this->configdata["DataPath"] = $datadir;
            return $this->Save();
        }
        return false;
    }

    public function Installed() {
        return count($this->configdata) > 0;
    }

    public function IsME($sessionid, $password) {
        $hash = sha1(sha1("Transp" . $password . "arency"));
        return ($hash == $this->configdata["Password"]) &&$this->HasRootAuth($sessionid);
    }

    public function Logout() {
        unset($this->configdata["SessionID"]);
        unset($this->configdata["AuthIP"]);
        return $this->Save();
    }

    public function IsOnline() {
        if ($this->Installed() && isset($this->configdata["Online"])) {
            return $this->configdata["Online"];
        }
        return false;
    }

    public function Save() {

        if (is_writable(dirname(__FILE__))) {
            file_put_contents(dirname(__FILE__) . "/Config.Dat", serialize($this->configdata));
            return true;
        }
        return false;
    }

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

}
