<?php
 
include_once '../../../../../../Class/Core/Config/Config.php';
include_once '../../../../../../Class/Com/Category/Database.php';
$config = new Config();
 $category=new Category_Database($config);
if ($config->IsOnline() ) {
    echo json_encode($category->SearchKeyword($_POST["Keyword"]));
    
}
 
 