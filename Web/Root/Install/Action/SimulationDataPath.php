<?php

include_once '../../../../Class/Core/Config/Config.php';
$config = new Config();
if (isset($_POST["Path"])) {
  echo $config->SimulationDataPath($_POST["Path"]);
    
}