<?php

include_once '../../../../Class/Core/Config/Installer.php';
$install = new Installer();
if ($install->DetectAllowPath($_POST["path"])) {
    if (!$install->Installed()) {
        $output = $install->Install($_POST["path"], $_POST["name"], $_POST["password"]);
        switch ($output) {
            case Installer_ConfigPath_Error:
                echo 'ConfigPath Error';
                break;
            case Installer_DataPath_Error:
                echo 'DataPath Error';
                break;
            case Installer_Config_Error:
                echo 'Config Error';
                break;
            case Installer_OK:
                echo 'OK';
                break;
        }
    } else {
        include_once '../../../../Class/Core/Config/ConfigDB.php';
            
        $config = new ConfigDB($install->GetInstallPath()."/Config.DB");
        if (!$config->Installed()) {
            $out = $config->Install($_POST["name"], $_POST["password"]);
            if ($out) {
                echo 'PersonalCMS Had Installed';
            } else {
                echo 'Configuration Mistake';
            }
        } else {
            echo 'PersonalCMS Had Installed';
        }
    }
} else {
    echo 'Not Allow Path';
}
 