<?php

session_start();
include_once '../../../../../../Class/Core/Config/Config.php';
include_once '../../../../../../Class/FileIO/VirtualDirectory.php';
include_once '../../../../../../Class/Core/User/Session.php';
include_once '../../../../../../Class/Core/User/Member.php';
include_once '../../../../../../Class/Core/User/Database.php';
include_once '../../../../../../Class/OfficeIO/Blog.php';
$config = new Config();
$userdb = new User_Database($config);
$session = new User_Session($userdb);
$userdata = new User_Member($userdb);

if ($config->IsOnline() && isset($_SESSION["User"]) &&
        $session->Registered(session_id()) &&
        $userdata->CanWritable($_SESSION["User"]["id"])) {
    $vd = new VirtualDirectory($userdb->GetFilesPath($_SESSION["User"]["id"]));
    $blog = new OfficeIO_Blog($vd->DiskPath($_GET["path"]));

  $name="";
  
    $fileinfo = new SplFileInfo( ($_GET["name"]));
  
    if (in_array(strtolower($fileinfo->getExtension()), array("html", "htm"))) {
        $html=  mb_convert_encoding($blog->Get($_GET["name"]), 'HTML-ENTITIES', 'UTF-8');
        $dom = new DOMDocument();
        $dom->loadHTML($html);
        foreach ($dom->getElementsByTagName('img') as $img) {
            $x = $img->getAttribute("src");
            $img->setAttribute("src", $_SERVER["SCRIPT_NAME"] . "?" . http_build_query(array("path" => $_GET["path"], "name" =>$x)));
            $img->setAttribute("src_ref", $x);
        }
        echo $dom->saveHTML();
    }
    else  if (in_array(strtolower($fileinfo->getExtension()), array("jpg", "png","gif"))) {
         echo $blog->Get($_GET["name"]);
    }
     
    $blog->Close();
} else {
    echo '0';
}
$userdb->close();
$config->CloseDB();
