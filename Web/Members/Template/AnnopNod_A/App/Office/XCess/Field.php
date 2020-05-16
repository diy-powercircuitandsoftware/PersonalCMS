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
            <title><?php echo urldecode($_GET["path"]); ?></title>
            <link rel="stylesheet" href="../../css/Page.css">
            <style>
                .SQLTab{
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

            <script src="../../../../../js/dom/SSQueryFW.js"></script>
            <script src="../../../../../js/dom/SuperDialog.js"></script>
            <script src="../../../../../js/dom/TableTools.js"></script>
            <script>
                var ss = new SSQueryFW();
                ss.DocumentReady(function () {
                    //case "column_view":
                    var sd = new SuperDialog();
                    var FieldNew = new TableTools().Import(document.getElementById("FieldNew"));
                    var FieldList = new TableTools().Import(document.getElementById("FieldList"));
                    var datatypes = ["INTEGER", "REAL", "TEXT", "BLOB", "NUMERIC", "BOOLEAN", "DATETIME"];

                    ss.Post("../../../../Api/Ajax/SQLite/GetTable.php", {"path": ss.URLParam()["path"]}, function (data) {
                        data = JSON.parse(data);
                        for (var i in data) {
                            ss.S("#SelectTableField").Append("<option></option>").Val(data[i]).Text(data[i]);
                        }
                        ss.S("#SelectTableField").Change();
                    });

                    for (var i = 0; i < datatypes.length; i++) {
                        var opt = document.getElementById("SelectPKType").appendChild(document.createElement("OPTION"));
                        opt.value = datatypes[i];
                        opt.innerHTML = datatypes[i];
                    }

                    ss.S("#BNAddRow").Click(function () {

                        for (var a = 0; a < parseInt(ss.S("#InputCountRow").Val()); a++)
                        {

                            FieldNew.InsertRow();
                            FieldNew.InsertCellLastRow('<input class="AjaxNewTB" type="text" name="name" value="" />');
                            var selecttype = FieldNew.InsertCellLastRow(document.createElement("SELECT"));
                            FieldNew.InsertCellLastRow('<input min="0" class="AjaxNewTB" type="number" name="length"   />');
                            FieldNew.InsertCellLastRow('<input class="AjaxNewTB" type="checkbox" name="notnull" value="1" />');
                            FieldNew.InsertCellLastRow('<input list="DefaultTypes" class="AjaxNewTB" type="text" name="default" value="" />');
                            selecttype.setAttribute("name", "type");
                            selecttype.setAttribute("class", "AjaxNewTB");
                            for (var i = 0; i < datatypes.length; i++) {
                                var opt = selecttype.appendChild(document.createElement("OPTION"));
                                opt.value = datatypes[i];
                                opt.innerHTML = datatypes[i];
                            }
                        }

                    });
                    ss.S("#BNAddField").Click(function () {

                        for (var a = 0; a < parseInt(ss.S("#InputCountAlterField").Val()); a++)
                        {

                            FieldList.InsertRow();
                            FieldList.InsertCellLastRow("");
                            FieldList.InsertCellLastRow('<input class="AjaxAlterNewField" type="text" name="name" value="" />');
                            var selecttype = FieldList.InsertCellLastRow(document.createElement("SELECT"));
                            FieldList.InsertCellLastRow('<input min="0" class="AjaxAlterNewField" type="number" name="length"   />');
                            FieldList.InsertCellLastRow('<input class="AjaxAlterNewField" type="checkbox" name="notnull" value="1" />');
                            FieldList.InsertCellLastRow('<input list="DefaultTypes" class="AjaxAlterNewField" type="text" name="default" value="" />');
                            FieldList.InsertCellLastRow("");
                            selecttype.setAttribute("name", "type");
                            selecttype.setAttribute("class", "AjaxAlterNewField");
                            for (var i = 0; i < datatypes.length; i++) {
                                var opt = selecttype.appendChild(document.createElement("OPTION"));
                                opt.value = datatypes[i];
                                opt.innerHTML = datatypes[i];
                            }
                        }

                    });
                    ss.S("#BNCreateTable").Click(function () {
                        ss.S("#UICreateTable").Show();
                        ss.S("#UIEditTable").Hide();
                    });
                    ss.S("#BNDropField").Click(function () {
                        var sf = ss.S(".SelectField").Val();
                        alert(sf);
                    });


                    ss.S("#BNEditTable").Click(function () {
                        ss.S("#UIEditTable").Show();
                        ss.S("#UICreateTable").Hide();
                    });

                    ss.S("#BNGoNewTable").Click(function () {
                        var json = ss.S(".AjaxNewPK").SerializeToJson();
                        json.Field = [];
                        json.path = ss.URLParam()["path"];
                        ss.ForEach(FieldNew.GetRow(), function (d) {
                            var t = ss.S(d).Q(".AjaxNewTB").SerializeToJson();
                            json.Field.push(t);
                        });

                        ss.Post("../../../../Api/Ajax/SQLite/CreateTable.php", json, function (data) {
                            if (data == "1") {
                                location.reload();
                            } else {
                                sd.Alert(data).Title("Error").ZIndex(999);
                            }

                        });
                    });
                    ss.S("#BNGoAlterField").Click(function () {
                        var json = {};
                        json.Field = [];
                        json.path = ss.URLParam()["path"];
                        json.TableName = ss.S("#SelectTableField").Val();
                        ss.ForEach(FieldList.GetRow(), function (d) {
                            var t = ss.S(d).Q(".AjaxAlterNewField").SerializeToJson();
                            json.Field.push(t);
                        });

                        ss.Post("../../../../Api/Ajax/SQLite/AddNewField.php", json, function (data) {
                            if (data == "1") {
                                ss.S("#SelectTableField").Change();
                            } else {
                                sd.Alert(data).Title("Error").ZIndex(999);
                            }

                        });
                    });


                    ss.S("#SelectTableField").Change(function () {
                        ss.Post("../../../../Api/Ajax/SQLite/GetFieldByTable.php", {"path": ss.URLParam()["path"], "name": this.value}, function (data) {
                            FieldList.DeleteRowAfter(0);
                            data = JSON.parse(data)
                            for (var i in data) {
                                var t = data[i]["type"] || "";
                                var len = t.replace(/[^0-9]/g, '');
                                FieldList.InsertRow();
                                FieldList.InsertCellLastRow('<input type="checkbox" class="SelectField" value="' + data[i]["name"] + '" />');
                                if (data[i]["pk"]) {
                                    FieldList.InsertCellLastRow(data[i]["name"] + "(PK)");
                                } else {
                                    FieldList.InsertCellLastRow(data[i]["name"]);
                                }
                                FieldList.InsertCellLastRow(t);
                                FieldList.InsertCellLastRow(len);

                                FieldList.InsertCellLastRow(data[i]["notnull"].toString());
                                FieldList.InsertCellLastRow(data[i]["dflt_value"]);
                                FieldList.InsertCellLastRow('<button class="BNEditField" data-name="' + data[i]["name"] + '">Edit</button>');
                            }
                        });
                    });
                    FieldList.addEventListener("click", function (e) {
                        if (e.target.getAttribute("class") == "BNEditField") {
                            var tl = sd.TableLayout(function () {

                            }).ZIndex(999).Title("Edit Field");
                            tl.name = tl.AddTableDom('Name:', '<input style="display: block;box-sizing: border-box;width: 100%;" type="text" />');
                            tl.type = tl.AddTableDom('Type:', '<select style="display: block;box-sizing: border-box;width: 100%;" ></select>');
                            tl.length = tl.AddTableDom('Length:', '<input style="display: block;box-sizing: border-box;width: 100%;" type="number" min="0" value="0" />');
                            tl.default = tl.AddTableDom('Default:', '<input list="DefaultTypes" style="display: block;box-sizing: border-box;width: 100%;" type="text" />');
                            for (var i = 0; i < datatypes.length; i++) {
                                var opt = tl.type.appendChild(document.createElement("OPTION"));
                                opt.value = datatypes[i];
                                opt.innerHTML = datatypes[i];
                            }
                            tl.name.value = e.target.getAttribute("data-name");
                        }
                    });

                });
            </script>
        </head>
        <body >

            <div id="Header" style="position: absolute;width: 100%;" >
                <div style="width: 50%;">
                    <a href="../../index.php">
                        <img  src="../../../../../../File/Resource/Logo.png"/>
                    </a>
                </div>
                <div  style="width: 50%;text-align: right;">
                    <a href="../../index.php">MainPage</a>
                    <?php
                    $Dat = $User->GetBasicUserData($_SESSION["UserID"]);
                    printf('<img  src="../../../../Api/Action/Profile/GetUserIcon.php?id=%s" />', $Dat["userid"]);
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
                            <li><a href="../WordWord/MainPage.php">WordWord</a></li>
                            <li><a href="../WYSIWYG/NewDoc.php">WYSIWYG</a></li>
                            <li><a href="../XCell/MainPage.php">XCell</a></li>
                            <li style="font-weight: bold;">XCess</li>
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
                    <div>
                        <a id="BNCreateTable"   href="#">Create Table</a>
                        <div id="UICreateTable" style="display: none;">
                            <div>
                                <label>Table Name:</label> <input type="text" class="AjaxNewPK" name="TableName" value="" />
                            </div>
                            <label style="font-weight: bold;">Primary Key:</label>
                            <table border="1" style="text-align: center;">
                                <tr>
                                    <th>Name</th>
                                    <th>Type</th>
                                    <th>Length</th>
                                    <th>AutoIncrement</th>
                                </tr>
                                <tr>
                                    <td><input type="text" class="AjaxNewPK" name="PKName" value="" /></td>
                                    <td>
                                        <select id="SelectPKType" class="AjaxNewPK" name="PKType">
                                        </select>
                                    </td>
                                    <td><input type="number" class="AjaxNewPK" name="PKLength" value="" min="0" /></td>
                                    <td><input type="checkbox" class="AjaxNewPK" name="PKAL" value="1" /></td>
                                </tr>
                            </table>
                            <label style="font-weight: bold;">Field:</label>
                            <table id="FieldNew" border="1" style="text-align: center;">
                                <tr>
                                    <th>Name</th>
                                    <th>Type</th>
                                    <th>Length</th>
                                    <th>Not NULL</th>
                                    <th>DEFAULT</th>
                                </tr>
                            </table>
                            <div>
                                <label for="InputCountRow">Insert:</label>
                                <input id="InputCountRow" type="number" min="1" value="1" />
                                <button id="BNAddRow">Add</button>
                            </div>
                            <div style="text-align: right;">
                                <button id="BNGoNewTable">Go</button>
                            </div>
                        </div>
                    </div>

                    <div>
                        <a id="BNEditTable"   href="#">Edit Table</a>
                        <div  id="UIEditTable" style="display: none;">
                            <div>
                                <label>Table Name:</label>
                                <select id="SelectTableField"   >

                                </select>
                            </div>

                            <table border="1" id="FieldList" style="text-align: center;" >
                                <tr>
                                    <th>Select</th>
                                    <th>Name</th>
                                    <th>Type</th>
                                    <th>Length</th>
                                    <th>Not NULL</th>
                                    <th>DEFAULT</th>
                                    <th>Edit</th>
                                </tr>
                            </table>
                            <div>
                                <label for="InputCountAlterField">Insert:</label>
                                <input id="InputCountAlterField" type="number" min="1" value="1" />
                                <button id="BNAddField">Add</button>
                            </div>
                            <div style="text-align: right;">
                                <button id="BNDropField">Drop</button>
                                <button id="BNGoAlterField">Go</button>
                            </div>
                        </div>
                    </div>

                    <datalist id="DefaultTypes">
                        <option value="NULL">
                        <option value="CURRENT_TIME">
                        <option value="CURRENT_DATE">
                        <option value="CURRENT_TIMESTAMP">
                    </datalist>

                </div>
                <div class="Aside" >
                    <div id="DatabaseManager" class="BorderBlock" style="margin-top: 1px;">
                        <label class="Title">Database Manager</label>
                        <ul>
                            <li> <a href="MainPage.php">Database</a></li>
                            <li> <a href="<?php echo "Table.php?path=" . urlencode($_GET["path"]); ?>">Table</a></li>
                            <li style="font-weight: bold;">  Field </li>
                            <li> <a href="<?php echo "Data.php?path=" . urlencode($_GET["path"]); ?>">Data</a></li>
                            <li><a href="<?php echo "SQL.php?path=" . urlencode($_GET["path"]); ?>">SQL</a></li>
                        </ul>
                    </div>
                    <div class="BorderBlock" style="margin-top: 1px;">
                        <label class="Title">Form</label>
                        <a href="#">Create Form</a>
                    </div>
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
            <div>

            </div>
        </body>
    </html>
    <?php
    } else {
        header("location: MainPage.php");
    }
} else {
    header("location: ../../../../Session/AuthUserID.php");
    session_destroy();
}
