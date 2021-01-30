<?php

session_start();
include_once '../../../../../../Class/Core/Config/Config.php';
include_once '../../../../../../Class/Core/User/Database.php';
include_once '../../../../../../Class/Core/User/Session.php';
include_once '../../../../../../Class/Core/User/Member.php';
include_once '../../../../../../Class/Com/Blog/Database.php';
include_once '../../../../../../Class/Com/Blog/Manager.php';
$config = new Config();
$blog = new Blog_Manager(new Blog_Database($config));
$userdb = new User_Database($config);
$session = new User_Session($userdb);
$userdata = new User_Member($userdb);
if ($config->IsOnline() &&
        isset($_SESSION["User"]) &&
        $session->Registered(session_id()) &&
        $userdata->CanWritable($_SESSION["User"]["id"])
) {
    $kw = array();
    if (isset($_POST["keyword"])) {
        $kw = $_POST["keyword"];
        unset($_POST["keyword"]);
    }
$blog->EditBlog($_SESSION["User"]["id"], $_POST);
  /*  if ($blog->AddBlog($_SESSION["User"]["id"], $_POST)) {
        $lastid= $blog->LastInsertID();
        foreach ($kw as $value) {
            $blog->AddBlogKeyword($lastid, $value);
        }
        
    }*/
    echo '1';
}
else{
    echo '0';
}
 
 