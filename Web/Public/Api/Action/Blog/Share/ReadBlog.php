<?php
session_start();
include_once '../../../../../../Class/Core/Config/Config.php';
include_once '../../../../../../Class/Core/User/Database.php';
include_once '../../../../../../Class/Com/Blog/Database.php';
include_once '../../../../../../Class/Com/Blog/Reader.php';
include_once '../../../../../../Class/FileIO/VirtualDirectory.php';
include_once '../../../../../../Class/OfficeIO/Blog.php';
$config = new Config();
$blog = new Blog_Reader(new Blog_Database($config));
$userdb = new User_Database($config);
  
if ($config->IsOnline() &&  isset($_GET["id"])
) {
    $path = "index.html";
    $data = $blog->GetBlogFilePath($_GET["id"], Blog_Database::Access_Public);
    $vd = new VirtualDirectory($userdb->GetFilesPath($data["userid"]));
    $blogzip = new OfficeIO_Blog($vd->DiskPath($data["htmlfilepath"]));
    if (isset($_GET["path"])) {
        $path = $_GET["path"];
    }
    $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
    if ($ext == "html") {
        $dom = new DOMDocument();
        $dom->loadHTML($blogzip->Get($path));

        foreach ($dom->getElementsByTagName('img') as $img) {
            $x = $img->getAttribute("src");
            $img->setAttribute("src", $_SERVER["SCRIPT_NAME"] . "?" . http_build_query(array("path" => $x, "id" => $_GET["id"])));
        }
        foreach ($dom->getElementsByTagName('a') as $a) {
            $x = $a->getAttribute("href");
            $a->setAttribute("href", $_SERVER["SCRIPT_NAME"] . "?" . http_build_query(array("path" => $x, "id" => $_GET["id"])));
        }
        echo $dom->saveHTML();
    } else if (in_array($ext, array("jpg","jpeg","png","gif"))) {
        $binary = $blogzip->Get($path);
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $MIMETypeAndCharset = $finfo->buffer($binary);
        $MIMETypeAndCharsetArray = explode(';', $MIMETypeAndCharset);
        header("Content-Type: " . $MIMETypeAndCharsetArray[0]);
        echo $binary;
    }
}
 
 