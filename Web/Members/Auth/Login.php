<?php
session_start();
include_once '../../../Class/Core/Config/Config.php';
include_once '../../../Class/Core/User/Database.php';
include_once '../../../Class/Core/UI/NAV.php';

$config = new Config();
$usermodule = new User_Database($config);
$nav = new UINAV();

if (isset($_SESSION["UserID"]) && $config->IsOnline()) {
    header("location: ../Template/index.php");
} else if ($config->IsOnline()) {
    ?>
    <!DOCTYPE html>
    <html>
        <head>
            <meta charset="UTF-8">
            <title>Login</title>
            <style>
                #ScreenCenter{
                    position: absolute;
                    left: 50%;
                    top: 50%;
                    transform: translate(-50%, -50%);
                    border-style: solid;
                }
                body{
                    background-color: cornsilk;

                }
                input{
                    width: 100%;
                    box-sizing: border-box;
                }
            </style>



        </head>
        <body>
            <div style="text-align: right;">
                <label>Login With:</label>
                <a href="FaceDetection.php">Face</a>
            </div>
            <div style="position: relative;width:100vw;height: 100vh;">
                <div id="ScreenCenter">
                    <form action="Action/AuthUsingUserID.php" method="POST">
                        <table>
                            <tr style="background-color: burlywood;">
                                <td colspan="2">
                                    <label class="Title">Login</label>
                                </td>
                            </tr>
                            <tr>
                                <td>UserID:</td>
                                <td><input style="width: 98%;" type="text" name="UserID" value="" /></td>
                            </tr>
                            <tr>
                                <td>Password:</td>
                                <td> <input style="width: 98%; " type="password" name="Password" value="" /></td>
                            </tr>
                            <tr>
                                <td>Template:</td>
                                <td> 
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
                                </td>
                                <?php
                                if (isset($_GET["error"])) {
                                    echo '<tr><td colspan="2">error:' . ($_GET["error"]) . '</td></tr>';
                                }
                                ?>
                            </tr>
                            <?php
                            if ($usermodule->Installed()) {
                                ?>
                                <tr>
                                    <td style="width: 50%;"><a style="width: 99%;display: block;text-decoration: none;"  href="../../index.php">
                                            <input style="width: 100%;display: block;"  type="button" value="Back!"  />
                                        </a>
                                    </td>
                                    <td  style="width: 50%;"> <input  type="submit" value="Login!" style="width: 99%;display: block;" /></td>
                                </tr>
                                <?php
                            }
                            else{
                                ?>
                                <tr><td colspan="2">User Module Error! Please Administrator</td></tr>
                                <?php
                            }
                            ?>

                        </table>


                    </form>
                </div>
            </div>
            <div style=" text-align: right;width: 100vw;">
                <a style="text-decoration: none;" href="../../Root/index.php">Root</a>
            </div>
        </body>
    </html>
    <?php
} else {
    header("location: ../../../DefaultPages/Offline.php");
}