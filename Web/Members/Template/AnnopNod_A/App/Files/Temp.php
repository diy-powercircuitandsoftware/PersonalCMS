<?php
session_start();
include_once '../../../../../Class/DB/Config/DB/Config.php';
include_once '../../../../../Class/DB/Config/DB/Software.php';
include_once '../../../../../Class/DB/Com/User/SessionManager.php';
include_once '../../../../../Class/DB/Com/User/Profile.php';
include_once '../../../../../Class/DB/Com/Events/Viewer.php';
include_once '../../../../../Class/DB/Com/User/LoadModule.php';
include_once '../../../../../Class/DB/Com/Module/LoadModule.php';
include_once '../../../../../Class/DB/Com/User/Permission.php';
$DBConfig = new Config_DB_Config();
$SC = new Config_DB_Software($DBConfig);
$Sess = new Com_User_SessionManager($DBConfig);
$User = new Com_User_Profile($DBConfig);
$Event = new Com_Events_Viewer($DBConfig);
$Module = new Com_Module_LoadModule($DBConfig);
$UModule = new Com_User_LoadModule($DBConfig);
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
                <script src="../../../../js/dom/SSQueryFW.js"></script>
                <script src="../../../../js/dom/SuperDialog.js"></script>
                <script src="../../../../js/file/FilesList.js"></script>
                <script>
                    var ss = new SSQueryFW();
                    ss.DocumentReady(function () {
                        var dialog = new SuperDialog();

                        var fl = document.getElementById("FileRS").appendChild(new FilesList(false));
                        fl.DownloadURL = "../../../Api/Action/Files/TempDownloadFile.php?id=";
                        fl.currentdir = "/";
                            fl.ChDir=function (v) {
                                ss.Post("../../../Api/Ajax/FilesTemp/GetFiles.php", {"Location": v}, function (data) {
                                fl.currentdir = v;
                                fl.ClearFileList();
                                data = JSON.parse(data);
                                for (var i in data) {
                                    var ext = data[i]["ext"];
                                    if (["jpg", "gif", "png", "jpeg"].indexOf(ext) >= 0) {
                                        fl.AddFile(data[i]["name"], data[i]["fullpath"], "../../../Api/Action/Files/TempImagePreview.php?id=" + data[i]["fullpath"], data[i]["size"], data[i]["modified"], data[i]["type"]);
                                    } else {
                                        fl.AddFile(data[i]["name"], data[i]["fullpath"], "", data[i]["size"], data[i]["modified"], data[i]["type"]);
                                    }

                                }
                            });
                            };
                            fl.ChDir(fl.currentdir);

                            fl.OpenFile=function (v) {
                                var ext = v.split('.').pop();
                            if (["mp4", "webm", "ogg"].indexOf(ext.toLowerCase()) >= 0) {
                                var player = dialog.VideoPlayer("../../../Api/Action/Files/TempDownloadFile.php?id=" + btoa(v))
                                player.ZIndex(999);
                                player.Width("800px");
                                player.Height("600px");
                            } else if (["mp3", "wma"].indexOf(ext.toLowerCase()) >= 0) {
                                var player = dialog.AudioPlayer("../../../Api/Action/Files/TempDownloadFile.php?id=" + btoa(v));
                                player.ZIndex(999);
                                player.Width("800px");

                            } else if (["jpg", "gif", "png", "jpeg"].indexOf(ext.toLowerCase()) >= 0) {
                                dialog.ImageViewer("../../../Api/Action/Files/TempDownloadFile.php?id=" + btoa(v)).ZIndex(999);
                            }
                            };
                            fl.PropertiesFile=function (v) {
                                ss.Post("../../../Api/Ajax/FilesTemp/GetPropertiesFile.php", {"Path": v}, function (data) {
                                data = JSON.parse(data);
                                var tl = dialog.TableLayout().Title("Properties").ZIndex(999);
                                tl.AddTableDom("Name", data["name"]);
                                tl.AddTableDom("Size", data["size"]);
                                tl.AddTableDom("Modified", data["modified"]);

                            });
                            };


                        ss.S("#BNDelete").Click(function () {
                            dialog.Confirm("Delete It????", function (name) {
                                var s = fl.GetSelectFiles();
                                ss.Post("../../../Api/Ajax/FilesTemp/DeleteFile.php", {"Files": (s)}, function (data) {
                                    fl.ChDir(fl.currentdir);
                                });
                            }).ZIndex(999);
                        });
                        ss.S("#BNEmpty").Click(function () {
                            dialog.Confirm("Empty ????", function (name) {
                                ss.Post("../../../Api/Ajax/FilesTemp/DeleteFile.php", {"Files": (["/"])}, function (data) {
                                    fl.ChDir(fl.currentdir);
                                });
                            }).ZIndex(999);
                        });
                        ss.S("#BNHome").Click(function () {
                            fl.currentdir = "/";
                            fl.ChDir(fl.currentdir);
                        });
                        ss.S("#BNRefresh").Click(function () {
                            fl.ChDir(fl.currentdir);
                        });



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
                        $UserData = $User->GetBasicUserData($_SESSION["UserID"]);
                        printf('<img  src="../../../Api/Action/Profile/GetUserIcon.php?id=%s" />', $UserData["userid"]);
                        echo '<span>' . $UserData["alias"] . '</span>';
                        ?>
                        <a href="../Config/Config.php">Config</a>
                        <a href="../../../Session/Action/Logout.php">Logout</a>
                    </div>
                </div>
                <div class="Container">
                    <div class="Nav" >
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
                                <li><a href="Manager.php">Manager</a></li>
                                <li style="font-weight: bold;">Temp</li>
                                <li><a href="Trash.php">Trash</a></li>

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
                                <li><a href="../Photo/ImageSlider.php">ImageSlider</a></li>
                                <li><a href="../Photo/PlayList.php">PlayList</a></li>
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
                    <div class="Section" >
                        <div style="width: 100%;box-sizing: border-box;" id="FileRS">
                        </div>
                    </div>
                    <div class="Aside" >
                        <div class="BorderBlock" >
                            <ul>
                                <li class="Title">Action</li>
                                <li> <a id="BNRefresh" href="#">Refresh</a></li>
                                <li class="Title">Folder</li>
                                <li> <a id="BNHome" href="#">Home</a></li>
                                <?php
                                if ($Permission->Writable($_SESSION["UserID"]) ) {
                                    ?>
                                    <li class="Title">Manager</li>
                                    <li> <a id="BNDelete" href="#">Delete</a></li>
                                    <li> <a id="BNEmpty" href="#">Empty</a></li>
                                    <?php
                                }
                            ?>
                            </ul>
                        </div>
                        <div class="BorderBlock">
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
                        <div class="BorderBlock">
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
            </body>
        </html>
        <?php
    } else {
     header("location: ../../../Session/AuthUserID.php");
    session_destroy();
}
