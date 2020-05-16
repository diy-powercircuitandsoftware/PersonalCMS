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
            <script src="../../../../js/io/FilesUpload.js"></script>
            <script src="../../../../js/dom/SuperDialog.js"></script>
            <script src="../../../../js/image/TakePhoto.js"></script>
            <script src="../../../../js/file/FilesList.js"></script>
            <script src="../../../../js/dom/TableTools.js"></script>
            <script>
                var ss = new SSQueryFW();
                ss.DocumentReady(function () {

                    var fileupload = new FilesUpload();
                    var dialog = new SuperDialog();
                    var bnupload = document.getElementById("BNUpload");
                    var TBShareFile = document.getElementById("TBShareFile").appendChild(new TableTools());
                    var fl = document.getElementById("FileRS").appendChild(new FilesList(true));
                    fl.DownloadURL = "../../../Api/Action/Files/DownloadFile.php?id=";
                    fl.currentdir = "/";
                    fileupload.URL = "../../../Api/Ajax/Files/Upload.php";
                    TBShareFile.Border(1);
                    TBShareFile.CSSText("width: 100%;box-sizing: border-box;");
                    TBShareFile.InsertRow();
                    TBShareFile.InsertHead('<input class="checkselectall" type="checkbox" />');
                    TBShareFile.InsertHead("File Path");
                    TBShareFile.InsertHead("Edit");
                    ss.S(bnupload).Change(function () {
                         fileupload.Upload(this.files);
                    });
                     
                    fileupload.GetCurrentFileName=function (name) {
                        ss.S("#UpLoadFName").Html(name);
                    };
                    fileupload.Complete=function () {
                        fl.ChDir(fl.currentdir);
                        window.onbeforeunload = null;
                        dialog.Alert("Upload Complete").ZIndex(999);
                        ss.S("#BNCancelUpload").Hide();
                    };
                    fileupload.Error=function (message) {
                        window.onbeforeunload = null;
                        dialog.Alert(message).ZIndex(999);
                        ss.S("#PGByte").Val(0);
                        ss.S("#PGFile").Val(0);
                        ss.S("#PGFOA").Val(0);
                        ss.S("#BNCancelUpload").Hide();
                    };
                    fileupload.ByteTransferProgress=function (v) {
                        ss.S("#PGByte").Val((v * 100));
                    };
                    fileupload.UploadProgress=function (v) {
                       ss.S("#PGFile").Val((v * 100));
                    };
                    fileupload.OverallProgress=function (v) {
                        ss.S("#PGFOA").Val((v * 100));
                    };
                    fileupload.BeforeUpload=function () {
                        window.onbeforeunload = function () {
                            return "File Uploading...";
                        };
                        ss.S("#BNCancelUpload").Show();
                    };
                    ss.S("#BNCancelUpload").Click(function () {
                        fileupload.Abort();
                    });

                    fl.ChDir = function (v) {
                        ss.Post("../../../Api/Ajax/Files/GetFiles.php", {"Location": v}, function (data) {
                            fl.currentdir = v;
                            fl.ClearFileList();
                            fileupload.PostJson = {"Location": v};
                            data = JSON.parse(data);
                            for (var i in data) {
                                var ext = (data[i]["ext"]).toLowerCase();
                                if (["jpg", "gif", "png", "jpeg"].indexOf(ext) >= 0) {
                                    fl.AddFile(data[i]["name"], data[i]["fullpath"], "../../../Api/Action/Files/ImagePreview.php?id=" + data[i]["fullpath"], data[i]["size"], data[i]["modified"], data[i]["type"]);
                                } else {
                                    fl.AddFile(data[i]["name"], data[i]["fullpath"], "", data[i]["size"], data[i]["modified"], data[i]["type"]);
                                }

                            }
                            ss.S("#CHDIRList").Html(decodeURIComponent(v));
                        });
                    };
                    fl.ChDir(fl.currentdir);

                    fl.Delete = function (v) {
                        dialog.Confirm("Delete This File????", function (name) {
                            ss.Post("../../../Api/Ajax/Files/MoveToTrash.php", {"Files": v}, function (data) {
                                fl.ChDir(fl.currentdir);
                            });
                        }).ZIndex(999);
                    };

                    fl.OpenFile = function (v) {

                        var ext = v.split('.').pop();
                        if (["mp4", "webm", "ogg"].indexOf(ext.toLowerCase()) >= 0) {
                            var player = dialog.VideoPlayer("../../../Api/Action/Files/DownloadFile.php?id=" + btoa(v))
                            player.ZIndex(999);
                            player.Width("800px");
                            player.Height("600px");
                        } else if (["mp3", "wma"].indexOf(ext.toLowerCase()) >= 0) {
                            var player = dialog.AudioPlayer("../../../Api/Action/Files/DownloadFile.php?id=" + btoa(v));
                            player.ZIndex(999);
                            player.Width("800px");

                        } else if (["jpg", "gif", "png", "jpeg"].indexOf(ext.toLowerCase()) >= 0) {
                            dialog.ImageViewer("../../../Api/Action/Files/DownloadFile.php?id=" + btoa(v)).ZIndex(999);
                        } else if (ext.toLowerCase() == "pdf") {
                            window.open('../../../Api/Action/Files/DownloadFile.php?id=' + btoa(v)+"&option=opendisable206", '_blank', 'fullscreen=yes');
                        }
                    };
                    fl.PropertiesFile = function (v) {
                        ss.Post("../../../Api/Ajax/Files/GetPropertiesFile.php", {"Path": v}, function (data) {
                            data = JSON.parse(data);
                            var tl = dialog.TableLayout().Title("Properties").ZIndex(999);
                            tl.AddTableDom("Name", data["name"]);
                            tl.AddTableDom("Size", data["size"]);
                            tl.AddTableDom("Modified", data["modified"]);

                        });
                    };
                    fl.RenameFile = function (v) {
                        dialog.Prompt("Rename", function (name) {
                            ss.Post("../../../Api/Ajax/Files/RenameFiles.php", {"Path": v, "Name": name}, function (data) {
                                fl.ChDir(fl.currentdir);
                            });
                        }).ZIndex(999);
                    };

                    ss.S("#BNAddShare").Click(function () {
                        dialog.Import("#AddShareDialog", function () {
                            ss.Post("../../../Api/Ajax/Files/AddShareList.php", {
                                "AuthName": ss.S("#TXTAddUserShare").Val(), "PW": ss.S("#TXTAddPWShare").Val(), "AccessMode": ss.S("#OPTAddAccessMode").Val(), "FilesList": fl.GetSelectFiles()}, function (data) {

                            });
                        }).Title("Share").ZIndex(999);
                    });

                    ss.S("#BNCutPaste").Click(function () {
                        if (this.cutdata === undefined || this.cutdata == null) {
                            if (fl.GetSelectFiles().length > 0) {
                                this.cutdata = fl.GetSelectFiles();
                                this.innerHTML = "Paste";
                                ss.S("#BNCopyPaste").Hide();
                            }
                        } else {
                            var ref = this;
                            ss.Post("../../../Api/Ajax/Files/MoveFile.php", {"Files": ref.cutdata, "DESC": fl.currentdir}, function (data) {
                                ref.innerHTML = "Cut";
                                ref.cutdata = null;
                                fl.ChDir(fl.currentdir);
                                ss.S("#BNCopyPaste").Show();
                            });
                        }
                    });
                    ss.S("#BNCopyPaste").Click(function () {
                        if (this.cutdata === undefined || this.cutdata == null) {
                            if (fl.GetSelectFiles().length > 0) {
                                this.cutdata = fl.GetSelectFiles();
                                this.innerHTML = "Paste";
                                ss.S("#BNCutPaste").Hide();
                            }
                        } else {
                            var ref = this;
                            var pw = dialog.PleaseWait().ZIndex(999);
                            ss.Post("../../../Api/Ajax/Files/CopyFile.php", {"Files": ref.cutdata, "DESC": fl.currentdir}, function (data) {
                                ref.innerHTML = "Copy";
                                ref.cutdata = null;
                                fl.ChDir(fl.currentdir);
                                ss.S("#BNCutPaste").Show();
                                pw.Close();
                            });
                        }
                    });
                    ss.S("#BNDelete").Click(function () {
                        if (fl.GetSelectFiles().length > 0) {
                            dialog.Confirm("Delete It????", function (name) {
                                var s = fl.GetSelectFiles();
                                ss.Post("../../../Api/Ajax/Files/MoveToTrash.php", {"Files": s}, function (data) {
                                    fl.ChDir(fl.currentdir);
                                });
                            }).ZIndex(999);
                        }
                    });
                    ss.S("#BNDeleteAccess").Click(function () {

                        dialog.Confirm("Delete It????", function (name) {
                            var v = ss.S(".checkaccessfileid").Val();
                            ss.Post("../../../Api/Ajax/Files/DelShareList.php", {"IDList": v}, function (data) {
                                ss.S("#OPTMAccessMode").Change();
                            });
                        }).ZIndex(1000);

                    });

                    ss.S("#BNHome").Click(function () {
                        fl.currentdir = "/";
                        fl.ChDir(fl.currentdir);
                    });

                    ss.S("#BNNewFolder").Click(function (e) {
                        dialog.Prompt("MKDIR", function (v) {
                            ss.Post("../../../Api/Ajax/Files/MkDir.php", {"Name": v, "Path": fl.currentdir}, function (data) {
                                fl.ChDir(fl.currentdir);
                            });
                        }).ZIndex(999);
                    });
                    ss.S("#BNNewPhoto").Click(function () {
                        var takephoto = new TakePhoto();
                        var cust = dialog.Custom();
                        cust.AddDOM(takephoto);
                        cust.OpenDialog();
                        cust.ZIndex(999);
                        cust.Title("Take Pictures");
                        takephoto.Open();
                        cust.AddButton("1", "Take Pictures");
                        cust.BeforeClose(function () {
                            takephoto.Close();
                        });
                        cust.CallbackResult = (function (rs) {
                            if (rs == "1") {
                                takephoto.TakePhoto();
                                takephoto.SaveToFile(function (f) {
                                    var ajax = ss.Ajax();
                                    var fd = new FormData();
                                    fd.append("Location", fl.currentdir);
                                    fd.append("Upload", f, f.name);
                                    ajax.Post("../../../Api/Ajax/Files/UploadOneByte.php", fd);
                                    ajax.ReadyStateChange().Success(function () {
                                        takephoto.ReSet();
                                        fl.ChDir(fl.currentdir);
                                    });

                                });
                            }
                        });
                    });
                    ss.S("#BNRefresh").Click(function () {
                        fl.ChDir(fl.currentdir);
                    });
                    ss.S("#BNShareManager").Click(function () {
                        ss.S("#OPTMAccessMode").Change();
                        dialog.Import("#ShareManagerDialog").Title("Share").ZIndex(999);
                    });
                    ss.S("#OPTMAccessMode").Change(function () {
                        ss.Post("../../../Api/Ajax/Files/GetShareList.php", {"AccessMode": this.value}, function (data) {
                            data = JSON.parse(data);
                            TBShareFile.DeleteRowAfter(0);
                            for (var i = 0; i < data.length; i++) {
                                TBShareFile.InsertRow();
                                TBShareFile.InsertCellLastRow('<input class="checkaccessfileid" type="checkbox" name="" value="' + data[i]["id"] + '" />');
                                TBShareFile.InsertCellLastRow(data[i]["fullpath"]);
                                TBShareFile.InsertCellLastRow('<button class="editaccesslist" data-id="' + data[i]["id"] + '" style="width:100%;box-sizing: border-box;">Edit</button>');
                            }
                        });
                    });
                    ss.S("#TBShareFile").Click(function (e) {
                        if (e.target.getAttribute("class") == "checkselectall") {
                            var chk = this.getElementsByClassName("checkaccessfileid");
                            for (var i = 0; i < chk.length; i++) {
                                chk[i].checked = e.target.checked;
                            }
                        } else if (e.target.getAttribute("class") == "editaccesslist") {
                            var t = dialog.TableLayout(function () {
                                ss.Post("../../../Api/Ajax/Files/UpdateShareList.php", {"ID": t.fileid, "AccessMode": t.Access.value, "AuthName": t.UserName.value, "PW": t.Password.value}, function (data) {
                                    ss.S("#OPTMAccessMode").Change();
                                    t.Close();
                                });

                            }).ZIndex(1000).Title("Edit");
                            t.Access = t.AddTableDom('Access:', '<select style="width: 100%;"><option value="0">None</option><option value="1">Public</option><option value="2">Member</option></select>');
                            t.UserName = t.AddTableDom('UserName:', '<input type="text" style="width: 100%;" />');
                            t.Password = t.AddTableDom('Password:', '<input type="password" style="width: 100%;" />');
                            t.fileid = e.target.getAttribute("data-id");
                        }
                    });

                    ss.S("#TXTSearch").Input(function () {
                        ss.Post("../../../Api/Ajax/Files/SearchFileName.php", {"Location": fl.currentdir, "Name": this.value}, function (data) {
                            fl.ClearFileList();
                            data = JSON.parse(data);
                            for (var i in data) {
                                var ext = data[i]["ext"];
                                if (["jpg", "gif", "png", "jpeg"].indexOf(ext) >= 0) {
                                    fl.AddFile(data[i]["name"], data[i]["fullpath"], "../../../Api/Action/Files/ImagePreview.php?id=" + data[i]["fullpath"], data[i]["size"], data[i]["modified"], data[i]["type"]);
                                } else {
                                    fl.AddFile(data[i]["name"], data[i]["fullpath"], "", data[i]["size"], data[i]["modified"], data[i]["type"]);
                                }
                            }
                            ss.S("#CHDIRList").Html("Search");
                        });
                    });

                });

            </script>
        </head>
        <body>

            <div id="Header"  >
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
                            <li><span style="font-weight: bold;">Manager</span></li>
                            <li><a href="Temp.php">Temp</a></li>
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
                <div id="FileSection" class="Section" >
                    <div style="display: flex;flex-direction: row;margin-top: 7px;">
                        <div style="width: 100%; box-sizing: border-box;">
                            <input id="TXTSearch" placeholder="Search" style="width: 100%;box-sizing: border-box;" type="text" name="" value="" />
                        </div>
                    </div>
                    <div id="CHDIRList"></div>
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
                            if ($Permission->Writable($_SESSION["UserID"])) {
                                ?>
                                <li class="Title">New</li>
                                <li> <a id="BNNewFolder"href="#">Folder</a></li>
                                <li> <a id="BNNewPhoto" href="#">Photo</a></li>
                                <li class="Title">Manager</li>
                                <li> <a href="#" id="BNCutPaste">Cut</a></li>
                                <li> <a href="#" id="BNCopyPaste">Copy</a></li>
                                <li> <a id="BNDelete" href="#">Delete</a></li>
                                <li class="Title">Share</li>
                                <li> <a id="BNAddShare" href="#">Add</a></li>
                                <li> <a id="BNShareManager" href="#">Manager</a></li>
                                <li class="Title">Upload</li>
                                <li style="text-align: center;overflow: hidden;">
                                    <input id="BNUpload"   type="file" multiple="multiple"   value="Upload" />
                                    <span  style="text-align: left;display: block;font-weight: bold;word-wrap: break-word;">Filename:
                                        <span id="UpLoadFName" style="font-weight: normal;"></span>
                                    </span>
                                    <span style="font-weight: bold;display: block;">ChunkSize:</span>
                                    <progress id="PGByte" style="display: block;width: 98%;" min =0 max="100" value="0"></progress>
                                    <span style="font-weight: bold;display: block;">File:</span>
                                    <progress id="PGFile" style="display: block;width: 98%;"min =0 max="100" value="0"></progress>
                                    <span style="font-weight: bold;display: block;">OverAll:</span>
                                    <progress id="PGFOA" style="display: block;width: 98%;"min =0 max="100" value="0"></progress>
                                    <input id="BNCancelUpload" type="button" style="display: none;width: 100%;" value="Cancel" />
                                </li>
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
            <div id="AddShareDialog" style="display: none;">
                <table style="width: 98%;">
                    <tr>
                        <td>Access Mode:</td>
                        <td>
                            <select id="OPTAddAccessMode" style="width: 100%;">
                                <?php
                                foreach ($DBConfig->GetAccessMode() as $value) {
                                    printf('<option value="%s">%s</option>', $value["value"], $value["name"]);
                                }
                                ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>UserName:</td>
                        <td>
                            <input type="text" style="width: 100%;" id="TXTAddUserShare" />
                        </td>
                    </tr>
                    <tr>
                        <td>Password:</td>
                        <td>
                            <input type="password" style="width: 100%;" id="TXTAddPWShare" />
                        </td>
                    </tr>

                </table>
            </div>
            <div id="ShareManagerDialog" style="display: none;">
                <table style="width: 98%;">
                    <tr>
                        <td>Access Mode:</td>
                        <td>
                            <select id="OPTMAccessMode" style="width: 100%;">
                                <?php
                                foreach ($DBConfig->GetAccessMode() as $value) {
                                    printf('<option value="%s">%s</option>', $value["value"], $value["name"]);
                                }
                                ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <div id="TBShareFile">

                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2"><button id="BNDeleteAccess" style="width: 100%">Remove</button></td>
                    </tr>
                </table>
            </div>
        </body>
    </html>
    <?php
} else {
     header("location: ../../../Session/AuthUserID.php");
    session_destroy();
}
