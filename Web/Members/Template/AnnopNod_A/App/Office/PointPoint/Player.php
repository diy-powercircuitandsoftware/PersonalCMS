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
            <script src="../../../../../../js/dom/SuperDialog.js"></script>
            <script src="../../../../../../js/dom/SSQueryFW.js"></script>
            <script src="../../../../../../js/io/Ajax.js"></script>
            <script src="../../../../../../js/office/PointPoint.js"></script>
            <script>
                var ss = new SSQueryFW();
                ss.DocumentReady(function () {
                    var ajax = new Ajax();
                    var sd = new SuperDialog();
                    var player = new PointPoint_Player(document.getElementById("Render"));
                    var index = 0;
                    if (ss.URLParam()["path"] !== undefined) {
                        var url = ss.URLParam()["path"];
                        var dpw = sd.PleaseWait().ZIndex(999);

                        if (url.charAt(url.length - 1) === "#") {
                            url = url.slice(0, -1);
                        }
                        player.path = url;

                        ajax.Post("../../../../../Api/Ajax/Office/PointPoint/Manager/GetMetaData.php", {"path": player.path}, function (data) {
                            data = JSON.parse(data);

                            if (data !== null && data["app"] === "PointPoint") {

                                for (var i = 0; i < data["slidescount"]; i++) {
                                    player.AddSlide(null);
                                    ss.S("#BNGoto").Append(i, i + 1);

                                }
                                dpw.Close();
                            } else {
                                window.location.replace("index.php");
                            }


                        });

                    } else {
                        window.location.replace("index.php");
                    }

                    player.AddPlayerEvent("click", function () {
                        if (player.IsNull()) {
                            var dialog = sd.PleaseWait();
                            ajax.Post("../../../../../Api/Ajax/Office/PointPoint/Manager/GetSlideData.php", {"path": player.path, "id": player.slidesindex}, function (data) {
                                var data = JSON.parse(data);
                                var pps = new PointPoint_Slide();
                                pps.Serialize(data);
                                player.ReplaceSlideAt(player.slidesindex, pps);
                                ss.S("#LabPage").Html(player.slidesindex + 1);
                                dialog.Close();
                                 
                            });
                        } else {
                            if (!player.NextItem()) {
                                player.NextSlide();
                            }
                        }

                    });
                });
            </script>
        </head>
        <body  class="HolyGrail">

            <div style="text-align: right;">
                <label id="LabPage" style="color: burlywood;font-size: xx-large;">Start</label>
            </div>

            <div id="Render" style="border-style: solid;max-width: 100vw;max-height: 100vh;margin-left: auto;margin-right: auto;">

            </div>

            <div>
                <label>Goto:</label>
                <select id="BNGoto">


                </select>
            </div>



        </body>
    </html>
    <?php
} else {
    header("location: ../../../../../Auth/Login.php");
    session_destroy();
}
