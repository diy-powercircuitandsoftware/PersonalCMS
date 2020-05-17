<?php
session_start();
include_once '../../../../Class/Core/Config/Config.php';
include_once '../../../../Class/Core/UI/NAV.php';
$config = new Config();
$uinav = new UINAV();
if ($config->HasRootAuth(session_id())) {
    ?>
    <!DOCTYPE html>
    <html>
        <head>
            <meta charset="UTF-8">
            <title><?php echo basename(__FILE__, ".php"); ?></title>
            <link rel="stylesheet" href="../css/Page.css">
            <script src="../../../js/io/Ajax.js"></script>
            <script src="../../../js/dom/SSQueryFW.js"></script>
            <script src="../../../js/dom/SuperDialog.js"></script>
            <script src="../../../js/dom/TableTools.js"></script>
            <script src="../../../js/dom/Tab.js"></script>
            <style>
                #UserList{
                    width: 100%;
                    border-style: solid;
                    border-width: thin;
                    margin-left: auto;
                    margin-right: auto;
                }
                table button{
                    width: 100%;
                    box-sizing: border-box;
                }

            </style>
            <script>
                var ss = new SSQueryFW();
                ss.DocumentReady(function () {
                    var ajax = new Ajax();

                    var dialog = new SuperDialog();
                    var tablemodmanager = new TableTools();
                    var tablelist = new TableTools();
                    var tab = new Tab(document.getElementById("Tab"));


                    tab.Add("1", document.getElementById("ModuleList"));
                    tab.Add("2", document.getElementById("ModuleManager"));

                    tablemodmanager.Import(document.getElementById("TableModuleManager"));
                    tablelist.Import(document.getElementById("TableField"));
                    /*
                     var lastid = 0;
                         
                         
                     userlist.Import(document.getElementById("UserList"));
                         
                     ajaxsb.AddScrollEvent(function (data) {
                     try {
                     data = JSON.parse(data);
                     for (var i in data) {
                     userlist.InsertRow();
                     userlist.InsertCellLastRow('<div style="text-align: center;"><input type="checkbox" class="UserSelect" value="' + data[i]["id"] + '" /></div>');
                     userlist.InsertCellLastRow(data[i]["id"]);
                     userlist.InsertCellLastRow(data[i]["alias"]);
                     userlist.InsertCellLastRow(data[i]["writable"]);
                     userlist.InsertCellLastRow(data[i]["enable"]);
                     userlist.InsertCellLastRow(data[i]["email"]);
                     userlist.InsertCellLastRow(data[i]["phone"]);
                     userlist.InsertCellLastRow('<button class="BNEdit" data-value="' + data[i]["id"] + '">Edit</button>');
                     lastid = Math.max(lastid, data[i]["id"]);
                     }
                     ajaxsb.Param("id", lastid);
                     } catch (e) {
                     dialog.Alert(data);
                     }
                     });
                     userlist.AddEventListener("click", function (e) {
                     if (e.target.getAttribute("class") === "BNEdit") {
                     var v = e.target.getAttribute("data-value");
                     ajax.Post("Action/GetUserData.php", {"id": v}, function (v) {
                     ss.S(".EditUser").ValByName(JSON.parse(v));
                     var d = dialog.Import("Edit", "#EditTable", {"OK": function () {
                     ajax.Post("Action/EditUserData.php", ss.S(".EditUser").ValByName(), function () {
                     lastid = 0;
                     userlist.DeleteRowAfter(0);
                     ajaxsb.Param("id", lastid);
                     ajaxsb.LoadAjax();
                     d.Close();
                         
                     });
                     }, "Cancel": function () {
                     d.Close();
                     ss.S(".AddUser").Val("");
                     }});
                     });
                     }
                     });
                         
                     ss.S("#BNAddUser").Click(function () {
                     var d = dialog.Import("Add", "#AddTable", {"OK": function () {
                     ajax.Post("Action/AddUser.php", ss.S(".AddUser").ValByName(), function () {
                     ajaxsb.LoadAjax();
                     d.Close();
                     ss.S(".AddUser").Val("");
                     });
                     }, "Cancel": function () {
                     d.Close();
                     ss.S(".AddUser").Val("");
                     }});
                     });
                         
                     ss.S("#BNDeleteUser").Click(function () {
                     dialog.Confirm("are you sure want to delete select user", function () {
                     var v = ss.S(".UserSelect").Val();
                     ajax.Post("Action/DeleteUser.php", {"UserID": v}, function (s) {
                     lastid = 0;
                     userlist.DeleteRowAfter(0);
                     ajaxsb.Param("id", lastid);
                     ajaxsb.LoadAjax();
                     });
                     }).ZIndex(999);
                     });
                     */

                    ss.S("#BNDiscardConfig").Click(function (e) {

                    });


                    ss.S(".BNInstall").Click(function (e) {
                        var modulefile = (this.getAttribute("data-id"));
                        ajax.Post("Action/GetConstantLayout.php", {}, function (l) {
                            var u = dialog.DropDown(function (v) {
                                ajax.Post("Action/InstallModule.php", {"FileName": modulefile, "Layout": v}, function (s) {

                                });

                            }).ZIndex(999).Title("Layout");
                            l = JSON.parse(l);
                            for (var k in l) {
                                u.Add(l[k], k);
                            }

                        });

                    });
                    ss.S("#BNInstallModManager").Click(function (e) {
                        e.preventDefault();
                        dialog.Load(this.href);
                    });





                    ss.S(".BNModuleTab").Click(function (e) {
                        tab.Show(this.getAttribute("data-id"));
                    });
                    ss.S("#BNSaveAllConfig").Click(function (e) {

                    });
                    ss.S("#BNTableViewModManager").Click(function (e) {
                        ajax.Post("Action/GetTableListManager.php", {}, function (s) {
                            s = JSON.parse(s);
                            ss.S("#TableList").Empty();
                            for (var i in s) {
                                ss.S("#TableList").Append(s[i], s[i]);
                            }
                            dialog.Import("TableFields", "#TableViewer");
                            ss.S("#TableList").Change();
                        });

                    });

                    ss.S(".BNUnInstall").Click(function (e) {
                        alert(this.getAttribute("data-id"));

                    });
                    ss.S("#BNUnInstallModManager").Click(function (e) {
                        e.preventDefault();
                        var ref = this.href;
                        var u = dialog.UnLock(function (v) {
                            ajax.Post(ref, {"password": v}, function (s) {
                                if (s == "1") {
                                    tablemodmanager.DeleteRowAfter(0);
                                    dialog.Alert("UnInstall Complete");
                                    u.Close();
                                } else {
                                    dialog.Alert(s);
                                    u.Close();
                                }

                            });

                        }).ZIndex(999);

                    });
                    ss.S("#SearchBox").Input(function (e) {

                        ajax.Post("Action/SearchModule.php", {"name": this.value}, function (data) {

                            tablemodmanager.DeleteRowAfter(0);
                            data = JSON.parse(data);
                            for (var i in data) {

                                tablemodmanager.InsertRow();
                                tablemodmanager.InsertCellLastRow('<div style="text-align: center;"><input type="checkbox" class="UserSelect" value="' + data[i]["id"] + '" /></div>');
                                tablemodmanager.InsertCellLastRow(data[i]["filename"]);
                                tablemodmanager.InsertCellLastRow(data[i]["classname"]);
                                if (data[i]["public"] == "1") {
                                    tablemodmanager.InsertCellLastRow('<input type="checkbox" data-id="' + data[i]["id"] + '"   checked="checked" />');
                                } else {
                                    tablemodmanager.InsertCellLastRow('<input type="checkbox" data-id="' + data[i]["id"] + '" />');
                                }
                                tablemodmanager.InsertCellLastRow(data[i]["layout"]);
                                tablemodmanager.InsertCellLastRow('<input type="number" name="" value="' + data[i]["priority"] + '" />');
                                if (data[i]["enable"] == "1") {
                                    tablemodmanager.InsertCellLastRow('<input type="checkbox" data-id="' + data[i]["id"] + '"   checked="checked" />');
                                } else {
                                    tablemodmanager.InsertCellLastRow('<input type="checkbox" data-id="' + data[i]["id"] + '" />');
                                }

                                tablemodmanager.InsertCellLastRow('<button class="BNEdit" data-value="' + data[i]["id"] + '">Edit</button>');
                            }

                        });

                    });

                    ss.S("#TableList").Change(function (e) {
                        ajax.Post("Action/GetTableFieldsManager.php", {"name": this.value}, function (data) {
                            data = JSON.parse(data);
                            tablelist.DeleteRowAfter(0);
                            for (var i in data) {
                                tablelist.InsertRow();
                                tablelist.InsertCellLastRow(data[i]["cid"]);
                                tablelist.InsertCellLastRow(data[i]["name"]);
                                tablelist.InsertCellLastRow(data[i]["type"]);
                                tablelist.InsertCellLastRow(data[i]["notnull"]);
                                tablelist.InsertCellLastRow(data[i]["dflt_value"]);
                                tablelist.InsertCellLastRow(data[i]["pk"]);
                            }

                        });

                    });
                    ss.S("#SearchBox").Input();
                });

            </script>
        </head>
        <body> 
            <header id="mainheader">
                <div style="width: 50%;"></div>
                <div style="width: 50%;text-align: right;">
                    <span style="font-weight: bold;cursor: default;">Root</span>
                    <a style="font-weight: bold;" href="../../Auth/ExitRoot.php">Exit</a>
                </div>
            </header>
            <div class="LMR157015">
                <div>
                    <nav>
                        <?php
                        foreach ($uinav->FindAllMenuFile("../../App") as $key => $valueA) {
                            echo '<div class="BorderBlock">';
                            printf(' <div class="TitleCenter">%s</div>', $key);
                            foreach ($valueA as $valueB) {
                                printf('  <a  class="MenuLink" href="%s">%s</a>', $valueB["path"], $valueB["name"]);
                            }
                            echo '</div>';
                        }
                        ?>
                    </nav>
                </div> 
                <div id="Tab">

                </div>

                <div>
                    <aside>
                        <div class="BorderBlock">
                            <div class="TitleCenter">Module</div>
                            <a class="BNModuleTab" data-id="1" style="display: block;"  href="#">Module List</a>
                            <a class="BNModuleTab" data-id="2" style="display: block;"  href="#">Module Manager</a>

                            <a id="BNInstallModManager"  style="display: block;" href="Action/InstallManager.php">Install Manager</a>
                            <a  id="BNUnInstallModManager" style="display: block;" href="Action/UnInstallManager.php">UnInstall Manager</a>
                            <a  id="BNTableViewModManager" style="display: block;" href="#">Table View</a>
                        </div>
                    </aside>
                </div>
            </div>


            <div id="AllDialog" style="display: none;">


                <div id="TableViewer">
                    <select id="TableList" style="width: 100%;box-sizing: border-box;"></select>
                    <table id="TableField">
                        <tr>
                            <th>cid</th>
                            <th>name</th>
                            <th>type</th>
                            <th>notnull</th>
                            <th>dflt_value</th>
                            <th>pk</th>
                        </tr>
                    </table>
                </div>
            </div>

            <div id="TabEditor">
                <div id="ModuleManager">
                    <div style="display: flex;flex-direction: row;   ">
                        <input   style="flex-grow: 1;" type="text" id="SearchBox" value="" />
                    </div>
                    <table id="TableModuleManager" style="text-align: center;width: 100%;box-sizing: border-box;">
                        <tr>
                            <th>id</th>
                            <th>filename</th>
                            <th>classname</th>
                            <th>public</th>
                            <th>layout</th>
                            <th>priority</th>
                            <th>enable</th>
                            <th>config</th> 
                        </tr>
                    </table>
                    <button id="BNSaveAllConfig">Save</button>
                    <button id="BNDiscardConfig">Discard</button>
                </div>
                <div id="ModuleList" >
                     

                </div> 
            </div>

        </body>
    </html>
    <?php
} else {
    header('Location: ../../Auth/index.php');
}