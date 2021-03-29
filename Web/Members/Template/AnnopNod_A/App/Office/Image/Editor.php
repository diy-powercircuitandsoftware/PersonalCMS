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
            <title>Open Image</title>
            <link rel="stylesheet" type="text/css" href="../../../../../../css/HolyGrail.css">
            <link rel="stylesheet" type="text/css" href="../../../../../../css/PersonalCMS.css">
            
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
            foreach ($modlist as $value) {
                echo $value->Execute(Module_SDK_Basic::Layout_Head);
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
                    ss.S(".BNToolBoxTab").Tabs(".ToolBoxTab","data-id");

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
        
        <body  class="HolyGrail">

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
                            <img  id="BNOpen"    style="border-style: outset;"  src="../img/io/open.gif" width="22" height="22"  />
                            <img  id="BNSave"    style="border-style: outset;"  src="../img/io/save.gif" width="22" height="22"  />
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
                </main>
                <aside>
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
           
        </body>
    </html>
     <?php
} else {
    header("location: ../../../../../Auth/Login.php");
    session_destroy();
}
