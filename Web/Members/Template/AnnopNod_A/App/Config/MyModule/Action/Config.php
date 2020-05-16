<?php

session_start();
include_once '../../../../../../../Class/DB/Config/DB/Config.php';
include_once '../../../../../../../Class/DB/Config/DB/Software.php';
include_once '../../../../../../../Class/DB/Com/User/SessionManager.php';
include_once '../../../../../../../Class/DB/Com/User/ConfigModule.php';
$DBConfig = new Config_DB_Config();
$SC = new Config_DB_Software($DBConfig);
$Module = new Com_User_ConfigModule($DBConfig);
$Sess = new Com_User_SessionManager($DBConfig);
$DBConfig->Open();
if ($SC->Online() && isset($_POST["FileName"]) && isset($_POST["ModuleName"]) && isset($_SESSION["UserID"]) && $Sess->Registered(session_id())) {
    $fname = '../../../../../../../Class/DB/UserModule/' . $_POST["FileName"];
    if (is_file($fname)) {
        $Module->ConfigModule($_SESSION["UserID"], $_POST);
        header("location: ../Config.php?ModuleName=" . $_POST["FileName"]);
    } else {
        header("location: ../Config.php?ModuleName=" . $_POST["FileName"]);
    }
} else {
    header("location: ../../Config.php");
}