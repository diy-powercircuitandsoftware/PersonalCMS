<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

class Config {

    private $configdb = null;

    public function __construct() {

        $cfgpath = dirname(__FILE__) . '/Path.php';
        if (file_exists($cfgpath)) {
            require $cfgpath;
            include_once dirname(__FILE__) . '/ConfigDB.php';
            if (defined("Config_Data_Path")) {
                $this->configdb = new ConfigDB(Config_Data_Path . "/Config.DB");
            }
        }
    }

    public function Auth($name, $password) {
        return $this->configdb->Auth($name, $password);
    }

    public function CanAuth() {
        return $this->configdb->CanAuth();
    }
    public function CloseDB( ) {
        return $this->configdb->close();
    }
    public function GetDataPath() {
        if (defined("Config_Data_Path")) {
            return Config_Data_Path;
        }
        return null;
    }

    public function GetName() {
        return $this->configdb->GetName();
    }

    public function GetLocalConfigPath() {
        if (defined("Config_Data_Path")) {
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
        return null;
    }

    public function HasRootAuth($sessionid) {
        return $this->configdb->HasRootAuth($sessionid);
    }

    public function InsertValue($key, $val) {
        return $this->configdb->InsertValue($key, $val);
    }

    public function IsOnline() {
        if (!defined("Config_Data_Path")) {
            return false;
        }
        return $this->configdb->IsOnline();
    }

    public function Installed() {
        if (!defined("Config_Data_Path")) {
            return false;
        }
        return $this->configdb->Installed();
    }

    public function Logout() {
        return $this->configdb->Logout();
    }

    public function SessionRegister($sessionid) {
        return $this->configdb->SessionRegister($sessionid);
    }

}
