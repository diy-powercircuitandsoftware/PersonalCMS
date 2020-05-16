<?php
session_start();
include_once '../../../../../Class/DB/Config/DB/Config.php';
include_once '../../../../../Class/DB/Config/DB/Software.php';
include_once '../../../../../Class/DB/Com/User/SessionManager.php';
include_once '../../../../../Class/DB/Com/User/Profile.php';
include_once '../../../../../Class/DB/Com/Events/Viewer.php';
include_once '../../../../../Class/DB/Com/Photo/PlayList_Manager.php';
include_once '../../../../../Class/DB/Com/Module/LoadModule.php';
include_once '../../../../../Class/DB/Com/User/LoadModule.php';
include_once '../../../../../Class/DB/Com/User/Permission.php';
$DBConfig = new Config_DB_Config();
$SC = new Config_DB_Software($DBConfig);
$Sess = new Com_User_SessionManager($DBConfig);
$User = new Com_User_Profile($DBConfig);
$Event = new Com_Events_Viewer($DBConfig);
$Module = new Com_Module_LoadModule($DBConfig);
$UModule = new Com_User_LoadModule($DBConfig);
$PlayList = new Com_Photo_PlayList_Manager($DBConfig);
$Permission = new Com_User_Permission($DBConfig);
$DBConfig->Open();
if ($SC->Online() && isset($_SESSION["UserID"]) && $Sess->Registered(session_id())) {
    ?>
    <!DOCTYPE html>
    <html>
        <head>
            <meta charset="UTF-8">
            <title><?php echo $SC->GetName(); ?></title>
            <link rel="stylesheet" href="../css/Page.css">

            <style>
                .AjaxSend{
                    width: 100%;
                    box-sizing: border-box;
                }
            </style>
            <?php
            foreach ($UModule->LoadModule($_SESSION["UserID"], Com_User_LoadModule::Layout_Head) as $value) {
                try {
                    include_once '../../../../../Class/DB/UserModule/' . $value["filename"];
                    $mod = new $value["classname"]($UModule);
                    $mod->LoadConfig($value["config"]);
                    echo $mod->Execute();
                } catch (Exception $ex) {
                    
                }
            }
            ?>
            <script src="../../../../js/dom/SelectList.js"></script>
            <script src="../../../../js/dom/SSQueryFW.js"></script>
            <script src="../../../../js/dom/SuperDialog.js"></script>
            <script src="../../../../js/file/FilesList.js"></script>
            <script>
                var ss = new SSQueryFW();
                ss.DocumentReady(function () {
                    var sd = new SuperDialog();
                    var fl = document.getElementById("UIFileRS").appendChild(new FilesList());
                    var FilePhotoPlayList = document.getElementById("FilePhotoPlayList").appendChild(new SelectList());
                    fl.DownloadURL = "../../../Api/Action/Files/DownloadFile.php?id=";
                    fl.OpenFile = function (v) {
                        var ext = v.split('.').pop();
                        if (["mp3", "wma"].indexOf(ext.toLowerCase()) >= 0) {
                            sd.AudioPlayer("../../../Api/Action/Files/DownloadFile.php?id=" + btoa(v)).ZIndex(999).Width("800px");
                        } else if (["jpg", "gif", "png", "jpeg"].indexOf(ext.toLowerCase()) >= 0) {
                            sd.ImageViewer("../../../Api/Action/Files/DownloadFile.php?id=" + btoa(v)).ZIndex(999);
                        }
                    };
                    fl.ChDir = function (v) {
                        ss.Post("../../../Api/Ajax/PhotoPlayList/GetAudioPhotoFiles.php", {"Location": v}, function (data) {
                            fl.ClearFileList();
                            data = JSON.parse(data);
                            for (var i in data) {
                                var ext = (data[i]["ext"]).toLowerCase();
                                if (["jpg", "gif", "png", "jpeg"].indexOf(ext) >= 0) {
                                    fl.AddFile(data[i]["name"], data[i]["fullpath"], "../../../Api/Action/Files/ImagePreview.php?id=" + data[i]["fullpath"], data[i]["size"], data[i]["modified"], data[i]["type"]);
                                } else {
                                    fl.AddFile(data[i]["name"], data[i]["fullpath"], "", data[i]["size"], data[i]["modified"], data[i]["type"]);
                                }
                            }
                        });
                    };
                    fl.PropertiesFile = function (v) {
                        ss.Post("../../../Api/Ajax/Files/GetPropertiesFile.php", {"Path": v}, function (data) {
                            data = JSON.parse(data);
                            var tl = sd.TableLayout().Title("Properties").ZIndex(999);
                            tl.AddTableDom("Name", data["name"]);
                            tl.AddTableDom("Size", data["size"]);
                            tl.AddTableDom("Modified", data["modified"]);
                        });
                    };
                    fl.ChDir("/");

                    ss.S("#BNAddFile").Click(function () {
                        ss.Post("../../../Api/Ajax/PhotoPlayList/AddPhotoFileToPlayList.php", {"FilesList": fl.GetSelectFiles(), "ID": ss.S("#OPTSELALB").Val()}, function (data) {
                            ss.S("#OPTSELALB").Change();
                        });
                    });

                    ss.S("#BNAddPlayList").Click(function () {
                        ss.S(".AjaxSend").Val("");
                        sd.Import("#DialogEdit", function (v) {
                            ss.Post("../../../Api/Ajax/PhotoPlayList/CreatePlayList.php", ss.S(".AjaxSend").SerializeToJson(), function (data) {
                                location.reload();
                            });
                        }).ZIndex(999).Title("AddPlayList");
                    });

                    ss.S("#BNEditPlayList").Click(function () {
                        ss.Post("../../../Api/Ajax/PhotoPlayList/GetPlayListForEdit.php", {"id": ss.S("#OPTSELALB").Val()}, function (data) {
                            ss.S(".AjaxSend").ValByName(JSON.parse(data));
                            sd.Import("#DialogEdit", function (v) {
                                var jsondata = ss.S(".AjaxSend").SerializeToJson();
                                jsondata["id"] = ss.S("#OPTSELALB").Val();
                                ss.Post("../../../Api/Ajax/PhotoPlayList/EditPlayList.php", jsondata, function (data) {
                                    location.reload();
                                });
                            }).ZIndex(999).Title("EditPlayList");
                        });

                    });

                    ss.S("#BNHome").Click(function () {
                        fl.ChDir("/");
                    });

                    ss.S("#BNRemoveFile").Click(function () {
                        ss.Post("../../../Api/Ajax/PhotoPlayList/RemoveFileFromPlayList.php", {"ID": FilePhotoPlayList.GetSelectList().join(",")}, function (data) {
                            ss.S("#OPTSELALB").Change();
                        });
                    });
                    ss.S("#BNRemovePlayList").Click(function () {
                        sd.Confirm("Delect It????", function () {
                            ss.Post("../../../Api/Ajax/PhotoPlayList/DeletePlayList.php", {"ID": ss.S("#OPTSELALB").Val()}, function (data) {
                                location.reload();
                            });
                        }).ZIndex(999);
                    });

                    ss.S("#OPTSELALB").Change(function () {
                        ss.Get("../../../Api/Ajax/PhotoPlayList/GetFilesNameFromPlayList.php", {"PlayListID": this.value}, function (data) {
                            data = JSON.parse(data);
                            FilePhotoPlayList.Empty();
                            for (var i in data) {
                                FilePhotoPlayList.AddList(data[i]["id"], data[i]["name"]);
                            }

                        });
                    }).Change();
                });
            </script>
        </head>
        <body>

            <div id="Header" >
                <div style="width: 50%;">
                    <a href="../index.php">
                        <img  src="../../../../../File/Resource/Logo.png"/>
                    </a>
                </div>
                <div  style="width: 50%;text-align: right;">
                    <a href="../index.php">MainPage</a>
                    <?php
                    $Dat = $User->GetBasicUserData($_SESSION["UserID"]);
                    printf('<img  src="../../../Api/Action/Profile/GetUserIcon.php?id=%s" />', $Dat["userid"]);
                    echo '<span>' . $Dat["alias"] . '</span>';
                    ?>
                    <a href="../Config/Config.php">Config</a>
                    <a href="../../../Session/Action/Logout.php">Logout</a>
                </div>
            </div>
            <div class="Container" >
                <div class="Nav">
                    <div class="BorderBlock" style="margin-top: 1px;">
                        <span class="Title" style="display: block;">Audio</span>
                        <ul>
                            <li><a href="../Audio/Player.php">Player</a></li>
                            <li><a href="../Audio/PlayList.php">PlayList</a></li>
                        </ul>
                    </div>
                    <div class="BorderBlock" style="margin-top: 1px;">
                        <span class="Title" style="display: block;">Blog</span>
                        <ul>
                            <li><a href="../Blog/Manage.php">Manage</a></li>
                            <li><a href="../Blog/View.php">View</a></li>
                        </ul>
                    </div>
                    <div class="BorderBlock" style="margin-top: 1px;">
                        <span class="Title" style="display: block;">Event</span>
                        <ul>
                            <li><a href="../Event/Manage.php">Manage</a></li>
                            <li><a href="../Event/View.php">View</a></li>
                        </ul>
                    </div>
                    <div class="BorderBlock" style="margin-top: 1px;">
                        <span class="Title" style="display: block;">Files</span>
                        <ul>
                            <li><a href="../Files/Manager.php">Manager</a></li>
                            <li><a href="../Files/Temp.php">Temp</a></li>
                            <li><a href="../Files/Trash.php">Trash</a></li>
                        </ul>
                    </div>
                    <div class="BorderBlock" style="margin-top: 1px;">
                        <span class="Title" style="display: block;">Office</span>
                        <ul>
                            <li><a href="../Office/FinFin/MainPage.php">FinFin</a></li>
                            <li><a href="../Office/FlowFlow/MainPage.php">FlowFlow</a></li>
                            <li><a href="../Office/Image/MainPage.php">Image</a></li>
                            <li><a href="../Office/PointPoint/MainPage.php">PointPoint</a></li>
                            <li><a href="../Office/Statistics/MainPage.php">Statistics</a></li>
                            <li><a href="../Office/WordWord/MainPage.php">WordWord</a></li>
                            <li><a href="../Office/WYSIWYG/NewDoc.php">WYSIWYG</a></li>
                            <li><a href="../Office/XCell/MainPage.php">XCell</a></li>
                            <li><a href="../Office/XCess/MainPage.php">XCess</a></li>
                        </ul>
                    </div>
                    <div class="BorderBlock" style="margin-top: 1px;">
                        <span class="Title" style="display: block;">Photo</span>
                        <ul>
                            <li><a href="ImageSlider.php">ImageSlider</a></li>
                            <li><span style="font-weight: bold;">PlayList</span></li>
                        </ul>
                    </div>
                    <div class="BorderBlock" style="margin-top: 1px;">
                        <span class="Title" style="display: block;">Share</span>
                        <ul>
                            <li><a href="../Share/BlogViewer.php">Blog</a></li>
                            <li><a href="../Share/EventViewer.php">Event</a></li>
                        </ul>
                    </div>
                    <?php
                    $Dat = array_merge($Module->LoadModule(Com_Module_LoadModule::Layout_Nav, Config_DB_Config::Access_Mode_Members), $Module->LoadModule(Com_Module_LoadModule::Layout_Nav, Config_DB_Config::Access_Mode_Public));
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
                <div class="Section">
                    <?php
                    if ($Permission->Writable($_SESSION["UserID"])) {
                        echo ' <div style="width: 90%;margin-left: auto;margin-right: auto;">
                            <div id="UIFileRS"></div>
                        </div>';
                    }
                    ?>

                </div>
                <div class="Aside" style="">
                    <?php
                    if ($Permission->Writable($_SESSION["UserID"])) {
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
                                <button id="BNRemovePlayList" style="width: 33%;" href="#">Remove</button>
                            </div>
                            <span>FilesList:</span>
                            <div id="FilePhotoPlayList" style="margin-top: 1px;border-style: solid;border-width: thin;">

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
            <table id="DialogEdit" style="display: none;width: 100%;box-sizing: border-box;">
                <tr>
                    <td>Name:</td>
                    <td><input class="AjaxSend" type="text" name="name" value="" /></td>
                </tr>
                <tr>
                    <td>HoldTime:</td>
                    <td><input class="AjaxSend" name="hold_t" type="number" min="1" value="1" /></td>
                </tr>
                <tr>
                    <td>ChangeTime:</td>
                    <td><input class="AjaxSend" name="change_t" type="number" min="1" value="1" /></td>
                </tr>
                <tr>
                    <td>AuthName:</td>
                    <td><input class="AjaxSend" name="authname" type="text" min="1" value="1" /></td>
                </tr>
                <tr>
                    <td>Password:</td>
                    <td><input class="AjaxSend" name="password" type="text" min="1" value="1" /></td>
                </tr>
                <tr>
                    <td>Access:</td>
                    <td>
                        <select class="AjaxSend" name="accessmode">
                            <option value="0">None</option>
                            <option value="1">Public</option>
                            <option value="2">Member</option>
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
