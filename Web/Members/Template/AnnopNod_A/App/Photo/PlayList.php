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
            <script src="../../../../../js/dom/SuperDialog/SuperDialog.js"></script>
            <script src="../../../../../js/dom/SuperDialog/Template/Basic/MessageBox.js"></script>
            <script src="../../../../../js/dom/SuperDialog/Template/Basic/Input.js"></script>
            <script src="../../../../../js/dom/SuperDialog/Template/Basic/Multimedia.js"></script>
            <script src="../../../../../js/dom/FilesList.js"></script>
            <script src="../../../../../js/io/Ajax.js"></script>
            <script>
                var ss = new SSQueryFW();
                ss.DocumentReady(function () {
                    var dialoginput = new SuperDialog_Template_Input();
                    var dialogmsgbox = new SuperDialog_Template_MessageBox();
                    var ajax = new Ajax();
                    var FL = new FilesList(document.getElementById("FilesList"));
                    var FilePlayList = new SelectList(document.getElementById("FilePlayList"));
                    FL.SetDownload("../../../../Api/Action/Files/Download/DownloadFiles.php?path=");
                    FL.SetPreviewImage("../../../../Api/Action/Files/Download/ImagePreview.php?id=");
                    FL.Multiple(true);
                    FL.OpenDir(function (v) {
                        ajax.Post("../../../../Api/Ajax/Files/List/GetFilesListByExtension.php", {"Path": v, "Ext": "wma,mp3,ogg,jpg,gif,png,jpeg"}, function (data) {
                            FL.Clear();
                            data = JSON.parse(data);
                            for (var i in data) {
                                if (data[i]["type"] == "DIR") {
                                    FL.AddDir(data[i]["name"], data[i]["fullpath"], data[i]["modified"]);
                                } else if (data[i]["type"] == "FILE") {
                                    FL.AddFile(data[i]["name"], data[i]["fullpath"], data[i]["size"], data[i]["modified"]);
                                }
                            }
                            FL.RemoveEditable();
                            ss.S("#CHDIRList").Html((v));
                        });
                    });
                    FL.OpenFile(function (v) {
                        if (["ogg", "mp3", "wma", "jpg", "gif", "png", "jpeg"].indexOf(v.split('.').pop().toLowerCase()) >= 0) {
                            new SuperDialog_Template_Multimedia().MediaPlayer("../../../../Api/Action/Files/Download/DownloadFiles.php?path=" + (v));
                        }
                    })
                    FL.OpenDir("/");

                    function GetPlayList() {
                        ajax.Post("../../../../Api/Ajax/Photo/SlideShow/GetPlayList.php", {}, function (data) {
                            data = JSON.parse(data);
                            ss.S("#OptSelectLib").Empty();
                            for (var i in data) {
                                ss.S("#OptSelectLib").Append(data[i], data[i]);
                            }
                            ss.S("#OptSelectLib").Change();
                        });
                    }
                    ss.S("#BNAddFile").Click(function () {
                        ajax.Post("../../../../Api/Ajax/Photo/SlideShow/AddFilesToPlayList.php", {"Name": ss.S("#OptSelectLib").Val(), "Path": FL.GetSelectFiles()}, function (data) {
                            ss.S("#OptSelectLib").Change();
                        });
                    });

                    ss.S("#BNNewPlayList").Click(function () {
                        var p = dialoginput.Prompt("Name:", function (v) {
                            ajax.Post("../../../../Api/Ajax/Photo/SlideShow/CreatePlayList.php", {"Name": v}, function (data) {
                                GetPlayList();
                                p.close();
                            });
                        });
                    });

                    ss.S("#OptSelectLib").Change(function () {
                        ajax.Get("../../../../Api/Ajax/Photo/SlideShow/GetFilesList.php", {"Name": this.value}, function (data) {
                            ss.S("#OptShowExt").Prop("data", JSON.parse(data));
                            ss.S("#OptShowExt").Change();
                        });
                    });
                    ss.S("#OptShowExt").Change(function () {
                        FilePlayList.Empty();

                        for (var i in this.data) {

                            if (this.value == "1" && (["jpg", "gif", "png", "jpeg"].indexOf(this.data [i]["name"].split('.').pop().toLowerCase()) >= 0)) {
                                FilePlayList.AddList(this.data [i]["path"], this.data [i]["name"]);
                            } else if (this.value == "2" && (["ogg", "mp3", "wma"].indexOf(this.data [i]["name"].split('.').pop().toLowerCase())) >= 0) {
                                FilePlayList.AddList(this.data [i]["path"], this.data [i]["name"]);
                            } else if (this.value == "0") {
                                FilePlayList.AddList(this.data [i]["path"], this.data [i]["name"]);
                            }
                        }

                    });


                    ss.S("#BNDeletePlayList").Click(function () {
                        dialoginput.Confirm("Delect It????", function () {
                            ajax.Post("../../../../Api/Ajax/Photo/SlideShow/DeletePlayList.php", {"Name": ss.S("#OptSelectLib").Val()}, function (data) {
                                location.reload();
                            });
                        }).ZIndex(999);
                    });

                    ss.S("#BNEditPlayList").Click(function () {
                        dialoginput.Prompt("Rename", function (v) {
                            ajax.Post("../../../../Api/Ajax/Photo/SlideShow/RenamePlayList.php", {"Name": ss.S("#OptSelectLib").Val(), "NewName": v}, function (data) {
                                location.reload();
                            });
                        });
                    });

                    ss.S("#BNRemoveFile").Click(function () {
                        ajax.Post("../../../../Api/Ajax/Photo/SlideShow/DeleteFilesFromPlayList.php", {"Name": ss.S("#OptSelectLib").Val(), "Path": FilePlayList.GetSelectLists()}, function (data) {

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

                            printf('  <a class="MenuLink" href="%s">%s</a>', "../../App/" . $valueB["path"], $valueB["name"]);
                        }
                        echo '</div>';
                    }
                    foreach ($modlist as $value) {
                        if ($value->SupportLayout(Module_SDK_Basic::Layout_Nav)) {
                            echo ' <div class="BorderBlock" style="margin-top: 1px;" >';
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
                            <div class="BorderBlock" >
                                <label class="TitleCenter" style="display: block;">Share</label>
                                <a class="MenuLink"  id="BNAddShare" href="#">Add</a>
                                <a class="MenuLink" id="BNShareManager" href="#">Manager</a>
                            </div>
                            <div class="TitleCenter">Files List</div>
                            <select id="OptShowExt" style="width: 100%;box-sizing: border-box;">
                                <option value="0">All</option>
                                <option value="1">Photo</option>
                                <option value="2">Audio</option>
                            </select>
                            <div id="FilePlayList" class="BorderBlock">

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
                            echo ' <div class="BorderBlock" style="margin-top: 1px;" >';
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
            <div id="ShareFileDialog" style="display: none;">
                <table border="1" id="TBShareFile" style="width: 100%;">
                    <tr>
                        <th>Name</th>
                        <th>Access</th>
                    </tr>
                </table>
            </div>
            <div style="display: none;">
                <select id="CloneableOption">
                    <?php
                    printf('<option value="%s">Public</option>', FilesACLS_Database::Access_Public);
                    printf('<option value="%s">Member</option>', FilesACLS_Database::Access_Member);
                    ?>
                </select>
            </div>

        </body>
    </html>
    <?php
} else {
    header("location: ../../../../Auth/Login.php");
    session_destroy();
}
