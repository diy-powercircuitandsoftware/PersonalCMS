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

            <script>
                var ss = new SSQueryFW();
                ss.DocumentReady(function () {
                    if (ss.URLParam()["path"] !== undefined) {
                        var url = ss.URLParam()["path"];
                        var dpw = sd.PleaseWait().ZIndex(999);

                        if (url.charAt(url.length - 1) === "#") {
                            url = url.slice(0, -1);
                        }
                        domeditor.path = url;

                        ajax.Post("../../../../../Api/Ajax/Office/PointPoint/Manager/GetMetaData.php", {"path": domeditor.path}, function (data) {
                            data = JSON.parse(data);

                            if (data !== null && data["app"] === "PointPoint") {
                                ss.S("#SlidesIndexList").Attr("max", data["slidescount"]);

                                for (var i = 0; i < data["slidescount"]; i++) {
                                    domeditor.InsertSlide(null);
                                }
                                dpw.Close();
                            } else {
                                window.location.replace("index.php");
                            }


                        });

                    } else {
                        window.location.replace("index.php");
                    }



                });
            </script>
        </head>
        <body  class="HolyGrail">

            <div style="text-align: right;">
                <label id="LabPage" style="color: burlywood;font-size: xx-large;">Start</label>
            </div>

            <canvas style="border-style: solid;">

            </canvas>

            <div>
                <label>Goto:</label>
                <select id="BNGoto">
                    <option value="-1">Title</option>

                </select>
            </div>



        </body>
    </html>
    <?php
} else {
    header("location: ../../../../../Auth/Login.php");
    session_destroy();
}
