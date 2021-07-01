<?php
session_start();
include_once '../../../../../../Class/Core/Config/Config.php';
include_once '../../../../../../Class/Core/UI/NAV.php';
include_once '../../../../../../Class/Core/Module/Database.php';
include_once '../../../../../../Class/Com/Event/Database.php';
include_once '../../../../../../Class/Com/Event/Reader.php';
include_once '../../../../../../Class/Com/Blog/Database.php';
include_once '../../../../../../Class/Com/FilesACLS/Database.php';
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
            <script src="../../../../../js/io/Ajax.js"></script>
            <script src="../../../../../js/dom/SSQueryFW.js"></script>
            <script src="../../../../../js/dom/SuperDialog/SuperDialog.js"></script>  
            <script src="../../../../../js/dom/SuperDialog/Template/Basic/MessageBox.js"></script>   
            <script src="../../../../../js/dom/SuperDialog/Template/Basic/Input.js"></script>  
            <script src="../../../../../js/dom/SuperDialog/Template/Basic/Load.js"></script>
            <script src="../../../../../js/dom/SuperDialog/Template/Basic/Multimedia.js"></script>  
            <script src="../../../../../js/dom/TableTools.js"></script>
            <script src="../../../../../js/dom/FilesList.js"></script>
            <script src="../../../../../js/io/FilesUpload.js"></script>
            <script src="../../../../../js/image/TakePhoto.js"></script>

            <script>
                var ss = new SSQueryFW();
                ss.DocumentReady(function () {
                    var ajax = new Ajax();
                    var dialog = new SuperDialog();
                    var dialogmsgbox = new SuperDialog_Template_MessageBox();
                    var dialoginput = new SuperDialog_Template_Input();
                    var FL = new FilesList(document.getElementById("FileRS"));
                    var tablesharefile = new TableTools();
                    tablesharefile.Import(document.getElementById("TBShareFile"));
                    var fileupload = new FilesUpload({
                        "url": "../../../../Api/Ajax/Files/Manager/UploadFiles.php",
                        "files": "file",
                        "path": "/"
                    }, {

                    });
                    FL.SetDownload("../../../../Api/Action/Files/Download/DownloadFiles.php?path=");
                    fileupload.Log(function (v) {
                        ss.S("#PGFile").Val(v.FileProgress);
                        ss.S("#PGFOA").Val(v.AllProgress);
                        if (v.Complete) {
                            ss.S("#BNUpload").Disable(false);
                            ss.S("#BNCancelUpload").Hide();
                            FL.OpenDir(fileupload.currentdir);
                            dialogmsgbox.Alert("Upload Complete").ZIndex(999);
                        } else if (v.Error) {
                            dialogmsgbox.Alert("Upload Error").ZIndex(999);
                            ss.S("#BNUpload").Disable(false);
                            ss.S("#BNCancelUpload").Hide();
                        }
                    });

                    FL.Delete = function (v) {
                        dialogmsgbox.Confirm("Delete This File????", function (name) {
                            ajax.Post("../../../../Api/Ajax/Files/Manager/DeleteFiles.php", {"path": v}, function (data) {
                                if (data == "1") {
                                    FL.OpenDir(fileupload.currentdir);
                                } else {
                                    dialogmsgbox.Alert(data);
                                }
                            });
                        }).ZIndex(999);
                    };

                    FL.Multiple(true);
                    FL.SetPreviewImage("../../../../Api/Action/Files/Download/ImagePreview.php?id=");
                    FL.OpenDir(function (v) {
                        ajax.Post("../../../../Api/Ajax/Files/List/GetFilesListByExtension.php", {"Path": v}, function (data) {
                            fileupload.currentdir = v;
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
                        var ext = v.split('.').pop().toLowerCase();
                        if (["mp4", "webm", "ogg", "mp3", "wma", "jpg", "gif", "png", "jpeg"].indexOf(ext) >= 0) {
                            new SuperDialog_Template_Multimedia().MediaPlayer("../../../../Api/Action/Files/Download/DownloadFiles.php?path=" + (v));
                        } else if (ext == "txt") {
                            new SuperDialog_Template_Load().Load("../../../../Api/Action/Files/Download/DownloadFiles.php?path=" + (v)).Title(v);
                        }

                    })
                    FL.OpenDir("/");

                    FL.Properties(function (v) {
                        ajax.Get("../../../../Api/Ajax/Files/List/GetPropertiesFile.php", {"path": v}, function (data) {
                            data = JSON.parse(data);
                            var tl = dialog.TwoRow().Title("Properties");
                            tl.AddRow("Name", data["name"]);
                            tl.AddRow("Size", data["size"]);
                            tl.AddRow("Extension", data["ext"]);
                            tl.AddRow("md5", data["md5"]);
                            tl.AddRow("sha1", data["sha1"]);
                            tl.AddRow("Modified", data["modified"]);

                        });
                    });

                    FL.Rename(function (v) {
                        dialoginput.Prompt("Rename", function (name) {
                            ajax.Post("../../../../Api/Ajax/Files/Manager/Rename.php", {"path": v, "newname": name}, function (data) {
                                FL.OpenDir(fileupload.currentdir);
                            });
                            return  true;
                        }).ZIndex(999);
                    });



                    ss.S("#BNCancelUpload").Click(function () {
                        fileupload.Abort();
                    });

                    ss.S("#BNDelete").Click(function () {
                        if (FL.GetSelectFiles().length > 0) {
                            dialoginput.UnLock(function (p) {
                                var s = FL.GetSelectFiles();
                                ajax.Post("../../../../Api/Ajax/Files/Manager/DeleteFiles.php", {"path": s, "password": p}, function (data) {
                                    if (data == "1") {
                                        FL.OpenDir(fileupload.currentdir);
                                    } else {
                                        dialogmsgbox.Alert(data);
                                    }
                                });
                                return true;
                            });
                        }
                    });

                    ss.S("#BNHome").Click(function () {
                        FL.OpenDir("/");
                    });
                    ss.S("#BNNewFolder").Click(function (e) {
                        var p = dialoginput.Prompt("MKDIR", function (v) {
                            ajax.Post("../../../../Api/Ajax/Files/Manager/MKDIR.php", {"path": fileupload.currentdir + "/" + v}, function (data) {
                                FL.OpenDir(fileupload.currentdir);
                                p.Close();
                            });
                        });
                    });
                    ss.S("#BNRefresh").Click(function () {
                        FL.OpenDir(fileupload.currentdir);
                    });
                    ss.S("#BNUpload").Change(function () {
                        this.disabled = true;
                        ss.S("#BNCancelUpload").Show();
                        fileupload.SetPath(fileupload.currentdir);
                        fileupload.SetFiles(this.files);
                        fileupload.Send();
                    });

                    ss.S("#BNCut").Click(function () {
                        ss.S("#BNPaste").Data({"mode": "cut", "files": FL.GetSelectFiles()});
                    });
                    ss.S("#BNCopy").Click(function () {
                        ss.S("#BNPaste").Data({"mode": "copy", "files": FL.GetSelectFiles()});
                    });
                    ss.S("#BNPaste").Click(function () {
                        var mode = this.getAttribute("data-mode");
                        var files = this.getAttribute("data-files");
                        var url = "";
                        if (mode == "cut") {
                            url = "../../../../Api/Ajax/Files/Manager/MoveFiles.php";
                        } else if (mode == "copy") {
                            url = "../../../../Api/Ajax/Files/Manager/CopyFiles.php";
                        } else {
                            return;
                        }
                        ajax.Post(url, {"Path": fileupload.currentdir, "Files": files}, function (data) {
                            FL.OpenDir(fileupload.currentdir);
                            ss.S("#BNPaste").Data({"mode": null, "files": null});
                        });

                    });
                    ss.S("#TXTSearch").Input(function () {
                        ajax.Post("../../../../Api/Ajax/Files/List/SearchFiles.php", {"Path": fileupload.currentdir, "Name": this.value}, function (data) {
                            FL.Clear();
                            data = JSON.parse(data);
                            for (var i in data) {
                                if (data[i]["type"] == "DIR") {
                                    FL.AddDir(data[i]["name"], data[i]["fullpath"], data[i]["modified"]);
                                } else if (data[i]["type"] == "FILE") {
                                    FL.AddFile(data[i]["name"], data[i]["fullpath"], data[i]["size"], data[i]["modified"]);
                                }
                            }
                            ss.S("#CHDIRList").Html(" ");
                        });
                    });
                    ss.S("#BNAddShare").Click(function () {
                        dialoginput.DropDown(function (v) {
                            ajax.Post("../../../../Api/Ajax/Files/ACLS/AddACLS.php", {"Files": FL.GetSelectFiles(), "Access": v}, function (data) {

                            });
                        }).CopyOption("#CloneableOption").Title("Add Share");

                    });
                    ss.S("#BNCompress").Click(function () {
                       

                    });


                    ss.S("#BNShareManager").Click(function () {
                        ajax.Post("../../../../Api/Ajax/Files/ACLS/GetACLS.php", {"AccessMode": this.value}, function (data) {
                            data = JSON.parse(data);
                            tablesharefile.DeleteRowAfter(0);
                            var changeaccess = {};
                            for (var i = 0; i < data.length; i++) {
                                tablesharefile.InsertRow();
                                tablesharefile.InsertCellLastRow(data[i]["fullpath"]);
                                var select = tablesharefile.InsertCellLastRow('<select name=""><option value="1">Public</option><option value="0">Member</option><option value="-1">Remove</option></select>');
                                select.setAttribute("data-id", data[i]["id"]);
                                select.addEventListener("change", function () {
                                    changeaccess[this.getAttribute("data-id")] = this.value;
                                });
                                select.value = data[i]["public"];
                            }
                            var d = dialog.ImportOkCancel("#ShareFileDialog", function () {
                                ajax.Post("../../../../Api/Ajax/Files/ACLS/ChangeACLS.php", {"AccessList": changeaccess}, function (data) {
                                    d.Close();
                                });
                            }).Title("Share");

                        });

                    });


                    /*  
                         
                         
                         
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
                         
                         
                         
                     */

                });

            </script>
        </head>
        <body class="HolyGrail">
            <header class="Header">
                <div style="width: 50%;"></div>
                <div style="width: 50%;text-align: right;">
                    <?php
                    printf('<img src="../../../../Api/Action/Profile/Basic/GetUserIcon.php?id=%s"/>', $_SESSION["User"]["id"]);
                    printf('<span style="font-weight: bold;cursor: default;">%s</span>', $_SESSION["User"]["alias"]);
                    ?>       
                    <a  class="MenuLink" style="display: inline;" href="../../../../Auth/Action/Logout.php">LogOut</a>
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
                    <div style="display: flex;flex-direction: row;margin-top: 7px;">
                        <div style="width: 100%; box-sizing: border-box;">
                            <input id="TXTSearch" placeholder="Search" style="width: 100%;box-sizing: border-box;" type="text" name="" value="" />
                        </div>
                    </div>
                    <div id="CHDIRList"></div>
                    <div style="width: 100%;box-sizing: border-box;" id="FileRS">
                    </div>
                </main>
                <aside>
                    <div class="BorderBlock" >
                        <label class="TitleCenter" style="display: block;">Action</label>
                        <a  class="MenuLink" id="BNCompress" href="#">Compress</a>
                        <a  class="MenuLink" id="BNRefresh" href="#">Refresh</a>
                        <label class="TitleCenter" style="display: block;">Folder</label>
                        <a  class="MenuLink" id="BNHome" href="#">Home</a>

                    </div>
                    <?php
                    if ($_SESSION["User"]["writable"] == 1) {
                        ?>
                        <div class="BorderBlock" >
                            <label class="TitleCenter" style="display: block;">New</label>
                            <a class="MenuLink" id="BNNewFolder"href="#">Folder</a>
                            <a class="MenuLink" id="BNNewPhoto" href="#">Photo</a>
                        </div>
                        <div class="BorderBlock" >
                            <label class="TitleCenter" style="display: block;">Manager</label>
                            <a class="MenuLink" href="#" id="BNCut">Cut</a>
                            <a class="MenuLink" href="#" id="BNCopy">Copy</a>
                            <a class="MenuLink" href="#" id="BNPaste">Paste</a>
                            <a class="MenuLink" id="BNDelete" href="#">Delete</a>
                        </div>
                        <div class="BorderBlock" >
                            <label class="TitleCenter" style="display: block;">Share</label>
                            <a class="MenuLink"  id="BNAddShare" href="#">Add</a>
                            <a class="MenuLink" id="BNShareManager" href="#">Manager</a>
                        </div>
                        <div class="BorderBlock" >
                            <label class="TitleCenter" style="display: block;">Upload</label>
                            <input id="BNUpload"   type="file" multiple="multiple"   value="Upload" />
                            <span  style="text-align: left;display: block;font-weight: bold;word-wrap: break-word;">Filename:
                                <span id="UpLoadFName" style="font-weight: normal;"></span>
                            </span>
                            <span style="font-weight: bold;display: block;">File:</span>
                            <progress id="PGFile" style="display: block;width: 98%;"min =0 max="100" value="0"></progress>
                            <span style="font-weight: bold;display: block;">OverAll:</span>
                            <progress id="PGFOA" style="display: block;width: 98%;"min =0 max="100" value="0"></progress>
                            <input id="BNCancelUpload" type="button" style="display: none;width: 100%;" value="Cancel" />
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
