<?php
session_start();
include_once '../../../../../../Class/DB/Config/DB/Config.php';
include_once '../../../../../../Class/DB/Config/DB/Software.php';
include_once '../../../../../../Class/DB/Com/User/SessionManager.php';
include_once '../../../../../../Class/DB/Com/User/Profile.php';
include_once '../../../../../../Class/DB/Com/Events/Viewer.php';
include_once '../../../../../../Class/DB/Com/Module/LoadModule.php';
include_once '../../../../../../Class/DB/Com/User/LoadModule.php';
include_once '../../../../../../Class/DB/Com/User/Permission.php';
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
    if (isset($_GET["path"]) && $Permission->Writable($_SESSION["UserID"])) {
        ?>
        <!DOCTYPE html>
        <html>
            <head>
                <meta charset="UTF-8">
                <title><?php echo ($_GET["path"]); ?></title>
                <link rel="stylesheet" href="../../css/Page.css">
                <style>
                    .ToolBoxTab{
                        margin-top: 1px;
                        background-color: burlywood;
                        border-style: solid;
                        border-width: thin;
                        display: none;
                    }
                    .page{
                        width: 100%;
                        height: 150px;
                        text-align: center;
                        border-style: solid;
                        border-width: thin;
                        margin-top: 1px;
                    }
                </style>
                <?php
                foreach ($UModule->LoadModule($_SESSION["UserID"], Com_User_LoadModule::Layout_Head) as $value) {
                    try {
                        include_once '../../../../../../Class/DB/UserModule/' . $value["filename"];
                        $mod = new $value["classname"]($UModule);
                        $mod->LoadConfig($value["config"]);
                        echo $mod->Execute();
                    } catch (Exception $ex) {
                        
                    }
                }
                ?>
                <script src="../../../../../js/dom/WordWord.js"></script>
                <script src="../../../../../js/dom/SSQueryFW.js"></script>
                <script src="../../../../../js/dom/SuperDialog.js"></script>
                <script src="../../../../../js/file/FilesList.js"></script>
                <script>
                    var ss = new SSQueryFW();
                    ss.DocumentReady(function () {
                        var editor = document.getElementById("Editor").appendChild(new WordWord());
                        editor.Pages = [];
                        var sd = new SuperDialog();
                        var autosavetime = 60 * 5;
                        editor.LastID = null;
                        editor.MouseDown = (function () {
                            ss.S(".BNCMD").ForEach(function (dom) {
                                var cmd = dom.getAttribute("data-cmd");
                                dom.style.borderStyle = "inset";
                                if (!editor.EXECommandState(cmd)) {
                                    dom.style.borderStyle = "outset";
                                }
                            });
                            ss.S(".OptColor,.OptFont").ForEach(function (dom) {
                                var cmd = dom.getAttribute("data-cmd");
                                dom.value = editor.GetValue(cmd);
                            });
                        });
                        editor.OverFlow = (function (html) {
                            var s = editor.Size();
                            if (editor.LastID == editor.Pages.length - 1) {
                                editor.Pages.push({
                                    "width": s.width, "height": s.height, "html": html
                                });
                            } else if (editor.LastID < editor.Pages.length) {
                                editor.Pages[editor.LastID + 1]["html"] = html + editor.Pages[editor.LastID + 1]["html"];
                            }
                            ss.S("#BNNumPage").Attr("max", editor.Pages.length);
                        });
                        ss.Post("../../../../Api/Ajax/WordWord/GetMetadata.php", {"path": ss.URLParam()["path"]}, function (json) {
                            var dat = JSON.parse(json);
                            var data = dat.Data;
                            editor.EditorCSS('width: 100%;height: 80vh;margin-left: auto;margin-right: auto;');
                            editor.DesignMode(true);
                            for (var i = 0; i < parseInt(data.DocCount); i++) {
                                editor.Pages.push(null);
                            }

                            ss.S("#BNNumPage").Attr("max", data.DocCount).Change();
                            editor.init();
                        });

                        setInterval(function () {

                            if (ss.S("#BNEnableAutoSave").Val() == "1") {
                                //  autosavetime--;
                            }
                            ss.S("#LabAutoSave").Html(autosavetime);
                            if (autosavetime == 0) {
                                ss.S("#BNSave").Click();
                                autosavetime = 60 * 5;
                            }
                        }, 1000);
                        /*  window.onbeforeunload = function () {
                         return "Do You Exit This App";
                         };*/
                        ss.S("#BNAddNew").Click(function () {
                            //ss.S("#BNNumPage").Attr("max", data.DocCount);
                        });

                        ss.S(".BNCMD").Click(function () {
                            var cmd = this.getAttribute("data-cmd");
                            editor.EXECommand(cmd);
                            this.style.borderStyle = "inset";
                            ss.S(".BNCMD").ForEach(function (dom) {
                                var cmd = dom.getAttribute("data-cmd");
                                if (!editor.EXECommandState(cmd)) {
                                    dom.style.borderStyle = "outset";
                                }
                            })
                        });
                        ss.S(".BNCMDTable").Click(function () {
                            var cmd = this.getAttribute("data-cmd");
                            if (cmd == "InsertTable") {
                                var dialog = sd.TableLayout(function () {
                                    editor.TableCommand(cmd, dialog.row.value, dialog.col.value);
                                    return true;
                                }).ZIndex(999).Title("Insert Table");
                                dialog.row = dialog.AddTableDom('row', '<input type="number" min="1" value="1" />');
                                dialog.col = dialog.AddTableDom('col', '<input type="number" min="1" value="1" />');
                            } else if (cmd == "DeleteRow") {
                                editor.TableCommand(cmd, 1);
                            } else if (cmd == "DeleteColumn") {
                                editor.TableCommand(cmd, 1);
                            } else if (cmd == "InsertColumn") {
                                editor.TableCommand(cmd, 1);
                            } else if (cmd == "InsertRow") {
                                editor.TableCommand(cmd, 1);
                            }
                        });

                        ss.S("#BNHiddenUpload").Change(function (e) {
                            var reader = new FileReader();
                            reader.onload = function (e) {
                                editor.Html(reader.result);
                                ss.S("#BNHiddenUpload").Val("");
                            };
                            reader.readAsText(e.target.files[0]);
                        });


                        ss.S("#BNNumPage").Change(function () {
                            var reindex = parseInt(this.value) - 1;
                            if (editor.LastID !== null) {
                                var s = editor.Size();
                                editor.Pages[editor.LastID] = {
                                    "width": s.width, "height": s.height, "html": editor.Html( )
                                };

                            }

                            if (editor.Pages[reindex] !== null) {

                                var dat = editor.Pages[reindex];
                                editor.Size(dat["width"], dat["height"]);
                                editor.Html(dat["html"]);

                                editor.LastID = reindex;
                            } else {
                                ss.Post("../../../../Api/Ajax/WordWord/GetDoc.php", {"path": ss.URLParam()["path"], "page": reindex}, function (json) {
                                    var dat = JSON.parse(json);
                                    var size = {
                                        "width": dat.Size.Width + dat.Size.Unit,
                                        "height": dat.Size.Height + dat.Size.Unit
                                    }
                                    editor.Size(size.width, size.height);
                                    editor.Html(dat["Html"]);
                                    editor.Pages[reindex] = {
                                        "width": size.width, "height": size.height, "html": dat["Html"]
                                    };
                                    editor.LastID = reindex;
                                });
                            }


                        });


                        ss.S(".BNInsertCMD").Click(function () {
                            var cmd = this.getAttribute("data-cmd");
                            sd.Prompt("Enter Value", function (v) {
                                editor.InsertCommand(cmd, v);
                            }).ZIndex(999).Title(this.title);
                        });

                        ss.S("#BNOpen").Click(function () {

                            var SaveBeforeExit = sd.SaveBeforeExit("Do You Save Before Exit").ZIndex(999).Title("Open");
                            SaveBeforeExit.OnDiscard = function () {
                                window.onbeforeunload = null;
                                window.location.replace("MainPage.php");
                            };
                            SaveBeforeExit.OnSave = function () {
                                editor.AfterSave = function () {
                                    window.onbeforeunload = null;
                                    window.location.replace("MainPage.php");
                                };
                                ss.S("#BNSave").Click();
                            };

                        });

                        ss.S("#BNSave").Click(function () {
                            if (editor.LastID !== null) {
                                var s = editor.Size();
                                editor.Pages[editor.LastID] = {
                                    "width": s.width, "height": s.height, "html": editor.Html( )
                                };
                            }
                            var dpw = sd.PleaseWait().ZIndex(999);
                            var dat = {};
                            dat.FullPath = ss.URLParam()["path"];
                            dat.Pages = editor.Pages;

                            for (var i = 0; i < dat.Pages.length; i++) {

                                var parser = new DOMParser();
                                var doc = parser.parseFromString(dat.Pages[i].html, "text/html");
                                console.log(doc.body.innerHTML);
                            }
                            /*  ss.Post("../../../../Api/Ajax/WordWord/SaveWordWordFile.php", dat, function (data) {
                             if (data == "1") {
                             autosavetime = 60 * 5;
                             dpw.Close();
                             if (editor.AfterSave) {
                             editor.AfterSave();
                             }
                             } else {
                             
                             }
                             
                             });*/
                        });
                        ss.S(".BNToolBoxTab").Click(function () {
                            var id = this.getAttribute("data-id");
                            ss.S(".ToolBoxTab").Hide();
                            ss.S(".ToolBoxTab[data-id='" + id + "']").Show();
                        });
                        ss.S("#BNUpload").Click(function () {
                            ss.S("#BNHiddenUpload").Click();
                        });

                        ss.S(".InputExecCommand").Change(function () {
                            var cmd = this.getAttribute("data-cmd");
                            editor[cmd](this.value);
                        });
                        ss.S(".OptColor,.OptFont").Change(function () {
                            var cmd = this.getAttribute("data-cmd");
                            editor.SetValue(cmd, this.value);
                        });
                        ss.S("#OPTMode").Change(function () {
                            if (this.value == "1") {
                                ss.S("#ViewCode").Hide();
                                ss.S("#Editor").Show();
                                editor.Html(ss.S("#TXTViewCode").Val());
                                ss.S("#TXTViewCode").Val("");
                            } else if (this.value == "2") {
                                ss.S("#Editor").Hide();
                                ss.S("#ViewCode").Show();
                                ss.S("#TXTViewCode").Val(editor.Html());
                            }

                        });
                        ss.S(".OPTCMDTable").Change(function () {
                            var cmd = this.getAttribute("data-cmd");
                            if (cmd == "BorderStyle") {
                                editor.TableCommand("BorderStyle", this.value);
                            }
                        });
                        ss.S(".OPTCMDTable").Change(function () {
                            var cmd = this.getAttribute("data-cmd");
                            if (cmd == "BorderStyle") {
                                editor.TableCommand("BorderStyle", this.value);
                            }
                        });

                        ss.S("#test").Click(function () {
                            console.log(editor.Pages);
                        });
                    });
                </script>
            </head>
            <body style="">

                <div id="Header" style="position: absolute;" >
                    <div style="width: 50%;">
                        <a href="../../index.php">
                            <img  src="../../../../../../File/Resource/Logo.png"/>
                        </a>
                    </div>
                    <div  style="width: 50%;text-align: right;">
                        <a href="../../index.php">MainPage</a>
                        <?php
                        $Dat = $User->GetBasicUserData($_SESSION["UserID"]);
                        printf('<img  src="../../../../Api/Action/Profile/Basic/GetUserIcon.php?id=%s" />', $Dat["userid"]);
                        echo '<span>' . $Dat["alias"] . '</span>';
                        ?>
                        <a href="../../Config/Config.php">Config</a>

                        <a  href="../../../../Session/Action/Logout.php">Logout</a>
                    </div>
                </div>
                <div class="Container">
                    <div class="Nav">
                        <div class="BorderBlock" style="margin-top: 1px;">
                            <span class="Title" style="display: block;">Audio</span>
                            <ul>
                                <li><a href="../../Audio/Player.php">Player</a></li>
                                <li><a href="../../Audio/PlayList.php">PlayList</a></li>
                            </ul>
                        </div>
                        <div class="BorderBlock" style="margin-top: 1px;">
                            <span class="Title" style="display: block;">Blog</span>
                            <ul>
                                <li><a href="../../Blog/Manage.php">Manage</a></li>
                                <li><a href="../../Blog/View.php">View</a></li>
                            </ul>
                        </div>

                        <div class="BorderBlock" style="margin-top: 1px;">
                            <span class="Title" style="display: block;">Event</span>
                            <ul>
                                <li><a href="../../Event/Manage.php">Manage</a></li>
                                <li><a href="../../Event/View.php">View</a></li>
                            </ul>
                        </div>
                        <div class="BorderBlock" style="margin-top: 1px;">
                            <span class="Title" style="display: block;">Files</span>
                            <ul>
                                <li><a href="../../Files/Manager.php">Manager</a></li>
                                <li><a href="../../Files/Temp.php">Temp</a></li>
                                <li><a href="../../Files/Trash.php">Trash</a></li>
                            </ul>
                        </div>
                        <div class="BorderBlock" style="margin-top: 1px;">
                            <span class="Title" style="display: block;">Office</span>
                            <ul>
                                <li><a href="../FinFin/MainPage.php">FinFin</a></li>
                                <li><a href="../FlowFlow/MainPage.php">FlowFlow</a></li>
                                <li><a href="../Image/MainPage.php">Image</a></li>
                                <li><a href="../PointPoint/MainPage.php">PointPoint</a></li>
                                <li><a href="../Statistics/MainPage.php">Statistics</a></li>
                                <li style="font-weight: bold;"> WordWord</li>
                                <li><a href="../WYSIWYG/NewDoc.php">WYSIWYG</a></li>
                                <li><a href="../XCell/MainPage.php">XCell</a></li>
                                <li><a href="../XCess/MainPage.php">XCess</a></li>
                            </ul>
                        </div>
                        <div class="BorderBlock" style="margin-top: 1px;">
                            <span class="Title" style="display: block;">Photo</span>
                            <ul>
                                <li><a href="../../Photo/ImageSlider.php">ImageSlider</a></li>
                                <li><a href="../../Photo/PlayList.php">PlayList</a></li>
                            </ul>
                        </div>
                        <div class="BorderBlock" style="margin-top: 1px;">
                            <span class="Title" style="display: block;">Share</span>
                            <ul>
                                <li><a href="../../Share/BlogViewer.php">Blog</a></li>
                                <li><a href="../../Share/EventViewer.php">Event</a></li>
                            </ul>
                        </div>
                        <?php
                        $Dat = array_merge($Module->LoadModule(Com_Module_LoadModule::Layout_Nav, Config_DB_Config::Access_Mode_Members), $Module->LoadModule(Com_Module_LoadModule::Layout_Nav, Config_DB_Config::Access_Mode_Public));
                        foreach ($Dat as $value) {
                            try {
                                echo ' <div class="BorderBlock" style="margin-top: 3px;" >';
                                include_once '../../../../../../Class/DB/Module/' . $value["filename"];
                                $mod = new $value["classname"]($Module);
                                printf('<label class="Title">%s</label>', $mod->GetTitle());
                                $mod->SetModuleID($value["id"]);
                                $mod->SetModulePage("../../Module/Page.php");
                                $mod->SetUserID($_SESSION["UserID"]);
                                echo $mod->Execute();
                                echo '</div>';
                            } catch (Exception $ex) {
                                
                            }
                        }
                        ?>
                    </div>
                    <div class="Section" style="box-sizing: border-box;">
                        <div style="margin-top: 1px;background-color: burlywood;border-style: solid;border-width: thin;">
                            <a class="BNToolBoxTab" data-id="Basic" href="#">Basic</a>
                            <a class="BNToolBoxTab" data-id="Color" href="#">Color</a>
                            <a class="BNToolBoxTab" data-id="Table" href="#">Table</a>
                        </div>
                        <div>
                            <div class="ToolBoxTab" data-id="Basic" style="display: block;" >
                                <img  id="BNAddNew"  style="border-style: outset;"  src="../img/wysiwyg/addnew.png" width="22" height="22"  />
                                <img  id="BNOpen"    style="border-style: outset;"  src="../img/wysiwyg/open.gif" width="22" height="22"  />
                                <img  id="BNSave"    style="border-style: outset;"  src="../img/wysiwyg/save.gif" width="22" height="22"  />
                                <img  id="BNUpload"    style="border-style: outset;"  src="../img/wysiwyg/upload.png" width="22" height="22"  />
                                <a href="<?php echo "../../../../Api/Action/Files/DownloadFile.php?id=" . base64_encode($_GET["path"]); ?>" download style="cursor: default;text-decoration: none;color: black;">   <img  style="border-style: outset;"  src="../img/wysiwyg/download.png" width="22" height="22"  /></a>
                                <img  class="BNCMD" data-cmd="bold"  style="border-style: outset;"  src="../img/wysiwyg/bold.gif" width="22" height="22"  />
                                <img  class="BNCMD" data-cmd="italic"  style="border-style: outset;"  src="../img/wysiwyg/italic.gif" width="22" height="22"  />
                                <img  class="BNCMD" data-cmd="underline"  style="border-style: outset;"  src="../img/wysiwyg/underline.gif" width="22" height="22"  />
                                <img  class="BNCMD" data-cmd="cut"  style="border-style: outset;"  src="../img/wysiwyg/cut.gif" width="22" height="22"  />
                                <img  class="BNCMD" data-cmd="copy"  style="border-style: outset;"  src="../img/wysiwyg/copy.gif" width="22" height="22"  />
                                <img  class="BNCMD" data-cmd="paste"  style="border-style: outset;"  src="../img/wysiwyg/paste.gif" width="22" height="22"  />
                                <img  class="BNCMD" data-cmd="undo"  style="border-style: outset;"  src="../img/wysiwyg/undo.gif" width="22" height="22"  />
                                <img  class="BNCMD" data-cmd="redo"  style="border-style: outset;"  src="../img/wysiwyg/redo.gif" width="22" height="22"  />
                                <img class="BNCMD" data-cmd="justifyLeft" style="border-style: outset;" src="../img/wysiwyg/justifyleft.gif" width="22" height="22" />
                                <img  class="BNCMD" data-cmd="justifyCenter"   style="border-style: outset;" src="../img/wysiwyg/justifycenter.gif" width="22" height="22"  />
                                <img  class="BNCMD" data-cmd="justifyRight"  style="border-style: outset;"  src="../img/wysiwyg/justifyright.gif" width="22" height="22"  />
                                <img  class="BNCMD" data-cmd="insertUnorderedList"  style="border-style: outset;"  src="../img/wysiwyg/dottedlist.gif" width="22" height="22"  />
                                <img  class="BNCMD" data-cmd="insertOrderedList"  style="border-style: outset;"  src="../img/wysiwyg/numberedlist.gif" width="22" height="22"  />
                                <img  class="BNCMD" data-cmd="indent"  style="border-style: outset;"  src="../img/wysiwyg/indent.gif" width="22" height="22"  />
                                <img  class="BNCMD" data-cmd="outdent"  style="border-style: outset;"  src="../img/wysiwyg/outdent.gif" width="22" height="22"  />
                                <img  class="BNCMD" data-cmd="strikeThrough"  style="border-style: outset;"  src="../img/wysiwyg/strikethrough.gif" width="22" height="22"   />
                                <img  class="BNCMD" data-cmd="superscript"  style="border-style: outset;"  src="../img/wysiwyg/superscript.gif" width="22" height="22"   />
                                <img  class="BNCMD" data-cmd="subscript"  style="border-style: outset;"  src="../img/wysiwyg/subscript.gif" width="22" height="22"   />
                                <img  class="BNInsertCMD" data-cmd="createlink" title="InsertLink" style="border-style: outset;"  src="../img/wysiwyg/link.gif" width="22" height="22"  />
                                <img  class="BNCMD" data-cmd="unlink" title="RemoveLink"  style="border-style: outset;"  src="../img/wysiwyg/unlink.gif" width="22" height="22"  />
                                <img  class="BNCMD" data-cmd="removeFormat" title="RemoveFormat"   style="border-style: outset;"  src="../img/wysiwyg/removeformat.gif" width="22" height="22"  />
                                <div style="display: inline;">
                                    <span >Font Size:</span>
                                    <select class="OptFont" data-cmd="fontSize">
                                        <option value="1">1</option>
                                        <option value="2">2</option>
                                        <option value="3">3</option>
                                        <option value="4">4</option>
                                        <option value="5">5</option>
                                        <option value="6">6</option>
                                        <option value="7">7</option>
                                    </select>

                                </div>
                                <div style="display: inline;">
                                    <span>Mode: </span>
                                    <select id="OPTMode">
                                        <option value="1">Design</option>
                                        <option value="2">Code</option>
                                    </select>

                                </div>
                            </div>
                            <div class="ToolBoxTab" data-id="Color" style="display: none;">
                                <div style="display: inline;">
                                    <span>Background Color:</span>
                                    <input type="color" class="InputExecCommand" data-cmd="BackgroundColor" />

                                </div>
                                <div style="display: inline;">
                                    <span>Font Color:  </span>
                                    <input type="color" class="OptColor"  data-cmd="foreColor"  />

                                </div>
                                <div style="display: inline;">
                                    <span>Hilite  Color: </span>
                                    <input type="color"  class="OptColor"  data-cmd="hiliteColor" />

                                </div>

                            </div>
                            <div class="ToolBoxTab" data-id="Table" style="display: none;">
                                <img class="BNCMDTable" title="InsertTable" data-cmd="InsertTable"  style="border-style: outset;"  src="../img/wysiwyg/table.gif" width="22" height="22"  />
                                <img class="BNCMDTable" title="Insert Row" data-cmd="InsertRow"  style="border-style: outset;"  src="../img/wysiwyg/insertrow.png" width="22" height="22"  />
                                <img class="BNCMDTable" title="Insert Column" data-cmd="InsertColumn"  style="border-style: outset;"  src="../img/wysiwyg/insertcol.png" width="22" height="22"  />
                                <img class="BNCMDTable" title="Delete Row" data-cmd="DeleteRow"  style="border-style: outset;"  src="../img/wysiwyg/deleterow.png" width="22" height="22"  />
                                <img class="BNCMDTable" title="Delete Column" data-cmd="DeleteColumn"  style="border-style: outset;"  src="../img/wysiwyg/deletecol.png" width="22" height="22"  />
                                <div style="display: inline;">
                                    <span>Border Style:</span>
                                    <select class="OPTCMDTable" data-cmd="BorderStyle">
                                        <option value="none">none</option>
                                        <option value="hidden">hidden</option>
                                        <option value="dashed">dashed</option>
                                        <option value="dotted">dotted</option>
                                        <option value="double">double</option>
                                        <option value="groove">groove</option>
                                        <option value="inset">inset</option>
                                        <option value="outset">outset</option>
                                        <option value="ridge">ridge</option>
                                        <option value="solid">solid</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <input id="test" type="button" value="TTT" />

                        <div id="Editor">

                        </div>
                        <div id="ViewCode" style="display: none;">
                            <textarea id="TXTViewCode" style="width: 100%;height: 512px;background-color: transparent;"></textarea>
                        </div>
                    </div>
                    <div class="Aside" >
                        <div class="BorderBlock" style="margin-top: 1px;">
                            <label class="Title">Files</label>
                            <div>
                                <span>AutoSave Time:</span>
                                <label id="LabAutoSave"></label>
                                <br>
                                <label>Enable</label>
                                <input id="BNEnableAutoSave" value="1" type="checkbox"  checked="true" />
                            </div>
                        </div>
                        <div class="BorderBlock" style="margin-top: 1px;">
                            <label class="Title">Page</label>
                            <input id="BNNumPage" type="number" name="" value="1" min="1" style="width: 100%;box-sizing: border-box;" />
                        </div> 
                        <div class="BorderBlock" style="margin-top: 1px;">
                            <label class="Title">My Event</label>
                            <?php
                            foreach ($Event->GetCurrentMyEvent($_SESSION["UserID"]) as $value) {
                                echo '<div>';
                                printf('<a href="../../Event/View.php?id=%s"><span style="font-weight: bold;">%s</span>', $value["id"], $value["name"]);
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
                                echo '<div>';
                                printf('<a href="../../Share/EventViewer.php?id=%s"><span style="font-weight: bold;">%s</span>', $value["id"], $value["name"]);
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
                                include_once '../../../../../../Class/DB/Module/' . $value["filename"];
                                $mod = new $value["classname"]($Module);
                                printf('<label class="Title">%s</label>', $mod->GetTitle());
                                $mod->SetModuleID($value["id"]);
                                $mod->SetModulePage("../../Module/Page.php");
                                $mod->SetUserID($_SESSION["UserID"]);
                                echo $mod->Execute();
                                echo '</div>';
                            } catch (Exception $ex) {
                                
                            }
                        }
                        ?>
                    </div>
                </div>
                <div style="display: none;" >
                    <input id="BNHiddenUpload" type="file" name="" value="" />
                </div>
            </body>
        </html>
        <?php
    } else {
        header("location: NewDoc.php");
    }
} else {
   header("location: ../../../../../Auth/Login.php");
    session_destroy();
}
