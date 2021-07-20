<?php
session_start();
include_once '../../../../../../../Class/Core/Config/Config.php';
include_once '../../../../../../../Class/Core/UI/NAV.php';
include_once '../../../../../../../Class/Core/Module/Database.php';
include_once '../../../../../../../Class/Com/Event/Database.php';
include_once '../../../../../../../Class/Com/Event/Reader.php';
include_once '../../../../../../../Class/SDK/Module/Basic.php';
include_once '../../../../../Auth/Action/VerifySession.php';
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
            <title> <?php echo $_GET["path"]; ?></title>
            <link rel="stylesheet" type="text/css" href="../../../../../../css/HolyGrail.css">
            <link rel="stylesheet" type="text/css" href="../../../../../../css/PersonalCMS.css">
            <?php
            foreach ($modlist as $value) {
                echo $value->Execute(Module_SDK_Basic::Layout_Head);
            }
            ?>
            <style>
                .BNCMDDialog{
                    cursor: pointer; 
                }
                .BNToolBoxTab{
                    text-decoration: none;
                    color: blue;
                }
                .ToolBoxTab{
                    margin-top: 1px;
                    background-color: burlywood;
                    border-style: solid;
                    border-width: thin;
                    display: none;
                }
                .TPList{
                    margin-left: 1px;
                    margin-top: 1px;
                    border-style: solid;
                    border-width: thin;
                    width: 100px;
                }
                .TPPreview{
                    background-color: white;
                    height: 100px;
                    width: 98%;
                    border-style: solid;
                    border-width: thin;
                }
                .SlidesList{
                    width: 98%;
                    margin-left: 1%;
                    min-height: 100px;
                    border-style: solid;
                    border-width: thin;
                    margin-top: 3px;
                }

            </style>
            <script src="../../../../../../js/dom/SuperDialog/SuperDialog.js"></script>
            <script src="../../../../../../js/dom/SuperDialog/Template/Basic/Load.js"></script>
            <script src="../../../../../../js/dom/SuperDialog/Template/Basic/MessageBox.js"></script>
            <script src="../../../../../../js/dom/SuperDialog/Template/Basic/Input.js"></script>
            <script src="../../../../../../js/dom/SSQueryFW.js"></script>
            <script src="../../../../../../js/io/Ajax.js"></script>
            <script src="../../../../../../js/office/PointPoint/PointPoint.js"></script>
            <script src="../../../../../../js/office/PointPoint/Editor/Editor.js"></script>
            <script src="../../../../../../js/office/PointPoint/Player/Player.js"></script>
            <script src="../../../../../../js/office/PointPoint/Player/Animation/Hide.js"></script>
            <script>
                var ss = new SSQueryFW();
                ss.DocumentReady(function () {
                    var sd = new SuperDialog();
                    var superdialogload = new SuperDialog_Template_Load();
                    var superdialogmsgbox = new SuperDialog_Template_MessageBox();
                    var superdialoginput = new SuperDialog_Template_Input();
                    var ajax = new Ajax();
                    var pointpointeditor = new PointPoint_Editor(document.getElementById("Editor"));
                    var pointpoint = new PointPoint();
                    ss.S("#AnimationList").Append(new PointPoint_Animation_Hide().GetClassName(),new PointPoint_Animation_Hide().GetName());
                    if (ss.URLParam()["path"] !== undefined) {
                        var url = ss.URLParam()["path"];
                        var dpw = superdialogload.PleaseWait();
                        if (url.charAt(url.length - 1) === "#") {
                            url = url.slice(0, -1);
                        }
                        pointpointeditor.path = url;
                        ajax.Post("../../../../../Api/Ajax/Office/PointPoint/Manager/LoadAllData.php", {"path": pointpointeditor.path}, function (data) {
                            data = JSON.parse(data);
                            if (data !== null) {
                                var Slides = data.Slides;
                                if (Slides.length > 0) {
                                    for (var i = 0; i < Slides.length; i++) {
                                        if (Slides[i]) {
                                            var parser = new DOMParser();
                                            var dom = parser.parseFromString(Slides[i], "text/html").body.querySelector('[pointpoint-type="slide"]');
                                            var index = parseInt(dom.getAttribute("pointpoint-index"));
                                            pointpoint.ReplaceHtml(index, dom.innerHTML);
                                        }
                                    }
                                }

                                if (Slides.length == 0) {
                                    pointpoint.AddSlide(new PointPoint_Slide(0));
                                }
                                ss.S("#SlidesIndexList").Attr("max", pointpoint.Count()).Change();
                                dpw.close();
                            } else {
                                window.location.replace("index.php");
                            }
                        });
                    } else {
                        window.location.replace("index.php");
                    }

                    pointpointeditor.AfterSave = function () {};

                    pointpointeditor.MouseDown = function (e) {
                        ss.S("#AudioType").Val("");
                        ss.S("#AnimationList").Val("");
                        ss.S("#AnimationTime").Val(0);
                        if (e.selectitem == null) {
                            return;
                        }

                        if (ss.S("#OPTSelectMode").Val() == "edit" && e.selectitemtype == "text") {
                            e.selectitem.contentEditable = "true";
                            UpdateCommandState();
                        } else if (e.selectitemtype == "text") {
                            e.selectitem.contentEditable = "false";
                        }
                        if (ss.S("#OPTSelectMode").Val() == "delete") {
                            e.selectitem.parentNode.removeChild(e.selectitem);
                        }

                        ss.S("#AnimationList").Val(e.selectitem.getAttribute("pointpoint-animate"));
                        ss.S("#AnimationTime").Val(parseInt(e.selectitem.getAttribute("pointpoint-animate-time")) || 0);
                        ss.S("#AudioType").Val(e.selectitem.getAttribute("pointpoint-animate-audio-type"));

                        UpdateAudioFilesList(ss.S("#AudioType").Val(), function () {
                            ss.S("#AudioFile").Val(e.selectitem.getAttribute("pointpoint-animate-audio")).Change();
                        });

                    };

                    pointpointeditor.MouseMove = function (e) {
                        if ((ss.S("#OPTSelectMode").Val() == "move") && (e.selectitem != null)) {
                            var DomRect = e.selectitem.getBoundingClientRect();
                            var X = e.point_x - (DomRect.width * 0.5);
                            var Y = e.point_y - (DomRect.height * 0.5);
                            pointpointeditor.selectitem.style.left = X + "px";
                            pointpointeditor.selectitem.style.top = Y + "px";
                            pointpointeditor.selectitem.style.cursor = "move";
                        }
                    };
                    pointpointeditor.AddEditorEvent("mouseup", function (e) {
                        if ((ss.S("#OPTSelectMode").Val() == "move") && (pointpointeditor.selectitem != null)) {
                            pointpointeditor.selectitem.style.cursor = "";
                            pointpointeditor.selectitem = null;
                        }

                    });

                    pointpointeditor.AddEditorEvent("keyup", function (e) {
                        UpdateCommandState();

                    });



                    function UpdateCommandState() {
                        ss.S(".BNCMD").Each(function (dom) {
                            var cmd = dom.getAttribute("data-cmd");
                            if (pointpointeditor.QueryCommandState(cmd)) {
                                dom.style.borderStyle = "inset";
                            } else {
                                dom.style.borderStyle = "outset";
                            }
                            ;
                        });

                        ss.S(".OptColor,.OptFont").Each(function (dom) {
                            var cmd = dom.getAttribute("data-cmd");
                            //   dom.value = pointpointeditor.CommandValue(cmd);
                        });
                    }
                    function UpdateAudioFilesList(v, cb) {
                        var af = ss.S("#AudioFile");
                        if (v == "1") {
                            ajax.Post("../../../../Api/Ajax/PointPoint/GetEmbedList.php", {"path": pointpointeditor.path, "type": "Audio"}, function (json) {
                                json = JSON.parse(json);
                                af.Empty();
                                for (var i in json) {
                                    af.Append(json[i], json[i]);
                                }
                                cb();
                            });
                        } else if (v == "2") {
                            ajax.Post("../../../../../../sound/pointpoint/GetAllFiles.php", {}, function (json) {
                                json = JSON.parse(json);
                                af.Empty();
                                for (var i = 0; i < json.length; i++) {
                                    af.Append(json[i], json[i]);
                                }
                                cb();
                            });
                        } else {
                            af.Empty();
                        }
                    }

                    ss.S("#AnimationList").Change(function () {
                        if (pointpointeditor.selectitem !== null) {
                            pointpointeditor.selectitem.setAttribute("pointpoint-animate", this.value);
                        }
                    });
                    ss.S("#AnimationTime").Change(function () {
                        if (pointpointeditor.selectitem !== null) {
                            pointpointeditor.selectitem.setAttribute("pointpoint-animate-time", this.value);
                        }
                    });
                    ss.S("#AudioFile").Change(function () {
                        var at = ss.S("#AudioType");
                        if (at.Val() == "1") {//e
                            //  ss.S("#AudioPlay").Url("../../../../Api/Action/PointPoint/LoadAudio.php" + ss.JsonToQueryString({"path": pointpointeditor.path, "name": this.value}));
                        } else if (at.Val() == "2") {
                            ss.S("#AudioPlay").Val("../../../../../../sound/pointpoint/" + ss.S("#AudioFile").Val());
                        }
                        if (pointpointeditor.selectitem !== null) {
                            pointpointeditor.selectitem.setAttribute("pointpoint-animate-audio-type", at.Val());
                            pointpointeditor.selectitem.setAttribute("pointpoint-animate-audio", this.value);
                        }
                    });

                    ss.S("#AudioType").Change(function (e) {
                        UpdateAudioFilesList(this.value, function () {
                            ss.S("#AudioFile").Change();
                        });
                    });

                    ss.S("#BNAddNew").Click(function () {
                        sd.ImportOkCancel("#AddTPList", function () {
                            var t = ss.S("INPUT[name='TPType']").Val();
                            if (t == "Blank") {

                                pointpoint.AddSlide(new PointPoint_Slide(pointpoint.Count()));
                            }
                            ss.S("#SlidesIndexList").Attr("max", pointpoint.Count()).Change();

                            return true;

                        }).Title("Add New Slide");
                    });

                    ss.S(".BNCMD").Click(function () {
                        var cmd = this.getAttribute("data-cmd");
                        pointpointeditor.EXECommand(cmd, false, false);
                        this.style.borderStyle = "inset";
                        UpdateCommandState();
                    });
                    ss.S(".BNCMDInsert").Click(function () {
                        var cmd = this.getAttribute("data-cmd");
                        var v = parseInt(ss.S("#SlidesIndexList").Val()) - 1;

                        if (cmd == "TxtBox" && v >= 0) {
                            pointpoint.Get(v).AddText("Click For Edit", "50%", "50%");
                        } else if (cmd == "Image") {
                            /*   ss.Post("../../../../Api/Ajax/PointPoint/GetEmbedList.php", {"path": pointpointeditor.path, "type": "Image"}, function (data) {
                             data = JSON.parse(data);
                             var tl = sd.TableLayout(function () {
                             var img = pointpointeditor.AddImage();
                             img.src = "../../../../Api/Action/PointPoint/LoadImage.php" + ss.JsonToQueryString({
                             "path": pointpointeditor.path,
                             "imagepath": tl.sel.value,
                             "width": tl.w.value,
                             "height": tl.h.value
                             });
                             img.Embed = {
                             "FileType": 1,
                             "Path": tl.sel.value
                             };
                             img.Dimension = {
                             "Width": tl.w.value,
                             "Height": tl.h.value
                             };
                             return  true;
                             }).ZIndex(999).Title("Select Image");
                             tl.sel = tl.AddTableDom('<select style="width:100%;box-sizing: border-box;"></select>');
                             tl.w = tl.AddTableDom('width', '<input type="number"  value="" />');
                             tl.h = tl.AddTableDom('height', '<input type="number"  value="" />');
                             var ps = pointpointeditor.PaperSize();
                             tl.w.value = parseInt(ps.width) / 2;
                             tl.h.value = parseInt(ps.height) / 2;
                             for (var k in data) {
                             var opt = tl.sel.appendChild(document.createElement("OPTION"));
                             opt.innerHTML = k;
                             opt.value = data[k];
                             }
                             });*/

                        }

                    });
                    ss.S("#BNHiddenUpload").Change(function (e) {
                        ajax.Post("../../../../../Api/Ajax/Office/PointPoint/Manager/UploadEmbedFile.php", {
                            "FullPath": pointpointeditor.path,
                            "UploadFile": this.files[0]
                        }, function () {

                        });



                    });

                    ss.S("#BNUpload").Click(function () {
                        ss.S("#BNHiddenUpload").Click();
                    });

                    ss.S("#BNObjectManager").Click(function (e) {
                        ss.S("#EmbedType").Change();
                        sd.Import("#ObjManagerDialog").Title("Object Manager");

                    });

                    ss.S("#BNOpen").Click(function () {
                        superdialogmsgbox.SaveBeforeExit(function (v) {
                            if (v == -1) {
                                window.onbeforeunload = null;
                                window.location.replace("index.php");
                            } else if (v == 1) {
                                pointpointeditor.AfterSave = function () {
                                    window.onbeforeunload = null;
                                    window.location.replace("index.php");
                                };
                                ss.S("#BNSave").Click();
                            }
                        }).Title("Do You Save Before Open Document");


                    });
                    ss.S("#BNSave").Click(function () {
                        var slides = pointpoint.Serialize();
                        var dpw = superdialogload.PleaseWait();
                        ajax.Post("../../../../../Api/Ajax/Office/PointPoint/Manager/EditSlideData.php", {"path": pointpointeditor.path, "list": slides}, function (data) {
                            pointpointeditor.AfterSave();
                            dpw.close();
                        });

                    });
                    ss.S("#BNSize").Click(function () {
                        var s = pointpoint.Get(parseInt(ss.S("#SlidesIndexList").Val()) - 1).GetSlide();
                        superdialoginput.Size(function (v) {
                            s.style.width = v.width + "px";
                            s.style.height = v.height + "px";
                        }, parseInt(s.style.width), parseInt(s.style.height));

                    });
                    ss.S(".BNToolBoxTab").Tabs(".ToolBoxTab", "data-id");
                    ss.S("#EmbedType").Change(function (e) {
                        ajax.Post("../../../../../Api/Ajax/Office/PointPoint/Manager/GetEmbedList.php", {"path": pointpointeditor.path, "type": this.value}, function (data) {
                            data = JSON.parse(data);
                            ss.S("#EmbedList").Empty();
                            for (var i in data) {
                                ss.S("#EmbedList").Append(data[i], data[i]);
                            }
                        });
                    });
                    ss.S("#EmbedList").KeyUp(function (e) {
                        if (e.keyCode == 46) {
                            var v = this.value;
                            superdialogmsgbox.Confirm("Delete It", function () {
                                ajax.Post("../../../../../Api/Ajax/Office/PointPoint/Manager/DeleteObject.php", {"path": pointpointeditor.path, "delname": v}, function (data) {
                                    ss.S("#EmbedType").Change();
                                });
                            });
                        }
                    });


                    ss.S(".OptColor,.OptFont").Change(function () {
                        var cmd = this.getAttribute("data-cmd");
                        pointpointeditor.EXECommand(cmd, false, this.value);
                    });




                    ss.S(".SlideExecCommand").Click(function () {
                        var s = pointpoint.Get(parseInt(ss.S("#SlidesIndexList").Val()) - 1).GetSlide();
                        var cmd = this.getAttribute("data-cmd");
                        if (cmd == "Background") {
                            sd.ImportOkCancel("#BGDialog", function () {
                                var opt = ss.S("#SelBGType").Val();
                                if (opt == "none") {
                                    s.style.background = "";
                                } else if (opt == "color") {
                                    s.style.backgroundColor = ss.S("#SelBGColor").Val();

                                }

                            }).Title("Background");
                        }
                    });


                    ss.S("#SlidesIndexList").Change(function () {
                        var s = pointpoint.Get(parseInt(this.value) - 1).GetSlide();
                        pointpointeditor.Render(s);

                    });




                    return 0;


















                    ss.S(".BNCMDDialog").Click(function () {

                        if (pointpointeditor.selectdom) {

                            if (this.getAttribute("data-cmd") == "Rect") {
                                sd.PositionDialog(function (dat) {
                                    pointpointeditor.selectdom.style.left = dat.x + "px";
                                    pointpointeditor.selectdom.style.top = dat.y + "px";
                                    if (dat.w == 0) {
                                        pointpointeditor.selectdom.style.width = "";
                                    } else {
                                        pointpointeditor.selectdom.style.width = dat.w + "px";
                                        ;
                                    }
                                    if (dat.h == 0) {
                                        pointpointeditor.selectdom.style.height = "";
                                    } else {
                                        pointpointeditor.selectdom.style.height = dat.h + "px";
                                        ;
                                    }
                                    return true;
                                }, parseFloat(pointpointeditor.selectdom.style.left), parseFloat(pointpointeditor.selectdom.style.top), parseFloat(pointpointeditor.selectdom.style.width), parseFloat(pointpointeditor.selectdom.style.height));


                            }
                        }
                    });














                });
            </script>
        </head>
        <body class="HolyGrail">

            <header class="Header">
                <div style="width: 50%;"></div>
                <div style="width: 50%;text-align: right;">
                    <?php
                    printf('<img src="../../../../../Api/Action/Profile/Basic/GetUserIcon.php?id=%s"/>', $_SESSION["User"]["id"]);
                    printf('<span style="font-weight: bold;cursor: default;">%s</span>', $_SESSION["User"]["alias"]);
                    ?>       
                    <a class="MenuLink" style="display: inline;" href="../../../../../Auth/Action/Logout.php">LogOut</a>
                </div>
            </header>
            <div class="HolyGrail-body">
                <nav>
                    <?php
                    foreach ($uinav->FindAllMenuFile("../../../App") as $key => $valueA) {
                        echo '<div class="BorderBlock">';
                        printf(' <div class="TitleCenter">%s</div>', $key);
                        foreach ($valueA as $valueB) {

                            printf('  <a class="MenuLink" href="%s">%s</a>', "../../../App/" . $valueB["path"], $valueB["name"]);
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
                    <?php
                    if ($_SESSION["User"]["writable"] == 1) {
                        ?>
                        <div style="margin-top: 1px;background-color: burlywood;border-style: solid;border-width: thin;">
                            <a class="BNToolBoxTab" data-id="Basic" href="#">Basic</a>
                            <a class="BNToolBoxTab" data-id="Color" href="#">Color</a>

                            <a class="BNToolBoxTab" data-id="Insert" href="#">Insert</a>
                            <a class="BNToolBoxTab" data-id="Slide" href="#">Slide</a>

                            <a class="BNToolBoxTab" data-id="Animation" href="#">Animation</a>
                            <a class="BNToolBoxTab" data-id="Audio" href="#">Audio</a>
                        </div>
                        <div>
                            <div class="ToolBoxTab" data-id="Basic" style="display: block;" >
                                <img  id="BNOpen"    style="border-style: outset;"  src="../../../../../../img/io/open.gif" width="22" height="22"  />
                                <img  id="BNSave"    style="border-style: outset;"  src="../../../../../../img/io/save.gif" width="22" height="22"  />
                                <img  id="BNUpload"    style="border-style: outset;" title="UploadEmbedFile"  src="../../../../../../img/wysiwyg/upload.png" width="22" height="22"  />
                                <img  id="BNObjectManager"    style="border-style: outset;" title="ObjectManager"  src="../../../../../../img/wysiwyg/object.png" width="22" height="22"  />
                                <img  id="BNSize"    style="border-style: outset;" title="Resize" src="../../../../../../img/wysiwyg/docsize.png" width="22" height="22"  />
                                <img  class="BNCMD" data-cmd="bold"  style="border-style: outset;"  src="../../../../../../img/wysiwyg/bold.gif" width="22" height="22"  />
                                <img  class="BNCMD" data-cmd="italic"  style="border-style: outset;"  src="../../../../../../img/wysiwyg/italic.gif" width="22" height="22"  />
                                <img  class="BNCMD" data-cmd="underline"  style="border-style: outset;"  src="../../../../../../img/wysiwyg/underline.gif" width="22" height="22"  />
                                <img  class="BNCMD" data-cmd="cut"  style="border-style: outset;"  src="../../../../../../img/wysiwyg/cut.gif" width="22" height="22"  />
                                <img  class="BNCMD" data-cmd="copy"  style="border-style: outset;"  src="../../../../../../img/wysiwyg/copy.gif" width="22" height="22"  />
                                <img  class="BNCMD" data-cmd="paste"  style="border-style: outset;"  src="../../../../../../img/wysiwyg/paste.gif" width="22" height="22"  />
                                <img  class="BNCMD" data-cmd="undo"  style="border-style: outset;"  src="../../../../../../img/wysiwyg/undo.gif" width="22" height="22"  />
                                <img  class="BNCMD" data-cmd="redo"  style="border-style: outset;"  src="../../../../../../img/wysiwyg/redo.gif" width="22" height="22"  />
                                <img class="BNCMD" data-cmd="justifyLeft" style="border-style: outset;" src="../../../../../../img/wysiwyg/justifyleft.gif" width="22" height="22" />
                                <img  class="BNCMD" data-cmd="justifyCenter"   style="border-style: outset;" src="../../../../../../img/wysiwyg/justifycenter.gif" width="22" height="22"  />
                                <img  class="BNCMD" data-cmd="justifyRight"  style="border-style: outset;"  src="../../../../../../img/wysiwyg/justifyright.gif" width="22" height="22"  />
                                <img  class="BNCMD" data-cmd="insertUnorderedList"  style="border-style: outset;"  src="../../../../../../img/wysiwyg/dottedlist.gif" width="22" height="22"  />
                                <img  class="BNCMD" data-cmd="insertOrderedList"  style="border-style: outset;"  src="../../../../../../img/wysiwyg/numberedlist.gif" width="22" height="22"  />
                                <img  class="BNCMD" data-cmd="indent"  style="border-style: outset;"  src="../../../../../../img/wysiwyg/indent.gif" width="22" height="22"  />
                                <img  class="BNCMD" data-cmd="outdent"  style="border-style: outset;"  src="../../../../../../img/wysiwyg/outdent.gif" width="22" height="22"  />
                                <img  class="BNCMD" data-cmd="strikeThrough"  style="border-style: outset;"  src="../../../../../../img/wysiwyg/strikethrough.gif" width="22" height="22"   />
                                <img  class="BNCMD" data-cmd="superscript"  style="border-style: outset;"  src="../../../../../../img/wysiwyg/superscript.gif" width="22" height="22"   />
                                <img  class="BNCMD" data-cmd="subscript"  style="border-style: outset;"  src="../../../../../../img/wysiwyg/subscript.gif" width="22" height="22"   />
                                <img  class="BNInsertCMD" data-cmd="createlink" title="InsertLink" style="border-style: outset;"  src="../../../../../../img/wysiwyg/link.gif" width="22" height="22"  />
                                <img  class="BNCMD" data-cmd="unlink" title="RemoveLink"  style="border-style: outset;"  src="../../../../../../img/wysiwyg/unlink.gif" width="22" height="22"  />
                                <img  class="BNCMD" data-cmd="removeFormat" title="RemoveFormat"   style="border-style: outset;"  src="../../../../../../img/wysiwyg/removeformat.gif" width="22" height="22"  />
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
                                <div>
                                    <span >Mode:</span>
                                    <select id="OPTSelectMode">
                                        <option value="edit">Edit</option>
                                        <option value="delete">Delete</option>
                                        <option value="move">Move</option>
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

                            <div class="ToolBoxTab" data-id="Insert" style="display: none;">
                                <img  id="BNAddNew"  style="border-style: outset;"  src="../../../../../../img/wysiwyg/addnew.png" width="22" height="22"  />
                                <img class="BNCMDInsert" data-cmd="TxtBox"  style="border-style: outset;"  src="../../../../../../img/pointpoint/txtbox.png" width="22" height="22"  />
                                <img class="BNCMDInsert" data-cmd="Image"  style="border-style: outset;"  src="../../../../../../img/pointpoint/pic.png" width="22"  />
                                <img class="BNCMDInsert" data-cmd="InsertTable"  style="border-style: outset;"  src="../../../../../../img/wysiwyg/table.gif" width="22" height="22"  />
                            </div>
                            <div class="ToolBoxTab" data-id="Slide" style="display: none;">
                                <div style="display: inline;">
                                    <label>Background:</label>
                                    <input type="button" class="SlideExecCommand" data-cmd="Background" value="Select" />
                                </div>
                            </div>
                            <div class="ToolBoxTab" data-id="Animation" style="display: none;">
                                <label>Animation:</label>
                                <select id="AnimationList">
                                    <option value="">None</option>

                                </select>
                                <label>Time:</label>
                                <input id="AnimationTime" type="number" min="0" value="1" />
                            </div>
                            <div class="ToolBoxTab" data-id="Audio" style="display: none;">
                                <label>Type:</label>
                                <select id="AudioType">
                                    <option value="">None</option>
                                    <option value="1">Embed</option>
                                    <option value="2">Resource</option>

                                </select>
                                <label>Name:</label>
                                <select id="AudioFile">

                                </select>
                                <audio id="AudioPlay" src="" controls="controls"></audio>

                            </div>


                        </div>
                        <div  style="width: 100%; height: 80vh;border-style: solid;box-sizing: border-box;border-width: thin;overflow: auto;">
                            <div id="Editor" style="margin-left: auto;margin-right: auto;border-style: solid;width: max-content">

                            </div>
                        </div>

                        <?php
                    }
                    ?>
                </main>
                <aside>
                    <div class="BorderBlock" style="margin-top: 1px;">
                        <div class="TitleCenter">Player</div>
                        <a class="MenuLink" href="<?php echo 'Player.php?path=' . ($_GET["path"]); ?>" target="_blank"> Play</a>
                    </div>
                    <div class="BorderBlock" style="margin-top: 1px;">
                        <div class="TitleCenter">Slides</div>
                        <input style="width: 100%;box-sizing: border-box;" type="number" id="SlidesIndexList" min="1" value="1" />
                    </div>

                    <div class="BorderBlock" style="margin-top: 1px;">
                        <div class="TitleCenter">Event</div>
                        <?php
                        foreach ($event->GetComingEvent(Event_Database::Access_Member) as $value) {
                            echo '<div>';
                            printf('<a class="MenuLink" href="../../Event/View.php?id=%s"><span style="font-weight: bold;">%s</span>', $value["id"], $value["name"]);
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


            <div id="AddTPList" style="display: none;height: 80vh;width: 80vh;">
                <form>
                    <div style="display: flex;flex-direction: row;">
                        <div class="TPList"  >
                            <div class="TPPreview">

                            </div>
                            <br>
                            <input type="radio" name="TPType" value="Blank" />
                            <label>Blank</label>
                        </div>
                        <div class="TPList"  >
                            <div class="TPPreview" style="text-align: center;">
                                <span>Title</span>
                            </div>
                            <br>
                            <input type="radio" name="TPType" value="Title" />
                            <label>Title</label>
                        </div>
                        <div class="TPList" >
                            <div class="TPPreview" style="text-align: center;">
                                <span>Title</span>

                                <br>
                                <span>SubTitle</span>
                            </div>
                            <br>
                            <input   type="radio" name="TPType" value="TitleSubTitle" />
                            <label>Title And SubTitle</label>
                        </div>
                        <div class="TPList" >
                            <div class="TPPreview" style="text-align: center;">
                                <span>Title</span>
                                <br>
                                <span>Text</span>
                            </div>
                            <br>
                            <input   type="radio" name="TPType" value="TitleText" />
                            <label>Title And Text</label>
                        </div>
                    </div>
                </form>
            </div>

            <div style="display: none;" >
                <input id="BNHiddenUpload" type="file" name="UploadFile" value="" />
            </div>
            <table id="ObjManagerDialog" style="display: none;width: 100%;box-sizing: border-box;">
                <tr>
                    <td>Object Type:</td>
                    <td><select id="EmbedType"  style="width: 100%;box-sizing: border-box;">
                            <option value="Audio">Audio</option>
                            <option value="Image">Image</option>
                            <option value="Slides">Slides</option>
                            <option value="Video">Video</option>

                        </select>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <select id="EmbedList" style="width: 100%;box-sizing: border-box;" multiple="multiple">
                        </select>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">Press the delete button to delete it.</td>
                </tr>
            </table>
            <table id="BGDialog" style="display: none;">
                <tr>
                    <td>Background:</td>
                    <td>
                        <select id="SelBGType">
                            <option value="none">none</option>
                            <option value="color">color</option>
                        </select>
                    </td>
                </tr>
                <tr class="SelBGColorUI">
                    <td>Color:</td>
                    <td><input id="SelBGColor" type="color"  /></td>
                </tr>
            </table>

            <div id="TXTTableEdit" style="display: none;">

                <img class="BNCMDTable" data-cmd="InsertRow"  style="border-style: outset;"  src="../../../../../../img/wysiwyg/insertrow.png" width="22" height="22"  />
                <img class="BNCMDTable" data-cmd="InsertColumn"  style="border-style: outset;"  src="../../../../../../img/wysiwyg/insertcol.png" width="22" height="22"  />
                <img class="BNCMDTable" data-cmd="DeleteRow"  style="border-style: outset;"  src="../../../../../../img/wysiwyg/deleterow.png" width="22" height="22"  />
                <img class="BNCMDTable" data-cmd="DeleteColumn"  style="border-style: outset;"  src="../../../../../../img/wysiwyg/deletecol.png" width="22" height="22"  />
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

        </body>
    </html>
    <?php
} else {
    header("location: ../../../../../Auth/Login.php");
    session_destroy();
}



