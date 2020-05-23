<?php

session_start();
include_once '../../../../../Class/Core/Config/Config.php';
include_once '../../../../../Class/Core/Module/Database.php';
$config = new Config();
$module = new Module_Database($config);
if ($config->IsME(session_id(), $_POST["password"])) {
    $modpath = '../../../../../Module/';
    $addlist = array();
    if (isset($_FILES["file"])) {
        $count = 0;
        foreach ($_FILES['file']['name'] as $filename) {
            $zip = new ZipArchive();
            if ($zip->open($_FILES['file']['tmp_name'][$count])) {
                $dirname = basename($filename, ".zip");
                $zip->extractTo($modpath . $dirname);
                $zip->close();
                $addlist[] = $dirname;
            }

            $count++;
        }
    } else {
        $addlist[] = $_POST["dirname"];
    }
    $public = isset($_POST["public"]) &&( $_POST["public"] == "true"||$_POST["public"] == "1");
   $priority= intval($_POST["priority"]);
    foreach (array_values(array_diff($addlist, array(""), array(" "))) as $value) {
        $fullpath = $modpath . $value . "/init.php";
        if (file_exists($fullpath)) {
            $tokens = token_get_all(file_get_contents($fullpath));
            for ($i = 2; $i < count($tokens); $i++) {
                if ($tokens[$i - 2][0] == T_CLASS && $tokens[$i - 1][0] == T_WHITESPACE && $tokens[$i][0] == T_STRING
                ) {
                     $module->AddModule($value, $tokens[$i][1], $public, $priority);
                }
            }
        }
        
    }
    echo '1';
}else{
    echo 'Permission denied';
}
$module->close();
