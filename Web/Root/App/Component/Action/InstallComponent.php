<?php

session_start();
include_once '../../../../../Class/Core/Config/Config.php';
$config = new Config();
if ($config->HasRootAuth(session_id())) {
    $fullpath = '../../../../../Class/Com/'.$_POST["DIR"]."/Database.php";
     include_once $fullpath;
    
        if (file_exists($fullpath)) {
            $tokens = token_get_all(file_get_contents($fullpath));
            for ($i = 2; $i < count($tokens); $i++) {
                if ($tokens[$i - 2][0] == T_CLASS && $tokens[$i - 1][0] == T_WHITESPACE && $tokens[$i][0] == T_STRING
                ) {
                    $class= new $tokens[$i][1]($config);
                   $class->Install();
                }
            }
        }
    echo '1';
}else{
    echo 'Permission denied';
}
