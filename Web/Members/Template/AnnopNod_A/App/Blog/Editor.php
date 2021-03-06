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
            <script src="../../../../../js/io/FilesUpload.js"></script>
            <script src="../../../../../js/dom/SSQueryFW.js"></script>
            <script src="../../../../../js/dom/SuperDialog/SuperDialog.js"></script>  
            <script src="../../../../../js/dom/SuperDialog/Template/Basic/MessageBox.js"></script> 
            <script src="../../../../../js/dom/SuperDialog/Template/Basic/Input.js"></script>  
            <script src="../../../../../js/dom/SuperDialog/Template/Basic/Multimedia.js"></script> 
            <script src="../../../../../js/dom/TableTools.js"></script>
            <script src="../../../../../js/dom/FilesList.js"></script>
            <script src="../../../../../js/office/WYSIWYG.js"></script>

            <script>
                var ss = new SSQueryFW();
                ss.DocumentReady(function () {
                    var ajax = new Ajax();
                    var dialog = new SuperDialog();
                    var dialogmsgbox=new SuperDialog_Template_MessageBox();
                    var dialoginput=new SuperDialog_Template_Input();
                    var Editor = new WYSIWYG("#EditorDialog");
                    var FL = new FilesList(document.getElementById("FileList"));
                    var FV = new FilesList(document.getElementById("FileViewer"));
                    var FU = new FilesUpload({
                        "url": "../../../../Api/Ajax/Blog/Zip/AddFilesToBlogZip.php",
                        "files": "file",
                        "path": "/"
                    }, {

                    });

                    Editor.Size("800px", "600px");
                    Editor.MouseDown = function () {

                        ss.S(".BNCMD").Each(function (dom) {
                            var cmd = dom.getAttribute("data-cmd");
                            if (!Editor.EXECommandState(cmd)) {
                                dom.style.borderStyle = "outset";
                            }
                        })
                    };
                    Editor.KeyUp = function () {

                        ss.S(".BNCMD").Each(function (dom) {
                            var cmd = dom.getAttribute("data-cmd");
                            if (!Editor.EXECommandState(cmd)) {
                                dom.style.borderStyle = "outset";
                            }
                        })
                    };

                    FL.OpenDir(function (v) {
                        ajax.Post("../../../../Api/Ajax/Files/List/GetFilesListByExtension.php", {"Path": v, "Ext": "BlogZip"}, function (data) {
                            FL.CurrentDIR = v;
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
                    FU.Log(function (v) {
                        ss.S("#PGFile").Val(v.FileProgress);
                        ss.S("#PGFOA").Val(v.AllProgress);
                        if (v.Complete) {
                            ss.S("#BNUpload").Disable(false);
                            ss.S("#BNCancelUpload").Hide();
                            FV.OpenDir(FV.CurrentDIR);
                            dialogmsgbox.Alert("Upload Complete");
                        } else if (v.Error) {
                            dialogmsgbox.Alert("Upload Error");
                            ss.S("#BNUpload").Disable(false);
                            ss.S("#BNCancelUpload").Hide();
                        }
                    });

                    FV.Delete(function (v) {
                        /*ajax.Post("../../../../Api/Ajax/Blog/GetBlogZipStat.php", {"Path": FV.FilePath, "Name": v}, function (data) {
                         });*/
                    });
                    FV.OpenDir(function (v) {
                        if ( this.FilePath==undefined){                        
                            dialogmsgbox.Alert("File Not Open");
                            return false;
                        }
                        ajax.Post("../../../../Api/Ajax/Blog/Zip/GetFileListFromBlogZip.php", {"Path": this.FilePath, "ZipPath": v}, function (data) {
                            ss.S("#LabFileName").Html(FV.FilePath + " => " + v);
                            FV.CurrentDIR = v;
                            FV.Clear();
                            data = JSON.parse(data);
                            for (var i in data) {
                                if (data[i]["type"] == "dir") {
                                    FV.AddDir(data[i]["name"], data[i]["fullpath"], data[i]["mtime"]);
                                } else if (data[i]["type"] == "file") {
                                    FV.AddFile(data[i]["name"], data[i]["fullpath"], data[i]["size"], data[i]["mtime"]);
                                }
                            }

                        });
                    });
                    FV.OpenFile(function (v) {
                        ajax.Post("../../../../Api/Ajax/Blog/Zip/GetBlogZipStat.php", {"Path": FV.FilePath, "Name": v}, function (data) {
                            data = JSON.parse(data);
                            var ext = data["name"].split('.').pop().toLowerCase();
                            if (["mp4", "webm", "ogg", "mp3", "wma", "jpg", "gif", "png", "jpeg"].indexOf(ext) >= 0) {
                                new SuperDialog_Template_Multimedia().MediaPlayer("../../../../Api/Action/Blog/Zip/GetBlogZipFile.php?path=" + FV.FilePath + "&name=" + v, ext);
                            } else if (["htm", "html"].indexOf(ext) >= 0) {
                                ajax.Get("../../../../Api/Ajax/Blog/Zip/GetBlogZipFileHtmlEdit.php", {"path": FV.FilePath, "name": v}, function (htmldata) {
                                    var dia = dialog.ImportOkCancel( "#EditorDialog", function () {
                                        ajax.Post("../../../../Api/Ajax/Blog/Zip/AddHtmlToBlogZip.php", {"Path": FV.FilePath, "Name": v, "Html": Editor.Html()}, function (data) {
                                            if (data == "1") {
                                                FV.OpenDir(FV.CurrentDIR);
                                                dia.Close();
                                            }
                                        });
                                    }).Title("Open=>" + v);
                                    Editor.DesignMode(true);
                                    Editor.Html(htmldata);
                                    ss.S("#HtmlCodeForEditor").Val(htmldata);
                                    return true;
                                });
                            }
                        });

                    });
                    FV.Properties(function (v) {
                        ajax.Post("../../../../Api/Ajax/Blog/Zip/GetBlogZipStat.php", {"Path": FV.FilePath, "Name": v}, function (data) {
                            data = JSON.parse(data);
                            var tl = dialog.TableLayout().Title("Properties");
                            tl.AddNewRowElement();
                            tl.AddNewCellElement("Name", data["name"]);
                            tl.AddNewRowElement();
                            tl.AddNewCellElement("Index", data["index"]);
                            tl.AddNewRowElement();
                            tl.AddNewCellElement("Size", data["size"]);
                            tl.AddNewRowElement();
                            tl.AddNewCellElement("CRC", data["crc"]);
                            tl.AddNewRowElement();
                            tl.AddNewCellElement("Modified", data["mtime"]);
                            tl.AddNewRowElement();
                            tl.AddNewCellElement("Compression Size", data["comp_size"]);

                        });
                    });
                    ss.S("#BNAddNewHtmlFile").Click(function () {
                        if (FV.FilePath !== undefined) {
                            var p = dialoginput.Prompt("Name", function (v) {
                                v = v + ".html";
                                var Name = FV.CurrentDIR + "/" + v;
                                Editor.Html("");
                                var dia = dialog.ImportOkCancel( "#EditorDialog", function (v) {
                                    ajax.Post("../../../../Api/Ajax/Blog/Zip/AddHtmlToBlogZip.php", {"Path": FV.FilePath, "Name": Name, "Html": Editor.Html()}, function (data) {
                                        if (data == "1") {
                                            FV.OpenDir(FV.CurrentDIR);
                                            dia.Close();
                                        }
                                    });
                                }).Title("Create New:" + Name);
                                Editor.DesignMode(true);
                                return true;
                            });
                        }
                    });
                    ss.S("#BNCancelUpload").Click(function () {
                        FU.Abort();
                    });
                    ss.S(".BNCMD").Click(function () {
                        var cmd = this.getAttribute("data-cmd");
                        Editor.EXECommand(cmd);
                        this.style.borderStyle = "inset";
                        ss.S(".BNCMD").Each(function (dom) {
                            var cmd = dom.getAttribute("data-cmd");
                            if (!Editor.EXECommandState(cmd)) {
                                dom.style.borderStyle = "outset";
                            }
                        })
                    });
                    ss.S("#BNCreateNew").Click(function () {
                        var p = dialoginput.Prompt("Name", function (v) {
                            ajax.Post("../../../../Api/Ajax/Blog/Zip/CreateBlogZip.php", {"Path": FL.CurrentDIR, "Name": v}, function (data) {
                                if (data == "1") {
                                    p.Close();
                                    FL.OpenDir(FL.CurrentDIR);
                                }
                            });
                        });
                    });
                    ss.S("#BNHome").Click(function () {
                        FV.OpenDir("/");
                    });

                    ss.S("#BNOpenDialog").Click(function () {
                        FL.OpenDir("/");
                        dialog.ImportOkCancel( "#OpenDialog", function (v) {
                            FV.FilePath = FL.GetSelectFiles(0);
                            FV.SetPreviewImage("../../../../Api/Action/Blog/Zip/GetBlogZipImagePreview.php?path=" + FV.FilePath + "&name=");
                            FV.OpenDir("/");
                            return true;
                        }).Title("Open");

                    });



                    ss.S(".BNInsertCMD").Click(function () {
                        if (this.getAttribute("data-cmd") == "createlink") {

                            ajax.Get("../../../../Api/Ajax/Blog/Zip/GetBlogZipAllFilesName.php", {"path": FV.FilePath}, function (data) {
                                data = JSON.parse(data);
                                var dd = dialoginput.DropDown(function (v) {
                                    Editor.EXECommand("CreateLink", false, v);
                                    return true;
                                });
                                for (var i in data) {
                                    dd.Add(data[i], data[i]);
                                }
                            });
                        }
                    });

                    ss.S("#BNRefresh").Click(function () {
                        FV.OpenDir(FV.CurrentDIR);
                    });
                    ss.S("#BNUpload").Change(function () {
                        if (FV.FilePath !== undefined) {
                            this.disabled = true;
                            ss.S("#BNCancelUpload").Show();
                            FU.SetPath(FV.FilePath);
                            FU.SetFiles(this.files);
                            FU.SetParam("uploadto", FV.CurrentDIR);
                            FU.Send();
                        }
                    });
                    ss.S("#OPTEditorMode").Change(function () {
                        if (this.value == "0") {
                            ss.S("#HtmlCodeForEditor").Hide();
                            Editor.Html(ss.S("#HtmlCodeForEditor").Val());
                            Editor.SetVisible(true);
                        } else if (this.value == "1") {

                            ss.S("#HtmlCodeForEditor").Show().Val(Editor.Html());
                            Editor.SetVisible(false);

                        }
                    });

                    return 0;

                    //ddddddddddddddddddddddddddddddddddddddd





                    /* FL.Delete = function (v) {
                     dialog.Confirm("Delete This File????", function (name) {
                     ajax.Post("../../../../Api/Ajax/Files/DeleteFiles.php", {"path": v}, function (data) {
                     if (data == "1") {
                     FL.OpenDir(FL.CurrentDIR);
                     } else {
                     dialogmsgbox.Alert(data);
                     }
                     });
                     });
                     };
                     */




                    /*
                     FL.Rename(function (v) {
                     dialoginput.Prompt("Rename", function (name) {
                     ajax.Post("../../../../Api/Ajax/Files/Rename.php", {"path": v, "newname": name}, function (data) {
                     FL.OpenDir(FL.CurrentDIR);
                     });
                     return  true;
                     });
                     });
                     
                     */



                    /* ss.S("#BNDelete").Click(function () {
                     if (FL.GetSelectFiles().length > 0) {
                     dialog.UnLock(function (p) {
                     var s = FL.GetSelectFiles();
                     ajax.Post("../../../../Api/Ajax/Files/DeleteFiles.php", {"path": s, "password": p}, function (data) {
                     if (data == "1") {
                     FL.OpenDir(FL.CurrentDIR);
                     } else {
                     dialogmsgbox.Alert(data);
                     }
                     });
                     return true;
                     });
                     }
                     });
                     */

                    /* ss.S("#BNNewFolder").Click(function (e) {
                     var p = dialoginput.Prompt("MKDIR", function (v) {
                     ajax.Post("../../../../Api/Ajax/Files/MKDIR.php", {"path": FL.CurrentDIR + "/" + v}, function (data) {
                     FL.OpenDir(FL.CurrentDIR);
                     p.Close();
                     });
                     });
                     });*/






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
                            url = "../../../../Api/Ajax/Files/MoveFiles.php";
                        } else if (mode == "copy") {
                            url = "../../../../Api/Ajax/Files/CopyFiles.php";
                        } else {
                            return;
                        }
                        ajax.Post(url, {"Path": FL.CurrentDIR, "Files": files}, function (data) {
                            FL.OpenDir(FL.CurrentDIR);
                            ss.S("#BNPaste").Data({"mode": null, "files": null});
                        });
                    });







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
                    <div id="LabFileName"></div>
                    <div id="FileViewer"></div>
                </main>
                <aside>
                    <div class="BorderBlock" >
                        <label class="TitleCenter" style="display: block;">Action</label>
                        <a class="MenuLink" id="BNOpenDialog" href="#">Create New/Open</a>
                        <a class="MenuLink" id="BNRefresh" href="#">Refresh</a>
                        <label class="TitleCenter" style="display: block;">Folder</label>
                        <a class="MenuLink" id="BNHome" href="#">Home</a>
                    </div>
                    <?php
                    if ($_SESSION["User"]["writable"] == 1) {
                        ?>
                        <div class="BorderBlock" >
                            <label class="TitleCenter" style="display: block;">New</label>
                            <a class="MenuLink" id="BNNewFolder"href="#">Folder</a>
                            <a class="MenuLink" id="BNAddNewHtmlFile"href="#">File</a>
                        </div>
                        <div class="BorderBlock" >
                            <label class="TitleCenter" style="display: block;">Manager</label>
                            <a class="MenuLink" href="#" id="BNCut">Cut</a>
                            <a class="MenuLink" href="#" id="BNCopy">Copy</a>
                            <a class="MenuLink" href="#" id="BNPaste">Paste</a>
                            <a class="MenuLink" id="BNDelete" href="#">Delete</a>
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
            <div id="OpenDialog" style="display: none;">
                <div style="text-align: right;">
                    <button id="BNCreateNew">New</button>
                </div>
                <div id="CHDIRList"></div>
                <div style="width: 100%;box-sizing: border-box;" id="FileList">
                </div>
            </div>
            <div id="EditorDialog" style="display: none;">
                <div>
                    <img  class="BNCMD" data-cmd="bold"  style="border-style: outset;"  src="../../../../../img/wysiwyg/bold.gif" width="22" height="22"  />
                    <img  class="BNCMD" data-cmd="italic"  style="border-style: outset;"  src="../../../../../img/wysiwyg/italic.gif" width="22" height="22"  />
                    <img  class="BNCMD" data-cmd="underline"  style="border-style: outset;"  src="../../../../../img/wysiwyg/underline.gif" width="22" height="22"  />
                    <img  class="BNCMD" data-cmd="cut"  style="border-style: outset;"  src="../../../../../img/wysiwyg/cut.gif" width="22" height="22"  />
                    <img  class="BNCMD" data-cmd="copy"  style="border-style: outset;"  src="../../../../../img/wysiwyg/copy.gif" width="22" height="22"  />
                    <img  class="BNCMD" data-cmd="paste"  style="border-style: outset;"  src="../../../../../img/wysiwyg/paste.gif" width="22" height="22"  />
                    <img  class="BNCMD" data-cmd="undo"  style="border-style: outset;"  src="../../../../../img/wysiwyg/undo.gif" width="22" height="22"  />
                    <img  class="BNCMD" data-cmd="redo"  style="border-style: outset;"  src="../../../../../img/wysiwyg/redo.gif" width="22" height="22"  />
                    <img class="BNCMD" data-cmd="justifyLeft" style="border-style: outset;" src="../../../../../img/wysiwyg/justifyleft.gif" width="22" height="22" />
                    <img  class="BNCMD" data-cmd="justifyCenter"   style="border-style: outset;" src="../../../../../img/wysiwyg/justifycenter.gif" width="22" height="22"  />
                    <img  class="BNCMD" data-cmd="justifyRight"  style="border-style: outset;"  src="../../../../../img/wysiwyg/justifyright.gif" width="22" height="22"  />
                    <img  class="BNCMD" data-cmd="insertUnorderedList"  style="border-style: outset;"  src="../../../../../img/wysiwyg/dottedlist.gif" width="22" height="22"  />
                    <img  class="BNCMD" data-cmd="insertOrderedList"  style="border-style: outset;"  src="../../../../../img/wysiwyg/numberedlist.gif" width="22" height="22"  />
                    <img  class="BNCMD" data-cmd="indent"  style="border-style: outset;"  src="../../../../../img/wysiwyg/indent.gif" width="22" height="22"  />
                    <img  class="BNCMD" data-cmd="outdent"  style="border-style: outset;"  src="../../../../../img/wysiwyg/outdent.gif" width="22" height="22"  />
                    <img  class="BNCMD" data-cmd="strikeThrough"  style="border-style: outset;"  src="../../../../../img/wysiwyg/strikethrough.gif" width="22" height="22"   />
                    <img  class="BNCMD" data-cmd="superscript"  style="border-style: outset;"  src="../../../../../img/wysiwyg/superscript.gif" width="22" height="22"   />
                    <img  class="BNCMD" data-cmd="subscript"  style="border-style: outset;"  src="../../../../../img/wysiwyg/subscript.gif" width="22" height="22"   />
                    <img  class="BNInsertCMD" data-cmd="createlink" title="InsertLink" style="border-style: outset;"  src="../../../../../img/wysiwyg/link.gif" width="22" height="22"  />
                    <img  class="BNCMD" data-cmd="unlink" title="RemoveLink"  style="border-style: outset;"  src="../../../../../img/wysiwyg/unlink.gif" width="22" height="22"  />
                    <img  class="BNCMD" data-cmd="removeFormat" title="RemoveFormat"   style="border-style: outset;"  src="../../../../../img/wysiwyg/removeformat.gif" width="22" height="22"  />
                    <span> 
                        Mode:
                        <select id="OPTEditorMode">
                            <option value="0">Editor</option>
                            <option value="1">Code</option>
                        </select>
                    </span>
                </div>
                <textarea id="HtmlCodeForEditor" style="display: none;width: 100%;height: 600px;box-sizing: border-box;resize: vertical;"></textarea>
            </div>         
        </body>
    </html>
    <?php
} else {
    header("location: ../../../../Auth/Login.php");
    session_destroy();
}
