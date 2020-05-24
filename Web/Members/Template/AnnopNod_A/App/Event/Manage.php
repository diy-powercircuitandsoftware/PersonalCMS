<?php
session_start();
include_once '../../../../../../Class/Core/Config/Config.php';
include_once '../../../../../../Class/Core/UI/NAV.php';
include_once '../../../../../../Class/Core/Module/Database.php';
include_once '../../../../../../Class/Com/Event/Database.php';
include_once '../../../../../../Class/SDK/Module/Basic.php';
$config = new Config();
$uinav = new UINAV();
$module = new Module_Database($config);
$hasauth = false;
if (isset($_SESSION["User"])) {
    if ($_SESSION["User"]["session_count"] == 0) {
        include_once '../../../../Class/Core/User/Database.php';
        include_once '../../../../Class/Core/User/Session.php';
        $session = new User_Session(new User_Database($config));
        if ($session->Registered(session_id())) {
            $_SESSION["User"]["session_count"] = 1;
            $hasauth = true;
        }
    } else {
        $_SESSION["User"]["session_count"] = ($_SESSION["User"]["session_count"] + 1) % 12;
        $hasauth = true;
    }
}

if ($config->IsOnline() && $hasauth) {
    $modlist = array();
    foreach ($module->LoadModule() as $value) {
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
            foreach ($modlist as $value) {
                echo $value->Execute(Module_SDK_Basic::Layout_Head);
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
            <header id="mainheader">
                <div style="width: 50%;"></div>
                <div style="width: 50%;text-align: right;">
                    <?php
                    printf('<img src="../../../../Api/Action/Profile/GetUserIcon.php?id=%s"/>', $_SESSION["User"]["id"]);
                    printf('<span style="font-weight: bold;cursor: default;">%s</span>', $_SESSION["User"]["alias"]);
                    ?>       
                    <a style="font-weight: bold;" href="../../../../Auth/Action/Logout.php">Login</a>
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
                    <?php
                    if ($_SESSION["User"]["writable"] == 1) {
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
                <div>
                    <?php
                    if ($_SESSION["User"]["writable"] == 1) {
                        echo ' <div class="BorderBlock">
                            <div class="TitleCenter">Manage</div>
                             <span style="display: block;"><input type="checkbox" id="CBoxSelectAll"  /> Select All</span>
                              <a id="BNAddEvent" class="MenuLink" href="#">Add</a>
                               <a id="BNRemoveEvent" class="MenuLink" href="#">Remove</a>
                             
                        </div>
                        <div class="BorderBlock" style="margin-top: 1px;">
                            <div class="TitleCenter">Place</div>
                               <a id="BNAddPlace" class="MenuLink" href="#">Add</a>
                                  <a id="BNEditPlace" class="MenuLink" href="#">Edit</a>
                             
                        </div>';
                    }
                    ?> 
                </div>
            </div>
            <div class="Container">

                <div class="Section">

                    <?php
                    ?>
                </div>
                <div class="Aside">

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
    header("location: ../../../../Auth/Login.php");
    session_destroy();
}
