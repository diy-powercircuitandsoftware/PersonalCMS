
<?php
include_once '../../../../../Class/DB/Config/DB/Config.php';
include_once '../../../../../Class/DB/Config/DB/Software.php';
include_once '../../../../../Class/DB/Com/Events/Viewer.php';
include_once '../../../../../Class/DB/Com/Module/LoadModule.php';
include_once '../../../../../Class/DB/Com/User/Profile.php';
$DBConfig = new Config_DB_Config();
$SC = new Config_DB_Software($DBConfig);
$Event = new Com_Events_Viewer($DBConfig);
$User = new Com_User_Profile($DBConfig);
$Module = new Com_Module_LoadModule($DBConfig);
$DBConfig->Open();
if ($SC->Online()) {
    ?>
    <!DOCTYPE html>
    <html>
        <head>
            <meta charset="UTF-8">
            <title><?php echo $SC->GetName() . ' Cloud Service'; ?> </title>
            <link rel="stylesheet" type="text/css" href="../css/Page.css">
            <script src="../../../../js/dom/SSQueryFW.js"></script>
            <script src="../../../../js/dom/SuperDialog.js"></script>
            <script src="../../../../js/dom/SearchBox.js"></script>
            <script src="../../../../js/file/FilesList.js"></script>
            <style>
                .BNUserList{
                    cursor: pointer;
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
        <body  >
            <div  id="Header" style="position: static;">
                <h1  style="width: 100%;text-align: center;"><?php echo $SC->GetName(); ?> Cloud Service</h1>
            </div>
            <div class="Container">
                <div class="Nav">
                    <div class="BorderBlock" style="margin-top: 1px;">
                        <label class="Title">About</label>
                        <a href="../About/index.php">About</a>
                    </div>
                    <div class="BorderBlock" style="margin-top: 1px;">
                        <label class="Title">App</label>
                        <a href="../App/index.php">Player</a>
                    </div>
                    <div class="BorderBlock" style="margin-top: 1px;">
                        <label class="Title">Blog</label>
                        <a href="../Blog/index.php">Viewer</a>
                    </div>
                    <div class="BorderBlock" style="margin-top: 1px;">
                        <label class="Title">Event</label>
                        <a href="../Event/index.php">Viewer</a>
                    </div>
                    <div class="BorderBlock" style="margin-top: 1px;">
                        <label class="Title">Files</label>
                        <span style="font-weight: bold;">Viewer </span>
                    </div>

                    <div class="BorderBlock" style="margin-top: 1px;">
                        <label class="Title">Photo</label>
                        <a href="../Photo/ImageSlider.php">ImageSlider </a>
                    </div>
                    <div class="BorderBlock" style="margin-top: 1px;">
                        <label class="Title">Template</label>
                        <?php
                        $filelist = array_diff(scandir("../../"), array('.', '..'));
                        foreach ($filelist as $value) {
                            if (is_dir("../../" . $value)) {
                                printf('<a style="display:block;" href="../../%s">%s</a>', $value, $value);
                            }
                        }
                        ?>

                    </div>
                    <div class="BorderBlock" style="margin-top: 1px;">
                        <label class="Title">User</label>
                         <a href="../../../../Members/Session/AuthUserID.php?tp=AnnopNod_A">Login</a>
                         
                    </div>



                    <?php
                    foreach ($Module->LoadModule(Com_Module_LoadModule::Layout_Nav, Config_DB_Config::Access_Mode_Public) as $value) {
                        try {
                            echo ' <div class="BorderBlock" style="margin-top: 3px;" >';
                            include_once '../../../../../Class/DB/Module/' . $value["filename"];
                            $mod = new $value["classname"]($Module);
                            printf('<label class="Title">%s</label>', $mod->GetTitle());
                            $mod->SetModulePage("../Module/Page.php");
                            $mod->SetModuleID($value["id"]);
                            echo $mod->Execute();
                            echo '</div>';
                        } catch (Exception $ex) {
                            
                        }
                    }
                    ?>
                </div>
                <div class="Section">
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
                            <h1>Restricted Content</h1> This file is no longer available. For additional information <a href="../About/index.php">Contact <?php echo $SC->GetName(); ?> Support</a>.
                        </div>
                    </div>
                </div>
                <div class="Aside">

                    <div   class="BorderBlock" style="margin-top: 1px;">
                        <label class="Title">User</label>
                        <ul>
                            <?php
                            foreach ($User->GetUserList() as $value) {
                                printf('<li><a class="BNUserList" data-id="%s">%s</a></li>', $value["userid"], $value["alias"]);
                            }
                            ?>
                        </ul>
                        <div id="SearchBox" class="BorderBlock" style="margin-top: 1px;">

                        </div>
                    </div>


                    <div class="BorderBlock" style="margin-top: 1px;">
                        <label class="Title">Password</label>
                        <a id="BNClearPassword" href="#">ClearPassword</a>
                    </div>
                    <div class="BorderBlock">
                        <span  class="Title">Event</span>
                        <?php
                        foreach ($Event->GetCurrentEvent(Config_DB_Config::Access_Mode_Public) as $value) {
                            echo '<div  >';
                            printf('<a href="../Event/index.php?id=%s"><span style="font-weight: bold;">%s</span>', $value["id"], $value["name"]);
                            printf('<div style="color: black;">%s</div></a>', $value["description"]);
                            echo '</div><hr>';
                        }
                        ?>
                    </div>
                    <?php
                    foreach ($Module->LoadModule(Com_Module_LoadModule::Layout_Aside, Config_DB_Config::Access_Mode_Public) as $value) {
                        try {
                            echo ' <div class="BorderBlock" style="margin-top: 3px;" >';
                            include_once '../../../../../Class/DB/Module/' . $value["filename"];
                            $mod = new $value["classname"]($Module);
                            printf('<label class="Title">%s</label>', $mod->GetTitle());
                            $mod->SetModulePage("../Module/Page.php");
                            $mod->SetModuleID($value["id"]);
                            echo $mod->Execute();
                            echo '</div>';
                        } catch (Exception $ex) {
                            
                        }
                    }
                    ?>
                </div>
            </div>
            <div>
                <span style="font-weight: 700;display: block;">
                    <?php
                    echo "&COPY;" . date("Y") . " " . $SC->GetName();
                    ?>
                </span>
            </div>
        </body>
    </html>
    <?php
} else {
    header("location: ../Error/Offline.php");
}