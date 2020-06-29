<?php 
 include_once '../../../../../Class/Core/Config/Config.php';
 include_once '../../../../../Class/Com/Blog/Database.php';
 include_once '../../../../../Class/Com/Blog/Reader.php';
 include_once '../../../../../Class/Com/Files/Database.php';
 $config=new Config();
 $blog=new Blog_Reader(Blog_Database($config));
 