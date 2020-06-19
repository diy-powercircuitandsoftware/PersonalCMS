<?php
include_once '../../../../Class/Core/Config/Config.php';
$config = new Config();
if (!$config->Installed()) {
    if (isset($_POST["path"])&& is_writable($_POST["path"])&& is_dir($_POST["path"])){
      $v=  $config->Install($_POST["name"], $_POST["password"], $_POST["path"]);
    if ($v){
        echo '1';
    }
      
    }else{
        echo 'Path '.$_POST["path"]." Can Not Write";
    }
     
    
} else {
    echo 'PersonalCMS Had Installed';
}