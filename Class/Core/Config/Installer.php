<?php

define('Installer_ConfigPath_Error', 0);
define('Installer_DataPath_Error', 1);
define('Installer_Config_Error', 2);
define('Installer_OK', 3);

class Installer {

    public function Install($datadir, $rootname, $password) {
         
        if (!is_writable(dirname(__FILE__))) {
            return Installer_ConfigPath_Error;
        }
        if (!file_exists(dirname(__FILE__) . "/ConfigDB.php")) {
            return Installer_ConfigPath_Error;
        }
  
        $realpath = str_replace("\\", "/",  realpath($datadir));
        if (!is_writable($realpath) || !is_readable($realpath)) {
          
            return Installer_DataPath_Error;
        }
        file_put_contents(dirname(__FILE__) . '/Path.php',
                '<?php define( "Config_Data_Path", "' . $realpath . '"); ?>');
        include_once dirname(__FILE__) . '/Path.php';
        include_once dirname(__FILE__) . '/ConfigDB.php';
        $configdb = new ConfigDB($realpath."/Config.DB");
        $configdb->Install($rootname, $password);
        return Installer_OK;
    }

    public function DetectAllowPath($path) {
        $path = realpath($path);
        $notallow = array("Class", "DefaultPages", "Mobile", "Web");
        for ($i = 0; $i < count($notallow); $i++) {
            $napath = realpath($this->GetAppPath() . "/" . $notallow[$i]);
            if (strlen(strpos($path, $napath)) > 0) {
                return false;
            }
        }
        return true;
    }

    public function GetAppPath() {
        return realpath(dirname(__FILE__) . "/../../../");
    }
    public function GetInstallPath() {
        if (file_exists(dirname(__FILE__) . '/Path.php')){
            require_once  dirname(__FILE__) . '/Path.php';
            if (defined("Config_Data_Path")){
                return Config_Data_Path;
            }
        }
        return null;
       
    }
    public function Installed() {
        if (file_exists(dirname(__FILE__) . '/Path.php')){
            require_once  dirname(__FILE__) . '/Path.php';
            return defined("Config_Data_Path");
        }
        return false;
       
    }

}
