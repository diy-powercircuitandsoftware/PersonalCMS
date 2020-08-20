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
            <script src="../../../../../js/dom/SSQueryFW.js"></script>
            <style>
                #VideoList{
                    background-color: cornsilk;
                    border-width: thin;
                    margin-top: 3px;
                }
                .VideoList{
                    cursor: pointer;
                }
            </style>
            <script>
                var ss = new SSQueryFW();
                ss.DocumentReady(function () {
                    var VideoSrc = document.getElementById("VideoSrc");
                    var canvasvideo = document.getElementById("canvasvideo");
                    var canvasctx = canvasvideo.getContext("2d");
                    ss.S("#OptLibrary").Change(function (v) {
                        if (this.value == "-1") {
                            ss.Get("../../../../Api/Ajax/Video/GetAllVideo.php", {}, function (data) {
                                ss.S("#VideoList").Empty();
                                data = JSON.parse(data);
                                for (var i in data) {
                                    ss.S("#VideoList").Append('<li class="VideoList"></li>').Data("index", i).Data("id", data[i]["id"]).Html(data[i]["name"]);
                                }
                            });
                        }
                    });
                    ss.S("#VideoList").Click(function (e) {
                        if (e.target.getAttribute("class") == "VideoList") {
                            VideoSrc.src = "../../../../Api/Action/Files/DownloadFile.php?id=" + e.target.getAttribute("data-id");
                            VideoSrc.play();
                        }
                    });
                    //canvasvideo
                    VideoSrc.onloadedmetadata = function () {

                        canvasvideo.width = VideoSrc.videoWidth;
                        canvasvideo.height = VideoSrc.videoHeight;
                    };
                    VideoSrc.onplay = function () {

                        this.setAttribute("data-playing", "1");
                        /* var canvasvideoplaying = function () {
                         canvasctx.drawImage(VideoSrc, 0, 0, canvasvideo.width, canvasvideo.height);
                         if (VideoSrc.getAttribute("data-playing") == "1") {
                         requestAnimationFrame(canvasvideoplaying);
                         }
                         };
                         canvasvideoplaying();*/
                    };
                    VideoSrc.onpause = function () {
                        this.setAttribute("data-playing", "0");
                    };
                    VideoSrc.addEventListener("ended", function () {
                    });
                });
            </script>
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
                    printf('<img  src="../../../../Api/Action/Profile/Basic/GetUserIcon.php?id=%s" />', $Dat["userid"]);
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
                    <div style="display: flex;flex-direction: column;">
                        <div style="height: 80%;width: 100%;background-color: black;">
                            <canvas id="canvasvideo" style="width: 100%;display: none;"></canvas>
                            <video id="VideoSrc" style="width: 100%; " controls ></video>
                        </div>
                        <div style="height: 20%;">
                            <div>
                                <a href="#">Playing</a>
                                <a href="#">Equalizer</a>
                            </div>
                            <div class="PlayerOption">
                                <span>Repeat</span>
                                <select id="RepeatMode">
                                    <option value="0">None</option>
                                    <option value="1">Repeat</option>
                                    <option value="2">Repeat All</option>
                                </select>
                                <span><input type="checkbox" name="" value="" /> Random</span>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="Aside" style="">
                    <div class="BorderBlock" style="background-color: burlywood; ">
                        <span style="display: block;font-weight: bold;">Library</span>
                        <select id="OptLibrary" style="width: 99%;">
                            <option>==Select==</option>
                            <option value="-1">All Video</option>
                        </select>
                        <ul id="VideoList" style="">

                        </ul>
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
