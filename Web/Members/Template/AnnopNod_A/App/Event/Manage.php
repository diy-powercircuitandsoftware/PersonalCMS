<?php
session_start();
include_once '../../../../../Class/DB/Config/DB/Config.php';
include_once '../../../../../Class/DB/Config/DB/Software.php';
include_once '../../../../../Class/DB/Config/Api/Place.php';
include_once '../../../../../Class/DB/Com/User/SessionManager.php';
include_once '../../../../../Class/DB/Com/User/Profile.php';
include_once '../../../../../Class/DB/Com/Module/LoadModule.php';
include_once '../../../../../Class/DB/Com/User/LoadModule.php';
include_once '../../../../../Class/DB/Com/Events/Viewer.php';
include_once '../../../../../Class/DB/Com/Category/Viewer.php';
include_once '../../../../../Class/DB/Com/User/Permission.php';
$DBConfig = new Config_DB_Config();
$SC = new Config_DB_Software($DBConfig);
$PConfig = new Config_Api_Place($DBConfig);
$Sess = new Com_User_SessionManager($DBConfig);
$User = new Com_User_Profile($DBConfig);
$Module = new Com_Module_LoadModule($DBConfig);
$UModule = new Com_User_LoadModule($DBConfig);
$Event = new Com_Events_Viewer($DBConfig);
$Category = new Com_Category_Viewer($DBConfig);
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
            <style>
                .EventAjaxSend{
                    width: 100%;
                    box-sizing: border-box;
                }
                .PlaceAjaxSend{
                    width: 100%;
                    box-sizing: border-box;
                }
            </style>
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
            <script>
                var ss = new SSQueryFW();
                ss.DocumentReady(function () {
                    var sd = new SuperDialog();
                    var tabletool = new TableTools();
                    tabletool.Import(document.getElementById("TableOutput"));
                    var wsl = ss.WindowScrollLoad();
                    wsl.URL = "../../../Api/Ajax/EventManager/GetEventList.php";
                    wsl.Param["StartID"] = 0;
                    wsl.Done = (function (data) {
                        data = JSON.parse(data);
                        for (var i = 0; i < data.length; i++) {
                            tabletool.InsertRow();
                            tabletool.InsertCellLastRow('<input type="checkbox" class="SelectID" value="' + data[i]["id"] + '" />');
                            tabletool.InsertCellLastRow(data[i]["name"]);
                            tabletool.InsertCellLastRow(data[i]["startdate"]);
                            tabletool.InsertCellLastRow(data[i]["stopdate"]);

                            tabletool.InsertCellLastRow(data[i]["description"]);
                            tabletool.InsertCellLastRow('<button class="BNEdit" data-id="' + data[i]["id"] + '">Edit</button>');
                            wsl.Param["StartID"] = Math.max(parseInt(data[i]["id"]), wsl.Param["StartID"]);
                        }
                        wsl.Lock = false;
                    });
                    tabletool.addEventListener("click", function (e) {
                        if (e.target.getAttribute("class") == "SelectID") {
                            if (!e.target.checked) {
                                ss.S("#CBoxSelectAll").Val(false);
                            }
                        } else if (e.target.getAttribute("class") == "BNEdit") {
                            ss.Post("../../../Api/Ajax/EventManager/GetEventForEdit.php", {"ID": e.target.getAttribute("data-id")}, function (data) {
                                ss.S(".EventAjaxSend").ValByName(JSON.parse(data));
                                sd.Import("#EventDialog", function () {
                                    var json = ss.S(".EventAjaxSend").SerializeToJson();
                                    json["id"] = e.target.getAttribute("data-id");
                                    ss.Post("../../../Api/Ajax/EventManager/UpdateEventList.php", json, function () {
                                        location.reload();
                                    });
                                }).ZIndex(999).Title("Edit");
                            });
                        }
                    });
                    wsl.AddEventListener();
                    wsl.LoadData();

                    ss.S("#BNAddEvent").Click(function () {
                        ss.S(".EventAjaxSend").Val("");
                        sd.Import("#EventDialog", function () {
                            ss.Post("../../../Api/Ajax/EventManager/InsertEventList.php", ss.S(".EventAjaxSend").SerializeToJson(), function () {
                                location.reload();
                            });

                        }).ZIndex(999).Title("Add");
                    });
                    ss.S("#BNAddPlace").Click(function () {
                        ss.S(".PlaceAjaxSend").Val("");
                        ss.S("#BNPlaceDelete,#TRSelectPlace").Hide();
                        sd.Import("#PlaceDialog", function () {
                            ss.Post("../../../Api/Ajax/EventManager/AddPlace.php", ss.S(".PlaceAjaxSend").SerializeToJson(), function () {

                            });
                        }).ZIndex(999).Title("Add");
                    });
                    ss.S("#BNEditPlace").Click(function () {
                        ss.Post("../../../Api/Ajax/EventManager/GetAllPlaceList.php", "", function (data) {
                            ss.S("#BNPlaceDelete,#TRSelectPlace").Show();
                            data = JSON.parse(data);
                            ss.S("#OPTSelectPlace").Empty();
                            for (var i = 0; i < data.length; i++) {
                                ss.S("#OPTSelectPlace").Append("<option></option>").Val(data[i]["id"]).Html(data[i]["name"]);
                            }
                            ss.S("#OPTSelectPlace").Change();
                            sd.Import("#PlaceDialog", function () {
                                var json = ss.S(".PlaceAjaxSend").SerializeToJson();
                                json["id"] = ss.S("#OPTSelectPlace").Val();
                                ss.Post("../../../Api/Ajax/EventManager/EditPlace.php", json, function () {

                                });
                            }).ZIndex(999).Title("Edit");
                        });
                    });
                    ss.S("#BNPlaceDelete").Click(function () {
                        sd.Confirm("Do You Delect It", function () {
                            var v = ss.S("#OPTSelectPlace").Val();
                            ss.Post("../../../Api/Ajax/EventManager/DeletePlace.php", {"ID": v}, function () {
                                ss.S(".PlaceAjaxSend").Val("");
                                ss.Post("../../../Api/Ajax/EventManager/GetAllPlaceList.php", "", function (data) {
                                    ss.S("#OPTSelectPlace").Empty();
                                    data = JSON.parse(data);
                                    for (var i = 0; i < data.length; i++) {
                                        ss.S("#OPTSelectPlace").Append("<option></option>").Val(data[i]["id"]).Html(data[i]["name"]);
                                    }
                                    ss.S("#OPTSelectPlace").Change();
                                });
                            });
                        }).ZIndex(999);
                    });

                    ss.S("#BNRemoveEvent").Click(function () {
                        sd.Confirm("Do You Delect It", function () {
                            var v = ss.S(".SelectID").Val();
                            ss.Post("../../../Api/Ajax/EventManager/DeleteEvent.php", {"ID": v}, function () {
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
                    ss.S("#OPTSelectPlace").Change(function () {
                        ss.Post("../../../Api/Ajax/EventManager/GetPlaceForEdit.php", {"ID": this.value}, function (data) {
                            ss.S(".PlaceAjaxSend").ValByName(JSON.parse(data));

                        });
                    });

                });
            </script>
        </head>
        <body >

            <div id="Header">
                <div style="width: 50%;">
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
                            <li><a href="../Blog/Manage.php">Manage</a></li>
                            <li><a href="../Blog/View.php">View</a></li>
                        </ul>
                    </div>

                    <div class="BorderBlock" style="margin-top: 1px;">
                        <span class="Title" style="display: block;">Event</span>
                        <ul>
                            <li style="font-weight: bold;">Manage</li>
                            <li><a href="View.php">View</a></li>
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
                        echo ' <table style="width: 100%;text-align: center;word-wrap: break-word;" id="TableOutput">
                            <tr>
                                <th>Select</th>
                                <th>Name</th>
                                <th>Start</th>
                                <th>Stop</th>

                                <th>Description</th>
                                <th>Edit</th>
                            </tr>
                        </table>';
                    }
                    ?>
                </div>
                <div class="Aside">
                    <?php
                    if ($Permission->Writable($_SESSION["UserID"])) {
                        echo ' <div class="BorderBlock">
                            <span class="Title" style="display: block;">Manage</span>
                            <ul>
                                <li style="color: blue;"><input type="checkbox" id="CBoxSelectAll"  /> Select All</li>
                                <li><a id="BNAddEvent" href="#" style="display: block;">Add</a></li>
                                <li><a href="#" style="display: block;" id="BNRemoveEvent">Remove</a></li>
                            </ul>
                        </div>
                        <div class="BorderBlock" style="margin-top: 1px;">
                            <span class="Title" style="display: block;">Place</span>
                            <ul>
                                <li><a id="BNAddPlace" href="#" style="display: block;">Add</a></li>
                                <li><a id="BNEditPlace" href="#" style="display: block;" >Edit</a></li>
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
            <table id="EventDialog" style="display: none;width: 100%;box-sizing: border-box;">
                <tr>
                    <td>Name:</td>
                    <td><input class="EventAjaxSend" type="text" name="name" value="" /></td>
                </tr>
                <tr>
                    <td>Access:</td>
                    <td>
                        <select class="EventAjaxSend" name="accessmode">
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
                    <td><input class="EventAjaxSend" type="text" name="authname" value="" /></td>
                </tr>
                <tr>
                    <td>Password:</td>
                    <td><input class="EventAjaxSend" type="text" name="password" value="" /></td>
                </tr>
                <tr>
                    <td>Html Code:</td>
                    <td><textarea class="EventAjaxSend" name="htmlcode"></textarea></td>
                </tr>
                <tr>
                    <td>Category:</td>
                    <td>
                        <select class="EventAjaxSend" name="categoryid">
                            <option value="0">-</option>
                            <?php
                            foreach ($Category->GetAllCategory() as $value) {
                                printf('<option value="%s">%s</option>', $value["id"], $value["name"]);
                            }
                            ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>Place:</td>
                    <td><select class="EventAjaxSend" name="placeid">
                            <option value="0">-</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>Start Date:</td>
                    <td><input class="EventAjaxSend" type="date" name="startdate" value="" /></td>
                </tr>
                <tr>
                    <td>Stop Date:</td>
                    <td><input class="EventAjaxSend" type="date" name="stopdate" value="" /></td>
                </tr>
                <tr>
                    <td>Description:</td>
                    <td><textarea class="EventAjaxSend" name="description" ></textarea></td>
                </tr>
            </table>
            <table id="PlaceDialog" style="display: none;width: 100%;box-sizing: border-box;">
                <tr id="TRSelectPlace">
                    <td>Select:</td>
                    <td>
                        <select id="OPTSelectPlace" style="width: 100%;box-sizing: border-box;">
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>Name:</td>
                    <td><input class="PlaceAjaxSend" type="text" name="name" value="" /></td>
                </tr>
                <tr>
                    <td>Latitude:</td>
                    <td><input class="PlaceAjaxSend" type="text" name="latitude" value="" /></td>
                </tr>
                <tr>
                    <td>Longitude:</td>
                    <td><input class="PlaceAjaxSend" type="text" name="longitude" value="" /></td>
                </tr>
                <tr>
                    <td>Api:</td>
                    <td>
                        <select class="PlaceAjaxSend" name="config_api_id">
                            <option value="0">none</option>
                            <?php
                            foreach ($PConfig->GetApiPlaceAll() as $value) {
                                printf('<option value="%s">%s</option>', $value["id"], $value["name"]);
                            }
                            ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td colspan="2"><button id="BNPlaceDelete" style="width: 100%;box-sizing: border-box;" >Delete</button></td>
                </tr>
            </table>
        </body>
    </html>
    <?php
} else {
     header("location: ../../../Session/AuthUserID.php");
    session_destroy();
}
