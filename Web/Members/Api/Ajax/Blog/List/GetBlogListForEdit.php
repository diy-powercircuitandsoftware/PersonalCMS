<?php

session_start();
include_once '../../../../../../Class/Core/Config/Config.php';
include_once '../../../../../../Class/Com/Blog/Database.php';
include_once '../../../../../../Class/Com/Blog/Manager.php';
$config = new Config();
$blog = new Blog_Manager(new Blog_Database($config));
if ($config->IsOnline() && isset($_SESSION["User"])) {
   echo json_encode($blog->GetBlogList($_SESSION["User"]["id"], $_POST["id"]));
}
 
 