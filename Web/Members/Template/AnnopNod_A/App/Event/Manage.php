<?php
session_start();
include_once '../../../../../../Class/Core/Config/Config.php';
include_once '../../../../../../Class/Core/UI/NAV.php';
include_once '../../../../../../Class/Core/Module/Database.php';
include_once '../../../../../../Class/Com/Event/Database.php';
include_once '../../../../../../Class/Com/Event/Manager.php';
include_once '../../../../../../Class/Com/Event/Reader.php';
include_once '../../../../../../Class/Com/Category/Database.php';
include_once '../../../../../../Class/SDK/Module/Basic.php';
include_once '../../../../Auth/Action/VerifySession.php';
$config = new Config();
$uinav = new UINAV();
$module = new Module_Database($config);
$category = new Category_Database($config);
$eventdb=new Event_Database($config);
$event = new Event_Manager($eventdb);
$eventreader=new Event_Reader($eventdb);
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
            <script src="../../../../../js/io/Ajax.js"></script>
            <script src="../../../../../js/dom/SSQueryFW.js"></script>
            <script src="../../../../../js/dom/SuperDialog.js"></script>          
            <script src="../../../../../js/dom/TableTools.js"></script>
            <script>
                var ss = new SSQueryFW();
                ss.DocumentReady(function () {
                    var sd = new SuperDialog();
                    var tabletool = new TableTools();
                    var ajax = new Ajax();
                        var lastid=0;
                     var ajaxsb = new AjaxScrollBar("../../../../Api/Ajax/Event/GetEvent.php", {"id": 0});
                    tabletool.Import(document.getElementById("TableOutput"));
                    ajaxsb.AddScrollEvent(function (data) {
                        try {
                            data = JSON.parse(data);
                            for (var i in data) {
                                tabletool.InsertRow();
                                tabletool.InsertCellLastRow('<div style="text-align: center;"><input type="checkbox" class="UserSelect" value="' + data[i]["id"] + '" /></div>');
                                tabletool.InsertCellLastRow(data[i]["name"]);
                                tabletool.InsertCellLastRow(data[i]["startdate"]);
                                tabletool.InsertCellLastRow(data[i]["stopdate"]);
                                tabletool.InsertCellLastRow(data[i]["description"]);
                                tabletool.InsertCellLastRow('<button class="BNEdit" data-value="' + data[i]["id"] + '">Edit</button>');
                                lastid = Math.max(lastid, data[i]["id"]);
                            }
                            ajaxsb.Param("id", lastid);
                        } catch (e) {
                            sd.Alert(data);
                        }
                    });
                    
                    
                    tabletool.AddEventListener("click", function (e) {
                        if (e.target.getAttribute("class") == "SelectID") {
                            if (!e.target.checked) {
                                ss.S("#CBoxSelectAll").Val(false);
                            }
                        } else if (e.target.getAttribute("class") == "BNEdit") {
                            ajax.Post("../../../../Api/Ajax/Event/GetEventForEdit.php", {"id": e.target.getAttribute("data-value")}, function (data) {
                                ss.S(".EventAjaxSend").ValByName(JSON.parse(data));
                                sd.Import("Edit","#EventDialog", function () {
                                    var json = ss.S(".EventAjaxSend").SerializeToJson();
                                    json["id"] = e.target.getAttribute("data-id");
                                    ss.Post("../../../Api/Ajax/EventManager/UpdateEventList.php", json, function () {
                                        location.reload();
                                    });
                                }).ZIndex(999).Title("Edit");
                            });
                        }
                    });
                    

                    ss.S("#BNAddEvent").Click(function () {
                        ss.S(".EventAjaxSend").Val("");
                        ss.GeoLocation(function (v) {
                            ss.S("#txtlatitude").Val(v.latitude);
                            ss.S("#txtlongitude").Val(v.longitude);
                        });
                        ss.S("#txtstartday,#txtstopday").Val(new Date());

                        var i = sd.Import("Add", "#EventDialog", {"OK": function () {
                                ajax.Post("../../../../Api/Ajax/Event/AddEvent.php", ss.S(".EventAjaxSend").ValByName(), function (data) {
                                    if (data=="1"){
                                         location.reload();
                                    }
                                   
                                });

                            }, "Cancel": function () {
                                i.Close();
                            }}).ZIndex(999);
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
                             
                        </div>';
                    }
                    ?> 
                    <div class="BorderBlock" style="margin-top: 1px;">
                        <div class="TitleCenter">Event</div>
                        <?php
                        foreach ($eventreader->GetComingEvent(Event_Database::Access_Member) as $value) {
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

            <table id="EventDialog" style="display: none;width: 100%;box-sizing: border-box;">
                <tr>
                    <td>Name:</td>
                    <td><input class="EventAjaxSend" type="text" name="name" value="" /></td>
                </tr>
                <tr>
                    <td>Access:</td>
                    <td>
                        <input type="checkbox" style="width: auto;" class="EventAjaxSend" name="public" value="1" />
                        <label>public</label>
                    </td>
                </tr>

                <tr>
                    <td>Html Code:</td>
                    <td><textarea class="EventAjaxSend" name="htmlcode"></textarea></td>
                </tr>
                <tr>
                    <td>Category:</td>
                    <td>
                        <select class="EventAjaxSend" name="category">
                            <option value="0">-</option>
                            <?php
                            foreach ($category->GetAllCategory() as $value) {
                                printf('<option value="%s">%s</option>', $value["id"], $value["name"]);
                            }
                            ?>
                        </select>
                    </td>
                </tr>
                <tr> 
                    <td>Latitude:</td>
                    <td><input id="txtlatitude" class="EventAjaxSend" type="text" name="latitude" value="" /></td>
                </tr>
                <tr>
                    <td>Longitude:</td>
                    <td><input  id="txtlongitude"  class="EventAjaxSend" type="text" name="longitude" value="" /></td>
                </tr>
                <tr>
                    <td>Start Date:</td>
                    <td><input id="txtstartday" class="EventAjaxSend" type="date" name="startdate" value="" /></td>
                </tr>
                <tr>
                    <td>Stop Date:</td>
                    <td><input id="txtstopday" class="EventAjaxSend" type="date" name="stopdate" value="" /></td>
                </tr>
                <tr>
                    <td>Description:</td>
                    <td><textarea class="EventAjaxSend" name="description" ></textarea></td>
                </tr>
            </table>
             
        </body>
    </html>
    <?php
} else {
    header("location: ../../../../Auth/Login.php");
    session_destroy();
}
