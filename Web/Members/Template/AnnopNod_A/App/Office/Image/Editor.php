<?php
session_start();
include_once '../../../../../../Class/DB/Config/DB/Config.php';
include_once '../../../../../../Class/DB/Config/DB/Software.php';
include_once '../../../../../../Class/DB/Com/User/SessionManager.php';
include_once '../../../../../../Class/DB/Com/User/Profile.php';
include_once '../../../../../../Class/DB/Com/Events/Viewer.php';
include_once '../../../../../../Class/DB/Com/Module/LoadModule.php';
include_once '../../../../../../Class/DB/Com/User/LoadModule.php';
$DBConfig = new Config_DB_Config();
$SC = new Config_DB_Software($DBConfig);
$Sess = new Com_User_SessionManager($DBConfig);
$User = new Com_User_Profile($DBConfig);
$Event = new Com_Events_Viewer($DBConfig);
$Module = new Com_Module_LoadModule($DBConfig);
$UModule = new Com_User_LoadModule($DBConfig);
$DBConfig->Open();
if ($SC->Online() && isset($_SESSION["UserID"]) && $Sess->Registered(session_id())) {
    ?>
    <!DOCTYPE html>
    <html>
        <head>
            <meta charset="UTF-8">
            <title>Untitled Document</title>
            <link rel="stylesheet" href="../../css/Page.css">
            <style>
                .ToolBoxTab{
                    margin-top: 1px;
                    background-color: burlywood;
                    border-style: solid;
                    border-width: thin;
                    display: none;
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

            <script src="../../../../../js/dom/SuperDialog.js"></script>
            <script src="../../../../../js/dom/SSQueryFW.js"></script>
            <script src="../../../../../js/file/FilesList.js"></script>
            <script src="../../../../../js/imageedit/Pencil.js"></script>
            <script src="../../../../../js/imageedit/SelectArea.js"></script>
            <script src="../../../../../js/imageedit/Transaction.js"></script>

            <script>
                var ss = new SSQueryFW();
                ss.DocumentReady(function () {
                    var sd = new SuperDialog();
                    var canvas = document.getElementById("canvas");
                    var ctx = canvas.getContext("2d");
                    canvas.ref = null;
                    var image = new Image();
                    var imagetrans = new ImageTransaction();
                    var selectarea = new SelectArea(canvas);
                    ss.S(image).Url("../../../../Api/Action/Files/DownloadFile.php", {"id": btoa((ss.URLParam()["path"])), "option": "opendisable206"});
                    image.onload = function () {
                        ctx.canvas.width = this.width;
                        ctx.canvas.height = this.height;
                        ctx.drawImage(this, 0, 0);
                        imagetrans.AddTransaction("loadimage", this);
                    };
                    canvas.GetMousePosition = function (evt) {
                        var rect = this.getBoundingClientRect();
                        var rw = rect.width / this.width;
                        var rh = rect.height / this.height;
                        return {
                            x: (evt.clientX - rect.left) / rw,
                            y: (evt.clientY - rect.top) / rh
                        };
                    };

                    //RectSelect

                    ss.S("#BNOpen").Click(function () {
                        var SaveBeforeExit = sd.SaveBeforeExit("Do You Save Before Open Document").ZIndex(999).Title("New Document");
                        SaveBeforeExit.OnDiscard = function () {
                            window.onbeforeunload = null;
                            window.location.replace("MainPage.php");

                        };
                        /*SaveBeforeExit.OnSave = function () {
                         ss.Post("../../../../Api/Ajax/Image/SaveImage.php", {"FullPath": ss.URLParam()["path"], "Data": dat}, function (data) {
                         if (data == "1") {
                         window.onbeforeunload = null;
                         window.location.replace("MainPage.php");
                         } else {
                         
                         }
                         });
                         };*/
                    });
                    ss.S(".BNDrawCMD").Click(function () {

                        if (this.getAttribute("data-cmd") == "pencil") {
                            canvas.ref = new Pencil();
                            canvas.ref.Color = document.getElementById("FGColor").value;
                        } else if (this.getAttribute("data-cmd") == "rectselect") {
                            canvas.ref = selectarea;
                            canvas.ref.Save();
                        }

                    });
                    ss.S(".BNToolBoxTab").Click(function () {
                        var id = this.getAttribute("data-id");
                        ss.S(".ToolBoxTab").Hide();
                        ss.S(".ToolBoxTab[data-id='" + id + "']").Show();
                    });

                    ss.S("#OptSelectZoomSize").Change(function () {
                        ss.S("#canvas").CSS("width", this.value + "%");
                    });

                    canvas.addEventListener("mousedown", function (e) {
                        if (this.ref != null) {
                            this.ref.MouseDown(this.GetMousePosition(e));
                        }
                    });
                    canvas.addEventListener("mousemove", function (e) {
                        if (this.ref != null) {
                            this.ref.MouseMove(this.GetMousePosition(e));
                        }
                    });
                    canvas.addEventListener("mouseup", function (e) {
                        if (this.ref != null) {
                            this.ref.MouseUp(this.GetMousePosition(e));
                        }
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
                            <li style="font-weight: bold;">Image</li>
                            <li><a href="../PointPoint/MainPage.php">PointPoint</a></li>
                            <li><a href="../Statistics/MainPage.php">Statistics</a></li>
                            <li><a href="../WordWord/MainPage.php">WordWord</a></li>
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
                        <a class="BNToolBoxTab" data-id="Draw" href="#">Draw</a>
                        <a class="BNToolBoxTab" data-id="Fitter" href="#">Fitter</a>
                        <a class="BNToolBoxTab" data-id="Select" href="#">Select</a>
                        <a class="BNToolBoxTab" data-id="Zoom" href="#">Size</a>
                    </div>
                    <div>
                        <div class="ToolBoxTab" data-id="Basic" style="display: block;" >
                            <img  id="BNOpen"    style="border-style: outset;"  src="../img/wysiwyg/open.gif" width="22" height="22"  />
                            <img  id="BNSave"    style="border-style: outset;"  src="../img/wysiwyg/save.gif" width="22" height="22"  />
                        </div>
                        <div class="ToolBoxTab" data-id="Color" style="display: none;" >
                            <span>foreground:</span>    <input id="FGColor" type="color"   value="#000000">
                            <span>background:</span>    <input id="BGColor"type="color"   value="#FFFFFF">
                        </div>
                        <div class="ToolBoxTab" data-id="Draw" style="display: none;" >
                            <img  class="BNDrawCMD" data-cmd="pencil" style="border-style: outset;"  src="../img/image/pencil.png" width="22" height="22"  />
                        </div>
                        <div class="ToolBoxTab" data-id="Select" style="display: none;" >
                            <img  class="BNDrawCMD" data-cmd="rectselect" style="border-style: outset;"  src="../img/image/rectselect.png" width="22" height="22"  />
                        </div>
                        <div class="ToolBoxTab" data-id="Zoom" style="display: none;" >
                            <span>Zoom Size:</span>
                            <select id="OptSelectZoomSize">
                                <option value="20">20%</option>
                                <option value="50">50%</option>
                                <option value="75">75%</option>
                                <option selected="selected" value="100">100%</option>
                            </select>
                        </div>
                    </div>

                    <div id="EditorArea" style="overflow: auto;position: relative;"> 
                        <canvas id="canvas" style="border-style: solid;">

                        </canvas>

                    </div>


                </div>
                <div class="Aside"  >

                    <div class="BorderBlock" style="margin-top: 1px;">
                        <label class="Title">My Event</label>
                        <?php
                        foreach ($Event->GetCurrentMyEvent($_SESSION["UserID"]) as $value) {
                            echo '<div  >';
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
                            echo '<div  >';
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

        </body>
    </html>
    <?php
} else {
   header("location: ../../../../../Auth/Login.php");
    session_destroy();
}
