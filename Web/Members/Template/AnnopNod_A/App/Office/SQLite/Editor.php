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
            <title>Edit Database</title>
            <link rel="stylesheet" type="text/css" href="../../../../../../css/HolyGrail.css">
            <link rel="stylesheet" type="text/css" href="../../../../../../css/PersonalCMS.css">
            <style>
                .BNTab{
                    text-decoration: none;
                    color: blue;
                }
            </style>
            <?php
            foreach ($modlist as $value) {
                echo $value->Execute(Module_SDK_Basic::Layout_Head);
            }
            ?>

            <script src="../../../../../../js/dom/SuperDialog.js"></script>
            <script src="../../../../../../js/dom/SSQueryFW.js"></script>
            <script src="../../../../../../js/io/Ajax.js"></script>
            <script src="../../../../../../js/office/SQLite/TableEditor.js"></script>
            <script src="../../../../../../js/office/SQLite/DataEditor.js"></script>
            <script src="../../../../../../js/array/ArrayQueryFW.js"></script>
            <script>
                var ss = new SSQueryFW();
                ss.DocumentReady(function () {
                    var ajax = new Ajax();
                    var sd = new SuperDialog();
                    var arrfn = new ArrayQueryFW();
                    var url = "";
                    var tablenew = new SQLite_Table_Editor("#TableNew");
                    var tabledit = new SQLite_Table_Editor("#TableEdit");
                    var dataedit = new SQLite_Data_Editor("#DataEdit");
                    if (ss.URLParam()["path"] !== undefined) {
                        url = ss.URLParam()["path"];
                        if (url.charAt(url.length - 1) === "#") {
                            url = url.slice(0, -1);
                        }
                        LoadTable();
                    } else {
                        window.location.replace("index.php");
                    }
                    function LoadTable() {

                        ajax.Post("../../../../../Api/Ajax/Office/SQLite/Reader/GetSQLiteTable.php", {"Path": url}, function (data) {
                            data = JSON.parse(data);
                            ss.S(".OPTTableList").Empty();
                            for (var i in data) {
                                ss.S(".OPTTableList").Append(data[i], data[i]);
                            }
                            ss.S(".OPTTableList").Change();
                        });
                    }
                    tabledit.OnEdit(function (v) {
                        alert(v);
                    });
                    ss.S("#BNCreateNewRow").Click(function () {
                        for (var i = 1; i <= 3; i++) {
                            tablenew.AddNew();
                        }
                    });
                    ss.S("#BNCreateTable").Click(function () {
                        ajax.Post("../../../../../Api/Ajax/Office/SQLite/Manager/Exec.php", {"Path": url, "CMD": tablenew.GetCreateTableString(ss.S("#TXTCreateTable").Val())}, function (data) {
                            sd.Alert(data);
                            LoadTable();
                        });
                    });
                    ss.S(".BNTab").Tabs(".Tab", "data-tab");
                    ss.S("#OPTEditTableList").Change(function () {
                        ajax.Post("../../../../../Api/Ajax/Office/SQLite/Reader/QuerySQLiteTable.php", {"Path": url, "Query": "PRAGMA table_info('" + this.value + "');"}, function (data) {
                            data = JSON.parse(data);
                            for (var i in data) {
                                tabledit.AddForEdit(data[i]["cid"], data[i]["name"], data[i]["type"], data[i]["pk"], "-", data[i]["notnull"], data[i]["dflt_value"]);
                            }
                        });
                    });


                    ss.S("#OPTEditDataTableList").Change(function () {
                        var tablename=this.value ;
                        ajax.Post("../../../../../Api/Ajax/Office/SQLite/Reader/QuerySQLiteTable.php", {"Path": url, "Query": "PRAGMA table_info('" +tablename+ "');"}, function (data) {
                            var name = arrfn.Array_Value(JSON.parse(data), "name");
                            dataedit.AddColumn(name);
                            ajax.Post("../../../../../Api/Ajax/Office/SQLite/Reader/QuerySQLiteTable.php", {"Path": url, "Query": "SELECT * FROM ('" + tablename + "');"}, function (data) {
                                 
                               // var name = arrfn.Array_Value(JSON.parse(data), "name");
                                //dataedit.AddColumn(name);
                            });
                        });
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
                    <div style="background-color: burlywood;margin-top: 1px;border-style: solid;border-width: thin;">
                        <a class="BNTab" href="#" data-tab="tablenew">Create-Table</a>
                        <a class="BNTab" href="#" data-tab="tableedit">Edit-Table</a>
                        <a class="BNTab" href="#" data-tab="dataedit">Data</a>
                    </div>


                    <div class="Tab" data-tab="tablenew" style="position: relative;display: none;">
                        <div id="TableNew" style="position: absolute;">
                            <div>
                                <label>Name:</label>
                                <input id="TXTCreateTable" type="text" name="" value="" />
                                <button id="BNCreateTable">Create Table</button>
                                <button id="BNCreateNewRow">Add New Row</button>
                            </div>
                        </div>
                    </div>
                    <div class="Tab" data-tab="tableedit" style="position: relative;display: none;">
                        <div id="TableEdit" style="position: absolute;">
                            <div>
                                <label style="font-weight: bold;">Table:</label>
                                <select id="OPTEditTableList" class="OPTTableList"></select>

                            </div>

                        </div>
                    </div>
                    <div class="Tab" data-tab="dataedit" style="position: relative;display: none;">
                        <div id="DataEdit" style="position: absolute;">
                            <div>
                                <label style="font-weight: bold;">Table:</label>
                                <select id="OPTEditDataTableList" class="OPTTableList"></select>
                                <div>
                                    <span>Start:<input type="number" name="" value="" /></span>
                                    <span>Limit:<input type="number" name="" value="" /></span>
                                </div>
                            </div>
                        </div>
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
