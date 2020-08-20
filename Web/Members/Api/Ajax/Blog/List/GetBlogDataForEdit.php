<?php

session_start();
include_once '../../../../../../Class/Core/Config/Config.php';
include_once '../../../../../../Class/Com/Blog/Database.php';
include_once '../../../../../../Class/Com/Blog/Manager.php';
$config = new Config();
$blog = new Blog_Manager(new Blog_Database($config));
if ($config->IsOnline() && isset($_SESSION["User"])) {
    $out=$blog->GetBlogMetadata($_SESSION["User"]["id"], $_POST["id"]);
    $out["category"]=$blog->GetBlogCategory( $_POST["id"]);
   echo json_encode($out);
}
 
 