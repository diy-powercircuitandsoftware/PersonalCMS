<?php

session_start();
include_once '../../../../../../Class/Core/Config/Config.php';
include_once '../../../../../../Class/Com/Blog/Database.php';
include_once '../../../../../../Class/Com/Blog/Reader.php';
$config = new Config();
$blog = new Blog_Reader(new Blog_Database($config));
if ($config->IsOnline() &&
         isset($_POST["id"]) 
        &&isset($_POST["startid"])
) {
    echo json_encode($blog->SearchBlogUsingKeywordID($_POST["id"], $_POST["startid"], Blog_Database::Access_Public));
 
}
 
 