<?php

session_start();
include_once '../../../../Class/Core/Config/Config.php';
include_once '../../../../Class/Core/User/Database.php';
include_once '../../../../Class/Core/User/Member.php';
include_once '../../../../Class/Core/User/Session.php';
$config = new Config();
$ud = new User_Database($config);
$user = new User_Member($ud);
$session = new User_Session($ud);
if ($config->IsOnline()) {
    $tp = "";
    if (isset($_POST["tp"])) {
        $tp = "?tp=" . $_POST["tp"];
    }
    $UserID = $_POST["UserID"];
    if ($user->AuthByPassword($UserID, $_POST["Password"])) {
        $session->UnRegister(session_id());
        if ($session->Register(session_id(), $UserID)) {
            $_SESSION["User"] = array_merge(array("session_count" => 1 ), $user->GetProfileData($UserID));
            header("location: ../../Template/index.php" . $tp);
        } else {
            header("location: ../Login.php" . $tp);
        }
    } else {
        header("location: ../Login.php" . $tp);
    }
} else {
    header("location: ../../../../DefaultPages/Offline.php");
}
