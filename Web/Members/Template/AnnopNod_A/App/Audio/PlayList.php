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
                #EditArea{
                    display: flex;
                    flex-direction: row;
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
                    var ajax =new Ajax();
                    var FL = new FilesList(document.getElementById("FilesList"));
                  //  var FilePlayList = document.getElementById("FilePlayList").appendChild(new SelectList());
                     FL.SetDownload("../../../../Api/Action/Files/DownloadFiles.php?path=");
                   FL.Multiple(true);
                    FL.OpenDir(function (v) {
                        ajax.Post("../../../../Api/Ajax/Files/GetFilesListByExtension.php", {"Path": v,"Ext":"wma,mp3,ogg"}, function (data) {
                           
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
                     return ;
                    ss.S("#BNAddFile").Click(function () {
                        ss.Post("../../../Api/Ajax/AudioPlayList/AddAudioFileToPlayList.php", {"FilesList": fl.GetSelectFiles(), "ID": ss.S("#OPTSELALB").Val()}, function (data) {
                            fl.ClearSelectList();
                            ss.S("#OPTSELALB").Change();
                        });
                    });

                    ss.S("#BNAddPlayList").Click(function () {
                        ss.S(".AjaxSend").Val("");
                        sd.Import("#DialogEdit", function () {

                            ss.Post("../../../Api/Ajax/AudioPlayList/CreatePlayList.php", ss.S(".AjaxSend").SerializeToJson(), function (data) {
                                location.reload();
                            });
                        }).ZIndex(999).Title("AddPlayList");
                    });
                    ss.S("#BNDeletePlayList").Click(function () {
                        sd.Confirm("Delect It????", function () {
                            ss.Post("../../../Api/Ajax/AudioPlayList/DeletePlayList.php", {"ID": ss.S("#OPTSELALB").Val()}, function (data) {
                                location.reload();
                            });
                        }).ZIndex(999);
                    });
                    ss.S("#BNEditPlayList").Click(function () {
                        ss.Post("../../../Api/Ajax/AudioPlayList/GetPlaylistForEdit.php", {"ID": ss.S("#OPTSELALB").Val()}, function (data) {
                            ss.S(".AjaxSend").ValByName(JSON.parse(data));
                            sd.Import("#DialogEdit", function () {
                                var json = ss.S(".AjaxSend").SerializeToJson();
                                json["ID"] = ss.S("#OPTSELALB").Val();
                                ss.Post("../../../Api/Ajax/AudioPlayList/SetPlaylistForEdit.php", json, function (data) {
                                    location.reload();
                                });
                            }).ZIndex(999).Title("EditPlayList");
                        });

                    });

                    ss.S("#BNHome").Click(function () {
                        fl.ChDir("/");
                    });

                    ss.S("#BNRemoveFile").Click(function () {
                        ss.Post("../../../Api/Ajax/AudioPlayList/RemoveAudioFromPlayList.php", {"ID": FilePlayList.GetSelectList().join(",")}, function (data) {
                            ss.S("#OPTSELALB").Change();
                        });
                    });

                    ss.S("#OPTSELALB").Change(function () {
                        ss.Get("../../../Api/Ajax/AudioPlayList/GetFilesNameFromPlayList.php", {"PlayListID": this.value}, function (data) {
                            data = JSON.parse(data);
                            FilePlayList.Empty();
                            for (var i in data) {
                                FilePlayList.AddList(data [i]["id"], data [i]["name"]);
                            }

                        });
                    }).Change();
                });
            </script>
        </head>
        <body  class="HolyGrail">

            <header class="Header">
                <div style="width: 50%;"></div>
                <div style="width: 50%;text-align: right;">
                    <?php
                    printf('<img src="../../../../Api/Action/Profile/GetUserIcon.php?id=%s"/>', $_SESSION["User"]["id"]);
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
                            printf('  <a class="MenuLink" href="%s">%s</a>', $valueB["path"], $valueB["name"]);
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
                    <div>
                        <label>PlayList:</label>
                        <select name=""></select>

                        <?php
                        if ($_SESSION["User"]["writable"] == 1) {
                            ?>
                            <button>New</button> 
                            <button>Delete</button>
                            <div style="border-style: solid;border-width: thin;">
                                <button>Add</button>
                                <button>Delete</button>
                           
                            <div id="EditArea">


                                <div id=FilesList style="width: 50%;overflow: auto;"></div>
                                <div id=Playlist></div>

                            </div>
                                 </div>
                            <?php
                        }
                        ?>
                    </div>

                </main>
                <aside>
                    <?php
                    if ($_SESSION["User"]["writable"] == 1) {
                        ?>
                        <div class="BorderBlock" style="margin-top: 1px;">
                            <label class="Title">Files</label>
                            <a id="BNHome" style="width: 100%;box-sizing: border-box;" href="#">Go To Home</a>
                        </div>
                        <div class="BorderBlock" style="margin-top: 1px;">
                            <label class="Title">PlayList</label>
                            <select id="OPTSELALB"  style="width: 100%;box-sizing: border-box;">
                                <?php
                                foreach ($PlayList->GetPlayList($_SESSION["UserID"]) as $value) {
                                    printf('<option value="%s">%s</option>', $value["id"], $value["name"]);
                                }
                                ?>
                            </select>

                            <div style="display: flex;flex-direction: row;">
                                <button id="BNAddPlayList" style="width: 33%;"  href="#">Add</button>
                                <button id="BNEditPlayList" style="width: 33%;"  href="#">Edit</button>
                                <button id="BNDeletePlayList" style="width: 33%;" href="#">Remove</button>
                            </div>
                            <span>FilesList:</span>
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
                            printf('<a href="../Event/View.php?id=%s"><span style="font-weight: bold;">%s</span>', $value["id"], $value["name"]);
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

            <div class="Container" >


                <div class="Aside" style="">

                    <div class="BorderBlock" style="margin-top: 1px;">
                        <label class="Title">My Event</label>
                        <?php
                        foreach ($Event->GetCurrentMyEvent($_SESSION["UserID"]) as $value) {
                            echo '<div  >';
                            printf('<a href="../Event/View.php?id=%s"><span style="font-weight: bold;">%s</span>', $value["id"], $value["name"]);
                            printf('<div style="color: black;" >%s</div></a>', $value["description"]);
                            echo '</div><hr>';
                        }
                        ?>
                    </div>
                    <div class="BorderBlock" style="margin-top: 1px;">
                        <label class="Title">Other Event</label>
                        <?php
                        $Dat = array_merge($Event->GetCurrentEventNotUserID(Config_DB_Config::Access_Mode_Members, $_SESSION["UserID"]), $Event->GetCurrentEventNotUserID(Config_DB_Config::Access_Mode_Public, $_SESSION["UserID"]));
                        foreach ($Dat as $value) {
                            echo '<div  >';
                            printf('<a href="../Share/EventViewer.php?id=%s"><span style="font-weight: bold;">%s</span>', $value["id"], $value["name"]);
                            printf('<div style="color: black;" >%s</div></a>', $value["description"]);
                            echo '</div><hr>';
                        }
                        ?>
                    </div>
                    <div class="BorderBlock" style="margin-top: 1px;">
                        <span class="Title" style="display: block;">Share</span>
                        <ul>
                            <li><a href="../Share/BlogViewer.php">Blog</a></li>
                            <li><a href="../Share/EventViewer.php">Event</a></li>
                        </ul>
                    </div>
                    <?php
                    $Dat = array_merge($Module->LoadModule(Com_Module_LoadModule::Layout_Aside, Config_DB_Config::Access_Mode_Members), $Module->LoadModule(Com_Module_LoadModule::Layout_Aside, Config_DB_Config::Access_Mode_Public));
                    foreach ($Dat as $value) {
                        try {
                            echo ' <div class="BorderBlock" style="margin-top: 3px;" >';
                            include_once '../../../../../Class/DB/Module/' . $value["filename"];
                            $mod = new $value["classname"]($Module);
                            printf('<label class="Title">%s</label>', $mod->GetTitle());
                            $mod->SetModuleID($value["id"]);
                            $mod->SetModulePage("../Module/Page.php");
                            $mod->SetUserID($_SESSION["UserID"]);
                            echo $mod->Execute();
                            echo '</div>';
                        } catch (Exception $ex) {
                            
                        }
                    }
                    ?>

                </div>
            </div>
            <table id="DialogEdit" style="width: 100%;box-sizing: border-box;display: none;">
                <tr>
                    <td>Name:</td>
                    <td><input  class="AjaxSend" type="text" name="name" value="" /></td>
                </tr>
                <tr>
                    <td>UserName:</td>
                    <td><input class="AjaxSend"  type="text" name="authname" value="" /></td>
                </tr>
                <tr>
                    <td>Password:</td>
                    <td><input class="AjaxSend"  type="password" name="password" value="" /></td>
                </tr>
                <tr>
                    <td>AccessMode:</td>
                    <td>
                        <select class="AjaxSend" name="accessmode" >
                            <?php
                            foreach ($DBConfig->GetAccessMode() as $value) {
                                printf('<option value="%s">%s</option>', $value["value"], $value["name"]);
                            }
                            ?>
                        </select>
                    </td>
                </tr>
            </table>
        </body>
    </html>
    <?php
} else {
    header("location: ../../../Session/AuthUserID.php");
    session_destroy();
}
