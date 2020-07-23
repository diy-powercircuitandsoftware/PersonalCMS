<?php
session_start();
include_once '../../../../../../Class/DB/Config/DB/Config.php';
include_once '../../../../../../Class/DB/Config/DB/Software.php';
include_once '../../../../../../Class/DB/Com/Session.php';
include_once '../../../../../../Class/DB/Com/User.php';
include_once '../../../../../../Class/DB/Com/Audio.php';
$DBConfig = new Config_DB_Config();
$SC = new Config_DB_Software($DBConfig);
$Sess = new Com_Session($DBConfig);
$User = new Com_User($DBConfig);
$Audio = new Com_Audio($DBConfig);
$DBConfig->Open();
if ($SC->Online() &&isset($_SESSION["UserID"]) && $Sess->Registered(session_id())) {
    ?>
    <!DOCTYPE html>
    <html>
        <head>
            <meta charset="UTF-8">
            <title><?php echo $SC->GetName(); ?></title>
            <link rel="stylesheet" href="../../css/Page.css">
            <style>
                .ItemList{

                    display: inline-block;
                    text-align: center;
                    margin-left: 1px;
                    margin-top: 1px;
                }
                .ItemList img{
                    height: 50px;
                    width: 50px;
                }
            </style>

        </head>
        <body>
            <div id="Header">
                <div style="width: 50%;">
                    <a href="../../index.php">
                        <img  src="../../../../../../File/Resource/Logo.png"/>
                    </a>
                </div>
                <div  style="width: 50%;text-align: right;">
                      <a href="../../index.php">MainPage</a>
                    <?php
                    $Dat = $User->GetBasicUserData($_SESSION["UserID"]);
                    printf('<img  src="../../../../Api/Action/Profile/GetUserIcon.php?id=%s" />', $Dat["userid"]);
                    echo '<span>' . $Dat["alias"] . '</span>';
                    ?>
                     <a  href="../../../../Session/Action/Logout.php">Logout</a>
                </div>
            </div>
            <div class="Container">
                <div class="Nav">
                    <div  class="BorderBlock">
                        <span class="Title" style="display: block;">Cloud</span>
                        <ul>

                            <li><a href="../Audio/index.php">Audio</a></li>
                            <li><a href="../Files/index.php">Files</a></li>
                            <li><a href="../Photo/index.php">Photo</a></li>
                            <li style="font-weight: bold;">Video</li>
                        </ul>
                    </div>
                </div>
                <div class="Section">
                    <div style="width: 90%;margin-left: auto;margin-right: auto;">
                        <a href="Player.php"  class="ItemList" >
                            <img src="img/video.png" />
                            <span style="display: block;">Video</span>
                            <span style="display: block;">Player</span>
                        </a>
                    </div>
                </div>

            </div>
        </body>
    </html>
    <?php
} else {
    header("location: ../../../../Auth/Login.php");
    session_destroy();
}
