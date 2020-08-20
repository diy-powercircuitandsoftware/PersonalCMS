<?php
session_start();
include_once '../../../../../../Class/Core/Config/Config.php';
include_once '../../../../../../Class/Core/UI/NAV.php';
include_once '../../../../../../Class/Core/Module/Database.php';
include_once '../../../../../../Class/Com/Event/Database.php';
include_once '../../../../../../Class/Com/Event/Reader.php';
include_once '../../../../../../Class/Com/Blog/Database.php';
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
            <link rel="stylesheet" type="text/css" href="../../../../../css/HolyGrail.css">
            <link rel="stylesheet" type="text/css" href="../../../../../css/PersonalCMS.css">

            <?php
            foreach ($modlist as $value) {
                echo $value->Execute(Module_SDK_Basic::Layout_Head);
            }
            ?>
            <style>
                .AjaxSend{
                    width: 100%;
                    box-sizing: border-box;
                }


            </style>

            <script src="../../../../../js/dom/SelectList.js"></script>
            <script src="../../../../../js/dom/SSQueryFW.js"></script>
            <script src="../../../../../js/dom/SuperDialog.js"></script>
            <script src="../../../../../js/dom/FilesList.js"></script>
            <script src="../../../../../js/io/Ajax.js"></script>
            <script>
                var ss = new SSQueryFW();
                ss.DocumentReady(function () {
                    var sd = new SuperDialog();
                    var ajax = new Ajax();
                    var FL = new FilesList(document.getElementById("FilesList"));
                    var FilePlayList = new SelectList(document.getElementById("FilePlayList"));
                    FL.SetDownload("../../../../Api/Action/Files/DownloadFiles.php?path=");
                    FL.Multiple(true);
                    FL.OpenDir(function (v) {
                        ajax.Post("../../../../Api/Ajax/Files/GetFilesListByExtension.php", {"Path": v, "Ext": "wma,mp3,ogg"}, function (data) {
                            FL.Clear();
                            data = JSON.parse(data);
                            for (var i in data) {
                                if (data[i]["type"] == "DIR") {
                                    FL.AddDir(data[i]["name"], data[i]["fullpath"], data[i]["modified"]);
                                } else if (data[i]["type"] == "FILE") {
                                    FL.AddFile(data[i]["name"], data[i]["fullpath"], data[i]["size"], data[i]["modified"]);
                                }
                            }
                            ss.S("#CHDIRList").Html((v));
                        });
                    });
                    FL.OpenFile(function (v) {
                        if (["mp4", "webm", "ogg", "mp3", "wma", "jpg", "gif", "png", "jpeg"].indexOf(v.split('.').pop().toLowerCase()) >= 0) {
                            sd.MediaPlayer("../../../../Api/Action/Files/DownloadFiles.php?path=" + (v));
                        }
                    })
                    FL.OpenDir("/");

                    function GetPlayList() {
                        ajax.Post("../../../../Api/Ajax/Audio/GetPlayList.php", {}, function (data) {
                            data = JSON.parse(data);
                            ss.S("#OptSelectLib").Empty();
                            for (var i in data) {
                                ss.S("#OptSelectLib").Append(data[i], data[i]);
                            }
                            ss.S("#OptSelectLib").Change();
                        });
                    }
                    ss.S("#BNAddFile").Click(function () {
                        ajax.Post("../../../../Api/Ajax/Audio/AddAudioToPlayList.php", {"Name": ss.S("#OptSelectLib").Val(), "Path": FL.GetSelectFiles()}, function (data) {
                            ss.S("#OptSelectLib").Change();

                        });
                    });

                    ss.S("#BNNewPlayList").Click(function () {
                        var p = sd.Prompt("Name:", function (v) {
                            ajax.Post("../../../../Api/Ajax/Audio/CreatePlayList.php", {"Name": v}, function (data) {
                                GetPlayList();
                                p.Close();
                            });
                        });
                    });

                    ss.S("#OptSelectLib").Change(function () {
                        ajax.Get("../../../../Api/Ajax/Audio/GetAudioList.php", {"Name": this.value}, function (data) {
                            data = JSON.parse(data);
                            FilePlayList.Empty();
                            for (var i in data) {
                                FilePlayList.AddList(data [i]["path"], data [i]["name"]);
                            }

                        });
                    });


                    ss.S("#BNDeletePlayList").Click(function () {
                        sd.Confirm("Delect It????", function () {
                            ajax.Post("../../../../Api/Ajax/Audio/DeletePlayList.php", {"Name": ss.S("#OptSelectLib").Val()}, function (data) {
                                location.reload();
                            });
                        }).ZIndex(999);
                    });

                    ss.S("#BNEditPlayList").Click(function () {
                        sd.Prompt("Rename", function (v) {
                            ajax.Post("../../../../Api/Ajax/Audio/RenamePlayList.php", {"Name": ss.S("#OptSelectLib").Val(), "NewName": v}, function (data) {
                                location.reload();
                            });
                        });
                    });

                    ss.S("#BNRemoveFile").Click(function () {
                        ajax.Post("../../../../Api/Ajax/Audio/DeleteAudioFromPlayList.php", {"Name": ss.S("#OptSelectLib").Val(),"Path": FilePlayList.GetSelectLists()}, function (data) {

                            ss.S("#OptSelectLib").Change();
                        });
                    });
                    GetPlayList();


                });
            </script>
        </head>
        <body  class="HolyGrail">

            <header class="Header">
                <div style="width: 50%;"></div>
                <div style="width: 50%;text-align: right;">
                    <?php
                    printf('<img src="../../../../Api/Action/Profile/Basic/GetUserIcon.php?id=%s"/>', $_SESSION["User"]["id"]);
                    printf('<span style="font-weight: bold;cursor: default;">%s</span>', $_SESSION["User"]["alias"]);
                    ?>       
                    <a style="font-weight: bold;" href="../../../../Auth/Action/Logout.php">LogOut</a>
                </div>
            </header>
            <div class="HolyGrail-body">
                <nav>
                    <?php
                    foreach ($uinav->FindAllMenuFile("../../App") as $key => $valueA) {
                        echo '<div class="BorderBlock">';
                        printf(' <div class="TitleCenter">%s</div>', $key);
                        foreach ($valueA as $valueB) {
                             
 printf('  <a class="MenuLink" href="%s">%s</a>', "../../App/".$valueB["path"], $valueB["name"]);
                        }
                        echo '</div>';
                    }
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
                    <div id="FilesList"></div>


                </main>
                <aside>
                    <?php
                    if ($_SESSION["User"]["writable"] == 1) {
                        ?>

                        <div class="BorderBlock" style="margin-top: 1px;">
                            <div class="TitleCenter">PlayList</div>
                            <select id="OptSelectLib"  style="width: 100%;box-sizing: border-box;">

                            </select>

                            <div style="display: flex;flex-direction: row;">
                                <button id="BNNewPlayList" style="width: 33%;"  href="#">New</button>
                                <button id="BNEditPlayList" style="width: 33%;"  href="#">Edit</button>
                                <button id="BNDeletePlayList" style="width: 33%;" href="#">Delete</button>
                            </div>
                            <div class="TitleCenter">Files List</div>
                            <div id="FilePlayList" style="margin-top: 1px;border-style: solid;border-width: thin;">

                            </div>
                            <div style="display: flex;flex-direction: row;">
                                <button id="BNAddFile" style="width: 50%;"  href="#">Add</button>
                                <button id="BNRemoveFile" style="width: 50%;" href="#">Remove</button>
                            </div>
                        </div>
                        <?php
                    }
                    ?>
                    <div class="BorderBlock" style="margin-top: 1px;">
                        <div class="TitleCenter">Event</div>
                        <?php
                        foreach ($event->GetComingEvent(Event_Database::Access_Member) as $value) {
                            echo '<div>';
                            printf('<a class="MenuLink" href="../Event/View.php?id=%s"><span style="font-weight: bold;">%s</span>', $value["id"], $value["name"]);
                            printf('<div style="color: black;" >%s</div></a>', $value["description"]);
                            echo '</div><hr>';
                        }
                        ?>
                    </div>
                    <?php
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
    header("location: ../../../../Auth/Login.php");
    session_destroy();
}
