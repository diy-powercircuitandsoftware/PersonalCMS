<?php
session_start();
include_once '../../../../../../Class/Core/Config/Config.php';
include_once '../../../../../../Class/Core/UI/NAV.php';
include_once '../../../../../../Class/Core/Module/Database.php';
include_once '../../../../../../Class/Com/Event/Database.php';
include_once '../../../../../../Class/Com/Event/Reader.php';
include_once '../../../../../../Class/SDK/Module/Basic.php';
include_once '../../../../Auth/Action/VerifySession.php';
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
            <title><?php echo basename(__FILE__, ".php"); ?></title>
            <script src="../../../../../../Web/js/dom/SSQueryFW.js"></script>
            <script src="../../../../../../Web/js/dom/PlayingList.js"></script>
            <script src="../../../../../../Web/js/io/Ajax.js"></script>
            <link rel="stylesheet" type="text/css" href="../../../../../../Web/css/PersonalCMS.css">

            <?php
            foreach ($modlist as $value) {
                echo $value->Execute(Module_SDK_Basic::Layout_Head);
            }
            ?>
            <script>
                var ss = new SSQueryFW();
                ss.DocumentReady(function () {
                    var ajax = new Ajax();
                    var playlist = new PlayingList(document.getElementById("AudioList"));
                    var Audio = document.getElementById("Audio");
                    playlist.Select(function (v) {
                        Audio.pause();
                        Audio.src = "../../../../../../Web/Members/Api/Action/Files/Download/DownloadFiles.php?path=" + v;
                        Audio.play();

                    });
                    Audio.addEventListener("ended", function () {
                        playlist.Next();
                    });

                    ajax.Post("../../../../../../Web/Members/Api/Ajax/Audio/List/GetPlayList.php", {}, function (data) {
                        data = JSON.parse(data);

                        for (var i in data) {
                            ss.S("#OptLibrary").Append(data[i], data[i]);
                        }
                        ss.S("#OptSelectLib").Change();
                    });

                    ss.S("#OptLibrary").Change(function () {
                        ajax.Get("../../../../../../Web/Members/Api/Ajax/Audio/List/GetAudioList.php", {"Name": this.value}, function (data) {
                            data = JSON.parse(data);
                            playlist.Empty();
                            for (var i in data) {
                                playlist.AddList(data [i]["path"], data [i]["name"]);
                            }

                        });
                    });
                });
            </script>
        </head>
        <body> 
            <header  >

                <div class="TitleCenter" style=" text-align: right;">
                    <?php
                    printf('<span style="font-weight: bold;cursor: default;">%s</span>', $_SESSION["User"]["alias"]);
                    ?>       
                    <a class="MenuLink" style="display: inline;" href="../../../../Auth/Action/Logout.php">LogOut</a>
                </div>
            </header>
            <nav>
                <?php
                foreach ($uinav->FindAllMenuFile("App") as $key => $valueA) {
                    echo '<div class="MBorderBlock">';
                    printf(' <div class="TitleCenter">%s</div>', $key);
                    foreach ($valueA as $valueB) {
                        printf('  <a class="MenuLink" href="%s">%s</a>', "App/" . $valueB["path"], $valueB["name"]);
                    }
                    echo '</div>';
                }
                foreach ($modlist as $value) {
                    if ($value->SupportLayout(Module_SDK_Basic::Layout_Nav)) {
                        echo ' <div class="MBorderBlock" style="margin-top: ๅpx;" >';
                        printf('<div class="TitleCenter">%s</div>', $value->GetTitle());
                        echo $value->Execute(Module_SDK_Basic::Layout_Nav);
                        echo '</div>';
                    }
                }
                ?>     
            </nav>
            <main>
                <select id="OptLibrary" style="display: block;width: 100%;box-sizing: border-box;">
                    <option>==Select==</option>
                </select>
                <div id="AudioList"></div>
                <audio style="width: 100%;" id="Audio" controls="controls"></audio>
            </main>
            <aside>
                <div class="MBorderBlock" style="margin-top: 1px;">
                    <div class="TitleCenter">Event</div>
                    <?php
                    foreach ($event->GetComingEvent(Event_Database::Access_Member) as $value) {
                        echo '<div>';
                        printf('<a class="MenuLink" href="App/Event/View.php?id=%s"><span style="font-weight: bold;">%s</span>', $value["id"], $value["name"]);
                        printf('<div style="color: black;" >%s</div></a>', $value["description"]);
                        echo '</div><hr>';
                    }
                    ?>
                </div>
                <?php
                foreach ($modlist as $value) {
                    if ($value->SupportLayout(Module_SDK_Basic::Layout_Aside)) {
                        echo ' <div class="MBorderBlock" style="margin-top: ๅpx;" >';
                        printf('<div class="TitleCenter">%s</div>', $value->GetTitle());
                        echo $value->Execute(Module_SDK_Basic::Layout_Aside);
                        echo '</div>';
                    }
                }
                ?>
            </aside>
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
    header("location: ../../Auth/Login.php");
}