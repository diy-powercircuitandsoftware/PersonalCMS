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

                    var sd = new SuperDialog();
                    var DataList = new TableTools().Import(document.getElementById("DataList"));
                    var TBEdit = new TableTools().Import(document.getElementById("TBEdit"));
                    ss.Post("../../../../Api/Ajax/SQLite/GetTable.php", {"path": ss.URLParam()["path"]}, function (data) {
                        data = JSON.parse(data)
                        for (var i in data) {
                            ss.S("#SelectDataField").Append("<option></option>").Val(data[i]).Text(data[i]);
                        }
                        ss.S("#SelectDataField").Change();
                    });

                    ss.S("#BNAddCountData").Click(function () {
                        for (var i = 1; i <= parseInt(ss.S("#TXTAddCountData").Val()); i++) {
                            DataList.InsertRow().setAttribute("class", "RowInsert");
                            for (var j = 0; j < DataList.DataTypeList.length; j++) {
                                if (DataList.DataTypeList[j].type == "text") {
                                    DataList.InsertCellLastRow('<input class="TXTInsert" type="text" name="' + DataList.DataTypeList[j].name + '" value="" />');
                                } else if (DataList.DataTypeList[j].type == "number") {
                                    DataList.InsertCellLastRow('<input class="TXTInsert" type="number" name="' + DataList.DataTypeList[j].name + '" value="" />');
                                } else if (DataList.DataTypeList[j].type == "float") {
                                    DataList.InsertCellLastRow('<input class="TXTInsert" type="number" name="' + DataList.DataTypeList[j].name + '" value="" />');
                                } else {

                                }
                            }
                            DataList.InsertCellLastRow('');
                            DataList.InsertCellLastRow('');
                        }
                    });

                    ss.S("#BNEmptyData").Click(function () {
                        sd.Confirm("Do You Delete All Data", function () {
                            ss.Post("../../../../Api/Ajax/SQLite/EmptyData.php", {"ID": ss.URLParam()["path"], "TableName": ss.S("#SelectDataField").Val()}, function (data) {
                                ss.S("#SelectDataField").Change();
                            });
                        }).ZIndex(999).Title("Empty???");
                    });

                    ss.S("#BNSaveData").Click(function () {
                        var json = {};
                        json.row = [];
                        json.path = ss.URLParam()["path"];
                        json.table = ss.S("#SelectDataField").Val();
                        ss.S(".RowInsert").ForEach(function (d) {
                            var InsertData = ss.S(d).Q(".TXTInsert").SerializeToJson();
                            for (var i in InsertData) {
                                if (typeof InsertData[i] == "string") {
                                    InsertData[i] = "'" + InsertData[i] + "'";
                                }
                            }
                            json.row.push(InsertData);
                        });


                        ss.Post("../../../../Api/Ajax/SQLite/InsertData.php", json, function (data) {
                            ss.S("#SelectDataField").Change();
                        });

                    });

                    ss.S("#SelectDataField").Change(function () {
                        var ref = this;
                        ref.pk = "";
                        var tablename = this.value;
                        ss.Post("../../../../Api/Ajax/SQLite/GetFieldByTable.php", {"path": ss.URLParam()["path"], "name": tablename}, function (data) {
                            DataList.Empty();
                            TBEdit.Empty();
                            DataList.DataTypeList = [];
                            DataList.pk = "";
                            data = JSON.parse(data)
                            DataList.InsertRow();
                            for (var i in data) {
                                DataList.InsertCellLastRow(data[i]["name"]);
                                var strtype = data[i]["type"].toUpperCase();
                                if (strtype.indexOf("TEXT") >= 0) {
                                    DataList.DataTypeList.push({
                                        "type": "text",
                                        "name": data[i]["name"]
                                    });
                                } else if (strtype.indexOf("INTEGER") || strtype.indexOf("NUMERIC")) {
                                    DataList.DataTypeList.push({
                                        "type": "number",
                                        "name": data[i]["name"]
                                    });

                                } else if (strtype.indexOf("REAL")) {
                                    DataList.DataTypeList.push({
                                        "type": "float",
                                        "name": data[i]["name"]
                                    });
                                }


                                if (data[i]["pk"] == 1) {
                                    DataList.pk = data[i]["name"];
                                }

                            }

                            DataList.InsertCellLastRow("Edit");
                            DataList.InsertCellLastRow("Delete");

                            ss.Post("../../../../Api/Ajax/SQLite/SelectAllData.php", {"path": ss.URLParam()["path"], "tablename": tablename}, function (arrdata) {
                                arrdata = JSON.parse(arrdata);
                                for (var i in arrdata) {
                                    var pkid = 0;
                                    DataList.InsertRow();

                                    var subarray = arrdata[i];
                                    for (var k in subarray) {
                                        if (k == DataList.pk) {
                                            pkid = subarray[k];
                                        }
                                        if (subarray[k] !== null) {
                                            DataList.InsertCellLastRow(subarray[k].toString());
                                        } else {
                                            DataList.InsertCellLastRow("");
                                        }

                                    }
                                    DataList.InsertCellLastRow('<button data-primarykey="' + DataList.pk + '" data-id="' + pkid + '" class="BNEditRecord">Edit</button>');
                                    DataList.InsertCellLastRow('<button data-primarykey="' + DataList.pk + '" data-id="' + pkid + '" class="BNDeleteRecord">Delete</button>');
                                }
                            });
                        });
                    });
                    DataList.addEventListener("click", function (e) {
                        if (e.target.getAttribute("class") == "BNEditRecord") {
                            var param = {
                                "path": ss.URLParam()["path"],
                                "tablename": ss.S("#SelectDataField").Val(),
                                "pk": e.target.getAttribute("data-primarykey"),
                                "id": e.target.getAttribute("data-id")
                            };
                            ss.Post("../../../../Api/Ajax/SQLite/SelectDataByID.php", param, function (ldata) {
                                ldata = JSON.parse(ldata);
                                TBEdit.Empty();
                                for (var i = 0; i < DataList.DataTypeList.length; i++) {
                                    TBEdit.InsertRow();
                                    TBEdit.InsertCellLastRow(DataList.DataTypeList[i].name);
                                    if (DataList.DataTypeList[i].type == "text") {
                                        TBEdit.InsertCellLastRow('<input class="TXTUpdate" type="text" name="' + DataList.DataTypeList[i].name + '" value="" />');
                                    } else if (DataList.DataTypeList[i].type == "number") {
                                        TBEdit.InsertCellLastRow('<input class="TXTUpdate" type="number" name="' + DataList.DataTypeList[i].name + '" value="" />');
                                    } else if (DataList.DataTypeList[i].type == "float") {
                                        TBEdit.InsertCellLastRow('<input class="TXTUpdate" type="number" name="' + DataList.DataTypeList[i].name + '" value="" />');
                                    } else {

                                    }
                                }
                                ss.S(".TXTUpdate").ValByName(ldata);
                                sd.Import("#TBEdit", function () {
                                    var json = {
                                        "path": ss.URLParam()["path"],
                                        "tablename": ss.S("#SelectDataField").Val(),
                                        "pk": e.target.getAttribute("data-primarykey"),
                                        "id": e.target.getAttribute("data-id"),
                                        "data": ss.S(".TXTUpdate").SerializeToJson()
                                    };
                                    ss.Post("../../../../Api/Ajax/SQLite/UpdateDataByID.php", json, function (data) {
                                        ss.S("#SelectDataField").Change();
                                    });
                                }).ZIndex(999).Title("Edit");

                            });

                        } else if (e.target.getAttribute("class") == "BNDeleteRecord") {
                            var json = {
                                "path": ss.URLParam()["path"],
                                "tablename": ss.S("#SelectDataField").Val(),
                                "pk": e.target.getAttribute("data-primarykey"),
                                "id": e.target.getAttribute("data-id")
                            };
                            sd.Confirm("Do you Delete It", function () {
                                ss.Post("../../../../Api/Ajax/SQLite/DeleteData.php", json, function (data) {
                                    ss.S("#SelectDataField").Change();
                                });
                            }).ZIndex(999);
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
                <div class="Section" style="box-sizing: border-box;overflow: auto;">
                    <div>
                        <label>Table Name:</label>
                        <select id="SelectDataField"  >

                        </select>
                        <button id="BNEmptyData">Empty</button>
                        <label>Add Row:</label>
                        <input id="TXTAddCountData" type="number" min="1" value="1" />
                        <button  id="BNAddCountData">Go</button>
                        <button  id="BNSaveData">Save</button>
                    </div>

                    <table id="DataList" style="max-width: 100%;text-align: center;" border="1">

                    </table>

                </div>
                <div class="Aside" >
                    <div id="DatabaseManager" class="BorderBlock" style="margin-top: 1px;">
                        <label class="Title">Database Manager</label>
                        <ul>
                            <li> <a href="MainPage.php">Database</a></li>
                            <li> <a href="<?php echo "Table.php?path=" . ($_GET["path"]); ?>">Table</a></li>
                            <li > <a href="<?php echo "Field.php?path=" . ($_GET["path"]); ?>"> Field</a> </li>
                            <li style="font-weight: bold;">  Data</li>
                            <li><a href="<?php echo "SQL.php?path=" . ($_GET["path"]); ?>">SQL</a></li>
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

            <table id="TBEdit">

            </table>
        </body>
    </html>
    <?php
    } else {
        header("location: MainPage.php");
    }
} else {
   header("location: ../../../../../Auth/Login.php");
    session_destroy();
}
