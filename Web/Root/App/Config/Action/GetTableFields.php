<?php

session_start();
include_once '../../../../../Class/Core/Config/Config.php';
$config = new Config();
if ($config->HasRootAuth(session_id())) {
$sp= explode("/",  $_POST["name"]);
    $path = '../../../../../Class/Core/' .$sp[0] . '/Database.php';
    include_once $path;
    $classes = array();
    $tokens = token_get_all(file_get_contents($path));
    for ($i = 2; $i < count($tokens); $i++) {
        if ($tokens[$i - 2][0] == T_CLASS && $tokens[$i - 1][0] == T_WHITESPACE && $tokens[$i][0] == T_STRING
        ) {
            $class_name = $tokens[$i][1];
            $exec = new $class_name($config);
            echo json_encode($exec->GetTableFields($sp[1]));
            $exec->close();
        }
    }
}

