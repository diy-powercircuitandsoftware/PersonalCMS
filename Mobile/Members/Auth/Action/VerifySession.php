<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (isset($_SESSION["User"])) {
    if ($_SESSION["User"]["session_count"] == 0) {
        $path = dirname(__FILE__, 5) . "/";
        include_once $path . 'Class/Core/Config/Config.php';
        include_once $path . 'Class/Core/User/Database.php';
        include_once $path . 'Class/Core/User/Session.php';

        $session = new User_Session(new User_Database(new Config()));
        if ($session->Registered(session_id())) {
            $_SESSION["User"]["session_count"] = 1;
        } else {
            session_destroy();
        }
    } else {
        $_SESSION["User"]["session_count"] = ($_SESSION["User"]["session_count"] + 1) % 12;
    }
}