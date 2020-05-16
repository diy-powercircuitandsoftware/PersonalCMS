<?php

session_start();
include_once '../../../Class/Core/Config/Config.php';
$config = new Config();
$config->Logout();
session_destroy();
header("location: ../../index.php");
