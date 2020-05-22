<?php

session_start();
include_once '../../../../../Class/Core/Config/Config.php';
include_once '../../../../../Class/Core/Module/Database.php';
$config = new Config();
$module = new Module_Database($config);
if ($config->HasRootAuth(session_id())) {
    $modpath = '../../../../../Module/';
    if (isset($_FILES["file"])) {
         
        return;
        $zip = new ZipArchive;
        $res = $zip->open($_FILES["file"]["tmpname"][0]);
        if ($res === TRUE) {
            echo 'ssssss';
          //  $zip->extractTo($path);
            //$zip->close();

            echo 'Unzip!';
        }
    } else {
        echo 'tsssssssssssssssssss';
    }
    var_dump($_POST);
    /*

      if ( $module->InstallModule( $_POST["FileName"], GetClassNameFromFile($modpath), $_POST["Layout"])){
      echo 'Install Complete';
      } */
}
$module->close();
