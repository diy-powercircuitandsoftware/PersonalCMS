
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
$user=new User_Member(new User_Database($config));
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
            <link rel="stylesheet" type="text/css" href="../css/Page.css">
            <script src="../../../../js/dom/SSQueryFW.js"></script>
            <script src="../../../../js/dom/SuperDialog.js"></script>
            <script src="../../../../js/dom/SearchBox.js"></script>
            <script src="../../../../js/file/FilesList.js"></script>
            <style>
                .BNUserList{
                    cursor: pointer;
                    display: block;
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
                    var Dialog = new SuperDialog();
                    var SB = document.getElementById("SearchBox").appendChild(new SearchBox());
                    var FL = document.getElementById("FilesList").appendChild(new FilesList());
                    FL.RequestAuth = [];
                    FL.DownloadURL = "../../../Api/ShareAction/Files/DownloadFile.php?id=";
                    SB.Input = function (v) {
                        SS.Get("../../../Api/ShareAjax/User/SearchAlias.php", {"Alias": v}, function (data) {
                            data = JSON.parse(data);
                            for (var i = 0; i < data.length; i++) {
                                SB.AddList(data[i]["userid"], data[i]["alias"]);
                            }
                        });
                    };
                    SB.CallbackValue = function (v) {
                        SS.S("#DboxErrorBody").Hide();
                        FL.UserID = v;
                        FL.ChDir(("fullpath=..."));
                    };
                    FL.BeforeDownload = function (v) {
                        if (this.RequestAuth.indexOf(v) >= 0) {
                            Dialog.Login(function (lv) {
                                SS.Post("../../../Api/ShareAjax/Files/AuthFile.php", {"id": SS.URLParam(v)["id"], "Path": decodeURIComponent(SS.URLParam(v)["fullpath"]), "Auth": lv["UserName"], "Password": lv["Password"]}, function (rs) {
                                    if (rs == "1") {
                                        var index = FL.RequestAuth.indexOf(v);
                                        FL.RequestAuth.splice(index, 1);
                                    }
                                });

                                return true;
                            });
                            return false;
                        }
                    };
                    FL.ChDir = function (v) {
                        if (this.RequestAuth.indexOf(v) >= 0) {
                            var userid = this.UserID;
                            Dialog.Login(function (lv) {
                                SS.Post("../../../Api/ShareAjax/Files/GetFilesFromUserID.php", {"UserID": userid, "DIR": v, "Auth": lv["UserName"], "Password": lv["Password"]}, function (data) {
                                    var filedata = JSON.parse(data);
                                    if (filedata) {

                                        FL.ClearFileList();
                                        FL.RequestAuth = [];

                                        for (var i in filedata) {
                                            var ext = (filedata[i]["ext"]).toLowerCase();
                                            if (["jpg", "gif", "png", "jpeg"].indexOf(ext) >= 0) {
                                                FL.AddFile(filedata[i]["name"], filedata[i]["fullpath"], "../../../Api/ShareAction/Files/ImagePreview.php?" + (filedata[i]["fullpath"]), filedata[i]["size"], filedata[i]["modified"], filedata[i]["type"]);
                                            } else {
                                                FL.AddFile(filedata[i]["name"], filedata[i]["fullpath"], "", filedata[i]["size"], filedata[i]["modified"], filedata[i]["type"]);
                                            }
                                            if (parseInt(filedata[i]["hadpassword"]) == 1) {
                                                FL.RequestAuth.push(filedata[i]["fullpath"]);
                                            }
                                        }


                                        SS.S("#CHDIRList").Html(decodeURIComponent(decodeURIComponent(SS.URLParam(v)["fullpath"])));
                                    }
                                });
                                return true;
                            }).ZIndex(999);

                        } else {
                            SS.Post("../../../Api/ShareAjax/Files/GetFilesFromUserID.php", {"UserID": this.UserID, "DIR": v}, function (data) {
                                FL.ClearFileList();
                                FL.RequestAuth = [];
                                var filedata = JSON.parse(data);
                                for (var i in filedata) {
                                    var ext = (filedata[i]["ext"]).toLowerCase();
                                    if (["jpg", "gif", "png", "jpeg"].indexOf(ext) >= 0) {
                                        FL.AddFile(filedata[i]["name"], filedata[i]["fullpath"], "../../../Api/ShareAction/Files/ImagePreview.php?" + (filedata[i]["fullpath"]), filedata[i]["size"], filedata[i]["modified"], filedata[i]["type"]);
                                    } else {
                                        FL.AddFile(filedata[i]["name"], filedata[i]["fullpath"], "", filedata[i]["size"], filedata[i]["modified"], filedata[i]["type"]);
                                    }
                                    if (parseInt(filedata[i]["hadpassword"]) == 1) {
                                        FL.RequestAuth.push(filedata[i]["fullpath"]);
                                    }
                                }

                                SS.S("#CHDIRList").Html(decodeURIComponent(decodeURIComponent(SS.URLParam(v)["fullpath"])));
                            });
                        }

                    };



                    FL.OpenFile = function (v) {
                        var vl = SS.URLParam(v.toString().toLowerCase())["fullpath"].split('.').pop();
                        if (this.RequestAuth.indexOf(v) >= 0) {
                            Dialog.Login(function (lv) {
                                SS.Post("../../../Api/ShareAjax/Files/AuthFile.php", {"id": SS.URLParam(v)["id"], "Path": decodeURIComponent(SS.URLParam(v)["fullpath"]), "Auth": lv["UserName"], "Password": lv["Password"]}, function (rs) {

                                    if (rs == "1") {
                                        var index = FL.RequestAuth.indexOf(v);
                                        FL.RequestAuth.splice(index, 1);
                                        if (["mp4", "webm", "ogg"].indexOf(vl) >= 0) {
                                            var player = Dialog.VideoPlayer("../../../Api/ShareAction/Files/DownloadFile.php?id=" + btoa(v))
                                            player.ZIndex(999);
                                            player.Width("800px");
                                            player.Height("600px");
                                        } else if (["mp3", "wma"].indexOf(vl) >= 0) {
                                            var player = Dialog.AudioPlayer("../../../Api/ShareAction/Files/DownloadFile.php?id=" + btoa(v));
                                            player.ZIndex(999);
                                            player.Width("800px");
                                            player.Height("50px");
                                        } else if (["jpg", "gif", "png", "jpeg"].indexOf(vl) >= 0) {
                                            Dialog.ImageViewer("../../../Api/ShareAction/Files/DownloadFile.php?id=" + btoa(v)).ZIndex(999);
                                        } else if (vl.toLowerCase() == "pdf") {

                                            window.open('../../../Api/ShareAction/Files/DownloadFile.php?id=' + btoa(v) + "&option=opendisable206", '_blank', 'fullscreen=yes');
                                        }

                                    }

                                });

                                return true;
                            });

                        } else {
                            if (["mp4", "webm", "ogg"].indexOf(vl) >= 0) {
                                var player = Dialog.VideoPlayer("../../../Api/ShareAction/Files/DownloadFile.php?id=" + btoa(v))
                                player.ZIndex(999);
                                player.Width("800px");
                                player.Height("600px");
                            } else if (["mp3", "wma"].indexOf(vl) >= 0) {
                                var player = Dialog.AudioPlayer("../../../Api/ShareAction/Files/DownloadFile.php?id=" + btoa(v));
                                player.ZIndex(999);
                                player.Width("800px");
                                player.Height("50px");
                            } else if (["jpg", "gif", "png", "jpeg"].indexOf(vl) >= 0) {
                                Dialog.ImageViewer("../../../Api/ShareAction/Files/DownloadFile.php?id=" + btoa(v)).ZIndex(999);
                            } else if (vl.toLowerCase() == "pdf") {

                                window.open('../../../Api/ShareAction/Files//DownloadFile.php?id=' + btoa(v) + "&option=opendisable206", '_blank', 'fullscreen=yes');
                            }
                        }

                    };

                    SS.S("#BNClearPassword").Click(function () {
                        SS.Post("../../../Api/ShareAjax/Files/ClearPassword.php", '', function (rs) {
                            location.reload();
                        });
                    });
                    SS.S(".BNUserList").Click(function () {
                        SB.ChangeValue(this.innerHTML);
                        FL.UserID = this.getAttribute("data-id");
                        FL.ChDir(("fullpath=..."));
                        SS.S("#DboxErrorBody").Hide();
                    });



                });
            </script>
        </head>
        <body>
            <header> 
                <h1 style="width: 100%;text-align: center;"><?php echo $config->GetName(); ?> Website</h1>
            </header>
            <div class="LMR157015">
                <div>
                    <nav>
                        <?php
                        foreach ($uinav->FindAllMenuFile("../../App") as $key => $valueA) {
                            echo '<div class="BorderBlock">';
                            printf(' <div class="TitleCenter">%s</div>', $key);
                            foreach ($valueA as $valueB) {
                                printf('  <a  class="MenuLink" href="%s">%s</a>', $valueB["path"], $valueB["name"]);
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
                </div>
                <div>
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
                </div>
                <div>
                     <div   class="BorderBlock" style="margin-top: 1px;">
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
                        printf('<a href="Event/index.php?id=%s"><span style="font-weight: bold;">%s</span>', $value["id"], $value["name"]);
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
                </div>
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