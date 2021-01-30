<?php
session_start();
include_once '../../../../Class/Core/Config/Config.php';
include_once '../../../../Class/Core/User/Database.php';
include_once '../../../../Class/Core/User/Session.php';
$config = new Config();
$ud = new User_Database($config);
$session = new User_Session($ud);

if (isset($_GET["cmd"])&&$_GET["cmd"]=="all"){
    $session->UnRegisterByUserID($_SESSION["User"]["id"]);
    session_destroy();
}else if (isset($_POST["cmd"]) ){
    $session->UnRegister($_POST["cmd"]);
    
}
else{
    $session->UnRegister(session_id());
    session_destroy();
}

header("location: ../../../index.php");
