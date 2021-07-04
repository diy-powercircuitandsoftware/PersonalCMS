<?php
include_once '../../../../Class/Core/Config/Config.php';
$config = new Config();
if (!$config->Installed()) {
    echo  $config->Install($_POST["name"], $_POST["password"], $_POST["path"]);      
} else {
    echo 'PersonalCMS Had Installed';
}