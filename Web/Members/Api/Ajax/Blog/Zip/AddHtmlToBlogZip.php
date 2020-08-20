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
    $blog = new OfficeIO_Blog($vd->DiskPath($_POST["Path"]));

    $dom = new DOMDocument();
     
    $dom->loadHTML(mb_convert_encoding($_POST["Html"], 'HTML-ENTITIES', 'UTF-8'));

    foreach ($dom->getElementsByTagName('img') as $img) {
        $x = $img->getAttribute("src_ref");
        $img->setAttribute("src", $x);
    }
    if (isset($_POST["Name"])) {
        $blog->AddHtml($_POST["Name"], $dom->saveHTML());
    } elseif (isset($_POST["ID"])) {
        $blog->AddHtml(intval($_POST["ID"]), $dom->saveHTML());
    }

    echo $blog->Close();
} else {
    echo '0';
}
$userdb->close();
$config->close();
