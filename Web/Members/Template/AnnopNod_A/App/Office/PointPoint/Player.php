<?php
session_start();
include_once '../../../../../../../Class/Core/Config/Config.php';
include_once '../../../../../../../Class/Core/UI/NAV.php';
include_once '../../../../../../../Class/Core/Module/Database.php';
include_once '../../../../../../../Class/Com/Event/Database.php';
include_once '../../../../../../../Class/Com/Event/Reader.php';
include_once '../../../../../../../Class/SDK/Module/Basic.php';
include_once '../../../../../Auth/Action/VerifySession.php';
$config = new Config();
$uinav = new UINAV();
$module = new Module_Database($config);
$event = new Event_Reader(new Event_Database($config));
if ($config->IsOnline() && isset($_SESSION["User"])) {
    $modlist = array();
    foreach ($module->LoadModule(Module_Database::Access_Member) as $value) {

        include_once $module->ModulePath . $value["dirname"] . "/init.php";
        $cn = new $value["classname"]();
        $cn->SetUserID($_SESSION["User"]["id"]);
        $modlist[] = $cn;
    }
    ?>
    <!DOCTYPE html>
    <html>
        <head>
            <meta charset="UTF-8">
            <title>Player</title>
            <link rel="stylesheet" type="text/css" href="../../../../../../css/HolyGrail.css">
            <link rel="stylesheet" type="text/css" href="../../../../../../css/PersonalCMS.css">

            <script src="../../../../../../js/dom/SSQueryFW.js"></script>
            <script src="../../../../../../js/io/Ajax.js"></script>
            <script src="../../../../../../js/office/PointPoint/PointPoint.js"></script>
            <script src="../../../../../../js/office/PointPoint/Player/Player.js"></script>
            <script src="../../../../../../js/office/PointPoint/Player/Animation/Hide.js"></script>
            <script>
                var ss = new SSQueryFW();
                ss.DocumentReady(function () {
                    var ajax = new Ajax();
                    var pointpoint = new PointPoint();
                    var player = new PointPoint_Player(document.getElementById("Render"));
                    var sideindex = 0;
                    player.AddAnimation("PointPoint_Animation_Hide", PointPoint_Animation_Hide);
                    if (ss.URLParam()["path"] !== undefined) {
                        ajax.Post("../../../../../../sound/pointpoint/GetAllFiles.php", {}, function (json) {
                            json = JSON.parse(json);
                          
                            for (var i = 0; i < json.length; i++) {
                                player.AddAudioFromResource(json[i],"../../../../../../sound/pointpoint/"+ json[i]);
                            }
                             
                        });



                        var url = ss.URLParam()["path"];
                        if (url.charAt(url.length - 1) === "#") {
                            url = url.slice(0, -1);
                        }
                        player.path = url;
                        ajax.Post("../../../../../Api/Ajax/Office/PointPoint/Manager/LoadAllData.php", {"path": player.path}, function (data) {
                            data = JSON.parse(data);
                            var Slides = data.Slides;
                            for (var i = 0; i < Slides.length; i++) {
                                var parser = new DOMParser();
                                var dom = parser.parseFromString(Slides[i], "text/html").body.querySelector('[pointpoint-type="slide"]');
                                var index = parseInt(dom.getAttribute("pointpoint-index"));
                                pointpoint.ReplaceHtml(index, dom.innerHTML);

                            }
                            if (pointpoint.Count() > 0) {
                                player.Click();
                            }

                        });

                    } else {
                        window.location.replace("index.php");
                    }

                    player.AddPlayerEvent("click", function () {
                        if (player.HasAnimation()) {
                            player.PlayAnimation();
                        } else {
                            if (sideindex < pointpoint.Count()) {
                                var x = pointpoint.Get(sideindex).GetSlide();
                                player.SetDom(x.cloneNode(true));
                                player.FullScreen();
                                sideindex++;
                                ss.S("#LabPage").Html(sideindex);
                            } else {
                                player.End();
                                ss.S("#LabPage").Html("End");
                            }

                        }

                    });


                });
            </script>
        </head>
        <body  class="HolyGrail"  ">
            <label id="LabPage" style="font-size: xx-large;position: absolute;right: 7px;">Start</label>


            <div id="Render" style="border-style: solid;">

            </div>
            <div>
                <ul>
                    <li>Goto</li>

                </ul>
            </div>
        </body>
    </html>
    <?php
} else {
    header("location: ../../../../../Auth/Login.php");
    session_destroy();
}
