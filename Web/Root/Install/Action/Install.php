<?php

include_once '../../../../Class/Core/Config/Config.php';
$config = new Config();
if (!$config->Installed()) {
    if ($config->Install($_POST["Password"], $_POST["DataPath"])) {
        header("location: ../../Auth/index.php");
    } else {
        header("location: ../index.php?error=write protected");
    }
} else {
    header("location: ../../Auth/index.php");
}