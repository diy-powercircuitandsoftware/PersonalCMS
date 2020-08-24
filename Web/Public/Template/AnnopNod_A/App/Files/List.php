
<?php
include_once '../../../../../../Class/Core/Config/Config.php';
include_once '../../../../../../Class/Core/UI/NAV.php';
include_once '../../../../../../Class/Core/Module/Database.php';
include_once '../../../../../../Class/Core/User/Database.php';
include_once '../../../../../../Class/Core/User/Member.php';
include_once '../../../../../../Class/Com/Blog/Database.php';
include_once '../../../../../../Class/Com/Blog/Reader.php';
include_once '../../../../../../Class/Com/Event/Database.php';
include_once '../../../../../../Class/Com/Event/Reader.php';
include_once '../../../../../../Class/SDK/Module/Basic.php';
$config = new Config();
$uinav = new UINAV();
$module = new Module_Database($config);
$blog = new Blog_Reader(new Blog_Database($config));
$event = new Event_Reader(new Event_Database($config));
$user = new User_Member(new User_Database($config));
if ($config->IsOnline()) {
    $modlist = array();
    foreach ($module->LoadModule(Module_Database::Access_Public) as $value) {

        include_once $module->ModulePath . $value["dirname"] . "/init.php";
        $modlist[] = new $value["classname"]();
    }
    ?>
    <!DOCTYPE html>
    <html>
        <head>
            <meta charset="UTF-8">
            <title><?php echo $config->GetName(); ?></title>
            <link rel="stylesheet" type="text/css" href="../../../../../css/HolyGrail.css">
            <link rel="stylesheet" type="text/css" href="../../../../../css/PersonalCMS.css">
            <script src="../../../../../js/dom/SSQueryFW.js"></script>
            <script src="../../../../../js/dom/SuperDialog.js"></script>
            <script src="../../../../../js/dom/SearchBox.js"></script>
            <script src="../../../../../js/dom/FilesList.js"></script>
            <script src="../../../../../js/io/Ajax.js"></script>
            <style>
                .BNUserList{
                    cursor: pointer;
                    display: block;
                    text-decoration: none;
                    color: blue;
                }
                #DboxErrorBody
                {
                    color: #444;
                    font-size: 16px;
                    font-weight: normal;
                    text-align: center;
                    width: 100%;
                    border-style: dashed;
                    background-color: white;
                    box-sizing: border-box;
                }

                #DboxErrorBodyMessage
                {
                    line-height: 1.6em;
                    margin: 0 auto;
                    max-width: 600px;
                    padding: 0 20px;
                    text-align: left;
                }

            </style>
            <script>
                var SS = new SSQueryFW();

                SS.DocumentReady(function () {
                    var ajax = new Ajax();
                    var dialog = new SuperDialog();
                    var SB = new SearchBox(document.getElementById("SearchBox"));
                    var FL = new FilesList(document.getElementById("FilesList"));
                    FL.SetPreviewImage("../../../../Api/Action/Files/Download/ImagePreview.php?id=");

                    FL.SetDownload("../../../../Api/Action/Files/Download/Download.php");
                    SB.Input = function (v) {
                        SS.Get("../../../Api/ShareAjax/User/SearchAlias.php", {"Alias": v}, function (data) {
                            data = JSON.parse(data);
                            for (var i = 0; i < data.length; i++) {
                                SB.AddList(data[i]["userid"], data[i]["alias"]);
                            }
                        });
                    };
                    SB.Calllback(function (v) {
                        FL.Clear();
                    });

                    FL.OpenDir(function (v) {

                        ajax.Get("../../../../Api/Ajax/Files/Share/GetACLS.php" + v, function (data) {
                            FL.Clear();
                            data = JSON.parse(data);
                            for (var i in data) {

                                if (data[i]["type"] == "DIR") {
                                    FL.AddDir(data[i]["name"], "?" + data[i]["fullpath"], data[i]["modified"]);
                                } else if (data[i]["type"] == "FILE") {
                                    FL.AddFile(data[i]["name"], "?" + data[i]["fullpath"], data[i]["size"], data[i]["modified"]);
                                }
                            }
                            SS.S("#CHDIRList").Html(v);
                        });

                    });
                    FL.OpenFile(function (v) {
                        var qstringpath = SS.URLParam(v);
                        var ext = qstringpath["?path"].split('.').pop().toLowerCase();
                        if (["mp4", "webm", "ogg", "mp3", "wma", "jpg", "gif", "png", "jpeg"].indexOf(ext) >= 0) {
                            dialog.MediaPlayer("../../../../Api/Action/Files/Download/Download.php" + v, ext);
                        }
                    })

                    SS.S(".BNUserList").Click(function () {
                        FL.OpenDir(ajax.JsonToQueryString({"userid": this.getAttribute("data-id"), "path": "..."}));
                        SS.S("#DboxErrorBody").Hide();
                    });



                });
            </script>
        </head>
        <body  class="HolyGrail">

            <header> 
                <h1 style="width: 100%;text-align: center;"><?php echo $config->GetName(); ?> Website</h1>
            </header>
            <div class="HolyGrail-body">

                <nav>
                    <?php
                    foreach ($uinav->FindAllMenuFile("../../App") as $key => $valueA) {
                        echo '<div class="BorderBlock">';
                        printf(' <div class="TitleCenter">%s</div>', $key);
                        foreach ($valueA as $valueB) {
                            printf('<a class="MenuLink" href="%s">%s</a>', "../../App/" . $valueB["path"], $valueB["name"]);
                        }
                        echo '</div>';
                    }
                    ?>

                    <div class="BorderBlock" style="margin-top: 1px;">
                        <div class="TitleCenter">Template</div>
                        <?php
                        foreach ($uinav->FindAllTemplate("../../../") as $key => $value) {
                            printf('  <a  class="MenuLink" href="%s">%s</a>', $value, $key);
                        }
                        ?>
                    </div>
                    <?php
                    foreach ($modlist as $value) {
                        if ($value->SupportLayout(Module_SDK_Basic::Layout_Nav)) {
                            echo ' <div class="BorderBlock" style="margin-top: ๅpx;" >';
                            printf('<div class="TitleCenter">%s</div>', $value->GetTitle());
                            echo $value->Execute(Module_SDK_Basic::Layout_Nav);
                            echo '</div>';
                        }
                    }
                    ?>
                </nav>

                <main>
                    <div>
                        <label>Location:</label>
                        <span id="CHDIRList"></span>
                    </div>
                    <div id="FilesList">

                    </div>

                    <div id="DboxErrorBody">
                        <div style="margin: 40px 0 0; padding: 0;">
                            <img src="../img/DropBoxError.png" width="574" height="388" alt="DropBoxError"/>
                        </div>
                        <div id="DboxErrorBodyMessage">
                            <h1>Restricted Content</h1> This file is no longer available. For additional information <a href="../About/index.php">Contact <?php echo $config->GetName(); ?> Support</a>.
                        </div>
                    </div>
                </main>
                <aside>
                    <div class="BorderBlock" style="margin-top: 1px;">
                        <div class="TitleCenter">User</div>
                        <?php
                        foreach ($user->GetUserList() as $value) {
                            printf('<a class="BNUserList" data-id="%s">%s</a>', $value["id"], $value["alias"]);
                        }
                        ?>

                        <div id="SearchBox" class="BorderBlock" style="margin-top: 1px;">

                        </div>
                    </div>
                    <?php
                    echo '<div class="BorderBlock" style="margin-top: 1px;">';
                    echo '  <div class="TitleCenter">Event</div>';
                    foreach ($event->GetComingEvent(Event_Database::Access_Public) as $value) {
                        echo '<div>';
                        printf('<a class="MenuLink" href="../Event/Viewer.php?id=%s"><span style="font-weight: bold;">%s</span>', $value["id"], $value["name"]);
                        printf('<div style="color: black;" >%s</div></a>', $value["description"]);
                        echo '</div><hr>';
                    }
                    echo '</div>';
                    foreach ($modlist as $value) {
                        if ($value->SupportLayout(Module_SDK_Basic::Layout_Aside)) {
                            echo ' <div class="BorderBlock" style="margin-top: ๅpx;" >';
                            printf('<div class="TitleCenter">%s</div>', $value->GetTitle());
                            echo $value->Execute(Module_SDK_Basic::Layout_Aside);
                            echo '</div>';
                        }
                    }
                    ?>
                </aside>
            </div>

            <footer>
                <span style="font-weight: bold;display: block;">
                    <?php
                    echo "&COPY;" . date("Y") . " " . $config->GetName();
                    ?>
                </span>  

            </footer>
        </body>
    </html>
    <?php
} else {
    header("location: ../../../../../../DefaultPages/Offline.php");
} 