<?php
include_once 'Class/Function/IsMobile.php';
include_once 'Class/Core/Config/Config.php';

$config = new Config();

if ($config->IsOnline()) {
    if (IsMobile()) {
          header("location: Mobile/index.php"); 
    } else {
        header("location: Web/index.php"); 
    }
} else {
     header("location: DefaultPages/Offline.php"); 
}
