<?php
session_start();
include_once '../../../../../Class/DB/Config/DB/Config.php';
include_once '../../../../../Class/DB/Config/DB/Software.php';
include_once '../../../../../Class/DB/Com/User/SessionManager.php';
include_once '../../../../../Class/DB/Com/User/Profile.php';
include_once '../../../../../Class/DB/Com/Category/Viewer.php';
include_once '../../../../../Class/DB/Com/Events/Viewer.php';
include_once '../../../../../Class/DB/Com/Module/LoadModule.php';
include_once '../../../../../Class/DB/Com/User/LoadModule.php';
include_once '../../../../../Class/DB/Com/User/Permission.php';
$DBConfig = new Config_DB_Config();
$SC = new Config_DB_Software($DBConfig);
$Sess = new Com_User_SessionManager($DBConfig);
$User = new Com_User_Profile($DBConfig);
$Category = new Com_Category_Viewer($DBConfig);
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
            <script src="../../../../js/dom/TableTools.js"></script>
            <script src="../../../../js/dom/SpanList.js"></script>
            <script>
                var ss = new SSQueryFW();
                ss.DocumentReady(function () {
                    var sd = new SuperDialog();
                    var tabletool = new TableTools();
                    var EKeyword = document.getElementById("EKeyword").appendChild(new SpanList());
                    var FL = document.getElementById("FilesList").appendChild(new FilesList(false));
                    FL.Mutilselect = false;
                    EKeyword.style.zIndex = "1000";
                    tabletool.Import(document.getElementById("TableOutput"));

                    FL.ChDir = function (v) {
                        ss.Post("../../../Api/Ajax/Editor/GetHtmlList.php", {"Location": v}, function (data) {
                            FL.ClearFileList();
                            data = JSON.parse(data);
                            for (var i in data) {
                                FL.AddFile(data[i]["name"], data[i]["fullpath"], "", data[i]["size"], data[i]["modified"], data[i]["type"]);
                            }
                            ss.S("#CHDIRList").Html(decodeURIComponent(v));
                        });
                    };


                    FL.ChDir("/");

                    EKeyword.Input = function (v) {
                        var ref = this;
                        ss.Get("../../../Api/Ajax/Keyword/SearchKeyword.php", {"Keyword": v}, function (data) {
                            data = JSON.parse(data);
                            for (var i = 0; i < data.length; i++) {
                                ref.AddList(data[i]["id"], data[i]["name"]);
                            }
                        });
                    };
                    EKeyword.Enter = function (v) {
                        var ref = this;
                        ss.Get("../../../Api/Ajax/Keyword/InsertKeyword.php", {"Keyword": v}, function (data) {
                            ref.Clear();
                            data = JSON.parse(data);
                            for (var i = 0; i < data.length; i++) {
                                ref.AddList(data[i]["id"], data[i]["name"]);
                            }
                        });
                    };

                    tabletool.addEventListener("click", function (e) {
                        if (e.target.getAttribute("class") == "SelectID") {
                            if (!e.target.checked) {
                                ss.S("#CBoxSelectAll").Val(false);
                            }
                        } else if (e.target.getAttribute("class") == "BNEdit") {
                            ss.Post("../../../Api/Ajax/BlogManager/GetEditBlog.php", {"ID": e.target.getAttribute("data-id")}, function (edata) {

                                edata = JSON.parse(edata);

                                ss.S(".AjaxSendEdit").ValByName(edata)
                                ss.S("#EFilePath").Html(edata["htmlfilepath"]);

                                var kwl = edata["keyword"];
                                EKeyword.Empty();
                                for (var i = 0; i < kwl.length; i++) {
                                    EKeyword.AddSelectList(kwl[i]["id"], kwl[i]["name"]);
                                }

                                sd.Import("#Dialog", function () {
                                    var senddata = ss.S(".AjaxSendEdit").SerializeToJson();
                                    senddata["filepath"] = FL.GetSelectFiles(0);
                                    senddata["id"] = e.target.getAttribute("data-id");
                                    senddata["foreignkey"] = edata.foreign_key;
                                    senddata["keyword"] = EKeyword.GetList();

                                    ss.Post("../../../Api/Ajax/BlogManager/EditBlog.php", senddata, function (d) {
                                        tabletool.DeleteRowAfter(0);
                                        wsl.Param["StartID"] = 0;
                                        wsl.Lock = false;
                                        wsl.LoadData();
                                        ss.S(".AjaxSendEdit").Val("");
                                        ss.S("#EFilePath").Html("");
                                    });
                                }).ZIndex(999).Title("Edit");
                            });

                        } else if (e.target.getAttribute("class") == "fullpathfile") {
                            ss.Post("../../../Api/Ajax/Editor/GetHtmlData.php", {"FullPath": e.target.getAttribute("data-id")}, function (data) {
                                data = JSON.parse(data);
                                sd.Text(data["HTML"], true).Title(data["Name"]).ZIndex(999);
                            });
                        }
                    });

                    var wsl = ss.WindowScrollLoad();
                    wsl.URL = "../../../Api/Ajax/BlogManager/GetBlogList.php";
                    wsl.Param["StartID"] = 0;
                    wsl.Done = (function (data) {
                        data = JSON.parse(data);
                        for (var i = 0; i < data.length; i++) {

                            tabletool.InsertRow();
                            tabletool.InsertCellLastRow('<input type="checkbox" class="SelectID" value="' + data[i]["id"] + '" />');
                            tabletool.InsertCellLastRow(data[i]["title"]);
                            tabletool.InsertCellLastRow(data[i]["categoryid"]);
                            tabletool.InsertCellLastRow('<a href="#" class="fullpathfile" data-id="' + data[i]["htmlfilepath"] + '" >' + data[i]["filename"] + '</a>');

                            tabletool.InsertCellLastRow('<button class="BNEdit" data-id="' + data[i]["id"] + '">Edit</button>');
                            wsl.Param["StartID"] = Math.max(parseInt(data[i]["id"]), wsl.Param["StartID"]);
                        }
                        wsl.Lock = false;
                    });


                    wsl.AddEventListener();
                    wsl.LoadData();
                    ss.S("#BNAdd").Click(function () {
                        ss.S(".AjaxSendEdit").Val("");
                        ss.S("#EFilePath").Html("");
                        EKeyword.Empty();
                        sd.Import("#Dialog", function () {
                            var senddata = ss.S(".AjaxSendEdit").SerializeToJson();
                            senddata["filepath"] = FL.GetSelectFiles(0);
                            senddata["keyword"] = EKeyword.GetList();

                            ss.Post("../../../Api/Ajax/BlogManager/InsertBlog.php", senddata, function (d) {
                                tabletool.DeleteRowAfter(0);
                                wsl.Param["StartID"] = 0;
                                wsl.Lock = false;
                                wsl.LoadData();

                                FL.ChDir("/");

                            });
                        }).ZIndex(999).Title("Add");

                    });
                    ss.S("#BNRemove").Click(function () {
                        sd.Confirm("Do You Delect It", function () {
                            var v = ss.S(".SelectID").Val();
                            ss.Post("../../../Api/Ajax/BlogManager/DeleteBlog.php", {"ID": v}, function () {
                                tabletool.DeleteRowAfter(0);
                                wsl.Param["StartID"] = 0;
                                wsl.Lock = false;
                                wsl.LoadData();
                            });
                        }).ZIndex(999);
                    });
                    ss.S("#CBoxSelectAll").Click(function () {
                        ss.S(".SelectID").Val(this.checked);
                    });

                });
            </script>
        </head>
        <body >

            <div id="Header">
                <div style="width: 50%; ">
                    <a href="../index.php">
                        <img  src="../../../../../File/Resource/Logo.png"/>
                    </a>
                </div>
                <div  style="width: 50%;text-align: right;">
                    <?php
                    $Dat = $User->GetBasicUserData($_SESSION["UserID"]);
                    printf('<img  src="../../../Api/Action/Profile/GetUserIcon.php?id=%s" />', $Dat["userid"]);
                    echo '<span>' . $Dat["alias"] . '</span>';
                    ?>
                    <a href="../Config/Config.php">Config</a>
                    <a href="../../../Session/Action/Logout.php">Logout</a>
                </div>
            </div>
            <div class="Container">
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
                            <li style="font-weight: bold;">Manage</li>
                            <li><a href="View.php">View</a></li>
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
                <div class="Section">
                    <?php
                    if ($Permission->Writable($_SESSION["UserID"])) {
                        echo '<table style="width: 100%;text-align: center;" id="TableOutput">
                        <tr>
                            <th>Select</th>
                            <th>Title</th>
                            <th>Category</th>
                            <th>FileName</th>

                            <th>Edit</th>
                        </tr>
                    </table>';
                    }
                    ?>

                </div>
                <div class="Aside">
                    <?php
                    if ($Permission->Writable($_SESSION["UserID"])) {
                        echo '<div class="BorderBlock">
                        <span class="Title" style="display: block;">Manage</span>
                        <ul>
                            <li style="color: blue;"><input type="checkbox" id="CBoxSelectAll"  /> Select All</li>
                            <li><a id="BNAdd" href="#" style="display: block;">Add</a></li>
                            <li><a href="#" style="display: block;" id="BNRemove">Remove</a></li>
                        </ul>
                    </div>';
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


            <div id="Dialog" style="display: none;">
                <table style="width: 98%;">
                    <tr>
                        <td>Title:</td>
                        <td><input type="text" name="title" class="AjaxSendEdit"  style="width: 99%;"></td>
                        <td>Category:</td>
                        <td>
                            <select class="AjaxSendEdit" name="categoryid"  style="width: 100%;">
                                <option value="">==Select==</option>
                                <option value="0">None</option>
                                <?php
                                foreach ($Category->GetAllCategory() as $value) {
                                    printf('<option value="%s">%s</option>', $value["id"], $value["name"]);
                                }
                                ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>Access:</td>
                        <td>
                            <select class="AjaxSendEdit" name="accessmode"  style="width: 100%;">
                                <?php
                                foreach ($DBConfig->GetAccessMode() as $value) {
                                    printf('<option value="%s">%s</option>', $value["value"], $value["name"]);
                                }
                                ?>
                            </select>
                        </td>
                        <td colspan="2">
                            UserName:<input class="AjaxSendEdit" name="authname"  type="text"   style="width:100%;">

                            Password:<input class="AjaxSendEdit" name="password"  type="text"   style="width:100%;">
                        </td>
                    </tr>
                    <tr>
                        <td>Description:</td>
                        <td>
                            <textarea style="min-width:100%; " class="AjaxSendEdit" name="description" rows="4" cols="20"></textarea>
                        </td>
                        <td>Keyword:</td>
                        <td>
                            <div id="EKeyword" style="width: 400px;min-height:100px; ">

                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>FilePath:</td>
                        <td colspan="3">
                            <div id="EFilePath"></div>
                        </td>
                    </tr>
                </table>

                <div id="FilesList">
                    <div id="CHDIRList"></div>
                </div>
            </div>
        </body>
    </html>
    <?php
} else {
     header("location: ../../../Session/AuthUserID.php");
    session_destroy();
}
