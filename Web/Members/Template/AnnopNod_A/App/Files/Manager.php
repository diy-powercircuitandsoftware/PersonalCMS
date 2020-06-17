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
            <link rel="stylesheet" href="../css/Page.css">
            <?php
            foreach ($modlist as $value) {
                echo $value->Execute(Module_SDK_Basic::Layout_Head);
            }
            ?>
            <script src="../../../../../js/io/Ajax.js"></script>
            <script src="../../../../../js/dom/SSQueryFW.js"></script>
            <script src="../../../../../js/dom/SuperDialog.js"></script>          
            <script src="../../../../../js/dom/TableTools.js"></script>
            <script src="../../../../../js/dom/FilesList.js"></script>
            <script src="../../../../../js/io/FilesUpload.js"></script>
            <script src="../../../../../js/image/TakePhoto.js"></script>

            <script>
                var ss = new SSQueryFW();
                ss.DocumentReady(function () {
                    var ajax = new Ajax();
                    var dialog = new SuperDialog();
                    var FL = new FilesList(document.getElementById("FileRS"));
                    var tablesharefile = new TableTools(document.getElementById("TBShareFile"));
                    var fileupload = new FilesUpload({
                        "url": "../../../../Api/Action/Files/UploadFiles.php",
                        "files":"file"
                    }, {
                        
                    });
                    fileupload.Log(function(v){
                        console.log(v);
                    });
                    FL.Multiple(true);
                    FL.SetPreviewImage("../../../../Api/Action/Files/ImagePreview.php?id=");
                    FL.OpenDir(function (v) {
                        ajax.Post("../../../../Api/Ajax/Files/GetFilesListByExtension.php", {"Path": v}, function (data) {
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
                            ss.S("#CHDIRList").Html(decodeURIComponent(v));
                        });
                    });
                    FL.OpenFile(function (v) {
                        if (["mp4", "webm", "ogg", "mp3", "wma", "jpg", "gif", "png", "jpeg"].indexOf(v.split('.').pop().toLowerCase()) >= 0) {
                            dialog.MediaPlayer("../../../../Api/Action/Files/DownloadFiles.php?path=" + (v));
                        }
                    })
                    FL.OpenDir("/");


                    ss.S("#BNDelete").Click(function () {
                        if (FL.GetSelectFiles().length > 0) {
                            dialog.UnLock(function (p) {
                                var s = FL.GetSelectFiles();
                                ajax.Post("../../../../Api/Action/Files/DeleteFiles.php", {"path": s, "password": p}, function (data) {
                                    if (data == "1") {
                                        FL.OpenDir(fileupload.currentdir);
                                    } else {
                                        dialog.Alert(data);
                                    }

                                });
                                return true;
                            }).ZIndex(999);
                        }
                    });

                    ss.S("#BNHome").Click(function () {
                        FL.OpenDir("/");
                    });
                    ss.S("#BNNewFolder").Click(function (e) {
                        var p = dialog.Prompt("MKDIR", function (v) {
                            ajax.Post("../../../../Api/Action/Files/MKDIR.php", {"path": fileupload.currentdir + "/" + v}, function (data) {
                                FL.OpenDir(fileupload.currentdir);
                                p.Close();
                            });
                        });
                    });
                    ss.S("#BNRefresh").Click(function () {
                        FL.OpenDir(fileupload.currentdir);
                    });
                    ss.S("#BNUpload").Change(function () {
                       this.disabled=true;
                       fileupload.SetParam("dir",   fileupload.currentdir);
                        fileupload.SetFiles(this.files);
                        fileupload.Send();
                    });

                    /*  
                         
                         
                     var bnupload = document.getElementById("");
                     var TBShareFile = document.getElementById("").appendChild(new TableTools());
                     var  =.appendChild(new FilesList(true));
                         
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
                         
                     fileupload.GetCurrentFileName = function (name) {
                     ss.S("#UpLoadFName").Html(name);
                     };
                     fileupload.Complete = function () {
                     fl.ChDir(fl.currentdir);
                     window.onbeforeunload = null;
                     dialog.Alert("Upload Complete").ZIndex(999);
                     ss.S("#BNCancelUpload").Hide();
                     };
                     fileupload.Error = function (message) {
                     window.onbeforeunload = null;
                     dialog.Alert(message).ZIndex(999);
                     ss.S("#PGByte").Val(0);
                     ss.S("#PGFile").Val(0);
                     ss.S("#PGFOA").Val(0);
                     ss.S("#BNCancelUpload").Hide();
                     };
                     fileupload.ByteTransferProgress = function (v) {
                     ss.S("#PGByte").Val((v * 100));
                     };
                     fileupload.UploadProgress = function (v) {
                     ss.S("#PGFile").Val((v * 100));
                     };
                     fileupload.OverallProgress = function (v) {
                     ss.S("#PGFOA").Val((v * 100));
                     };
                     fileupload.BeforeUpload = function () {
                     window.onbeforeunload = function () {
                     return "File Uploading...";
                     };
                     ss.S("#BNCancelUpload").Show();
                     };
                     ss.S("#BNCancelUpload").Click(function () {
                     fileupload.Abort();
                     });
                         
                         
                         
                     fl.Delete = function (v) {
                     dialog.Confirm("Delete This File????", function (name) {
                     ss.Post("../../../Api/Ajax/Files/MoveToTrash.php", {"Files": v}, function (data) {
                     fl.ChDir(fl.currentdir);
                     });
                     }).ZIndex(999);
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
                         
                     ss.S("#BNDeleteAccess").Click(function () {
                         
                     dialog.Confirm("Delete It????", function (name) {
                     var v = ss.S(".checkaccessfileid").Val();
                     ss.Post("../../../Api/Ajax/Files/DelShareList.php", {"IDList": v}, function (data) {
                     ss.S("#OPTMAccessMode").Change();
                     });
                     }).ZIndex(1000);
                         
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
                     });*/

                });

            </script>
        </head>
        <body>
            <header id="mainheader">
                <div style="width: 50%;"></div>
                <div style="width: 50%;text-align: right;">
                    <?php
                    printf('<img src="../../../../Api/Action/Profile/GetUserIcon.php?id=%s"/>', $_SESSION["User"]["id"]);
                    printf('<span style="font-weight: bold;cursor: default;">%s</span>', $_SESSION["User"]["alias"]);
                    ?>       
                    <a style="font-weight: bold;" href="../../../../Auth/Action/Logout.php">LogOut</a>
                </div>
            </header>
            <div class="LMR157015">
                <div>
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
                </div>
                <div>
                    <div style="display: flex;flex-direction: row;margin-top: 7px;">
                        <div style="width: 100%; box-sizing: border-box;">
                            <input id="TXTSearch" placeholder="Search" style="width: 100%;box-sizing: border-box;" type="text" name="" value="" />
                        </div>
                    </div>
                    <div id="CHDIRList"></div>
                    <div style="width: 100%;box-sizing: border-box;" id="FileRS">
                    </div>
                </div>
                <div>
                    <div class="BorderBlock" >
                        <label class="TitleCenter" style="display: block;">Action</label>
                        <a id="BNRefresh" href="#">Refresh</a>

                        <label class="TitleCenter" style="display: block;">Folder</label>
                        <a id="BNHome" href="#">Home</a>
                    </div>
                    <?php
                    if ($_SESSION["User"]["writable"] == 1) {
                        ?>
                        <div class="BorderBlock" >
                            <label class="TitleCenter" style="display: block;">New</label>
                            <a  style="display: block;" id="BNNewFolder"href="#">Folder</a>
                            <a  style="display: block;" id="BNNewPhoto" href="#">Photo</a>
                        </div>
                        <div class="BorderBlock" >
                            <label class="TitleCenter" style="display: block;">Manager</label>
                            <a style="display: block;" href="#" id="BNCutPaste">Cut</a>
                            <a style="display: block;" href="#" id="BNCopyPaste">Copy</a>
                            <a style="display: block;" id="BNDelete" href="#">Delete</a>
                        </div>
                        <div class="BorderBlock" >
                            <label class="TitleCenter" style="display: block;">Share</label>
                            <a style="display: block;"  id="BNAddShare" href="#">Add</a>
                            <a  style="display: block;" id="BNShareManager" href="#">Manager</a>
                        </div>
                        <div class="BorderBlock" >
                            <label class="TitleCenter" style="display: block;">Upload</label>
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
                </div>
            </div>



        </body>
    </html>
    <?php
} else {
    header("location: ../../../Session/AuthUserID.php");
    session_destroy();
}
