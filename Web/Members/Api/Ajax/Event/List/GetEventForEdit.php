<?php
session_start();
include_once '../../../../../../Class/Core/Config/Config.php';
include_once '../../../../../../Class/Com/Event/Database.php';
include_once '../../../../../../Class/Com/Event/Manager.php';
$config = new Config();
$event = new Event_Manager(new Event_Database($config));
if ($config->IsOnline() && isset($_SESSION["User"])) {
 echo   json_encode  (  $event->GetEventForEdit($_POST["ID"],$_SESSION["User"]["id"]));
}
 
 