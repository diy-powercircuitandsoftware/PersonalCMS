<?php

include_once '../Class/Core/Config/Config.php';
$config = new Config();
if ($config->Installed()) {
    header("location: Public/Template/index.php");
} else {
    header("location: Root/Install/index.php");
}
 //