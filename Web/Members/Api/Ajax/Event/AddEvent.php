<?php

session_start();
include_once '../../../../../Class/DB/Config/DB/Config.php';
include_once '../../../../../Class/DB/Config/DB/Software.php';
include_once '../../../../../Class/DB/Com/User/SessionManager.php';
include_once '../../../../../Class/DB/Com/Events/Manager.php';
include_once '../../../../../Class/DB/Com/User/Permission.php';
$DBConfig = new Config_DB_Config();
$Sess = new Com_User_SessionManager($DBConfig);
$SC = new Config_DB_Software($DBConfig);
$Event = new Com_Events_Manager($DBConfig);
$UserPermission = new Com_User_Permission($DBConfig);
$DBConfig->Open();
if ($SC->Online()&& $UserPermission->Writable($_SESSION["UserID"]) && isset($_SESSION["UserID"]) && $Sess->Registered(session_id())) {
      echo $Event->InsertEventList($_SESSION["UserID"], $_POST["name"], $_POST["htmlcode"], $_POST["categoryid"], $_POST["placeid"], $_POST["startdate"], $_POST["stopdate"], $_POST["description"], $_POST["authname"], $_POST["password"], $_POST["accessmode"]);
}
 