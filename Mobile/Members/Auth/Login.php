<?php
session_start();
include_once '../../../Class/Core/Config/Config.php';
include_once '../../../Class/Core/UI/NAV.php';
$config = new Config();
$nav = new UINAV();
if (isset($_SESSION["UserID"]) && $config->IsOnline()) {
    header("location: ../Template/index.php");
} else if ($config->IsOnline()) {
    ?>
    <!DOCTYPE html>
    <html>
        <head>
            <meta charset="UTF-8">
             <meta name="viewport" content="width=device-width, initial-scale=1.0">

            <title>Login</title>
            <style>

                body{
                    background-color: cornsilk;

                }
                input{
                    width: 100%;
                    box-sizing: border-box;
                }
            </style>
        </head>
        <body style="margin: 0;padding: 0;">
            <form action="Action/AuthUsingUserID.php" method="POST">
                <label style="background-color: burlywood;width: 100%;display: block;">Login</label>
                <label>UserID:</label>
                <input   type="text" name="UserID" value="" />
                <label>Password:</label>
                <input  type="password" name="Password" value="" />
                <label>Template:</label>
                <select name="tp" style="width: 100%;box-sizing: border-box;">
                    <?php
                    $arrdiff = array();
                    if (isset($_GET["tp"])) {
                        $arrdiff[] = $_GET["tp"];
                        printf(' <option>%s</option>', $_GET["tp"]);
                    }
                    foreach ($nav->FindAllTemplate("../Template/", $arrdiff) as $value) {
                        $bname = basename($value);
                        printf(' <option>%s</option>', $bname);
                    }
                    ?>
                </select>
                <?php
                if (isset($_GET["error"])) {
                    echo '<tr><td colspan="2">error:' . ($_GET["error"]) . '</td></tr>';
                }
                ?>
                <a style="text-decoration: none;" href="../../index.php"> <input style=" display: block;"  type="button" value="Back!"  /></a>
                <input  type="submit" value="Login!" style=" display: block;" /> 
            </form>


        </body>
    </html>
    <?php
} else {
    header("location: ../../../DefaultPages/Offline.php");
}