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
            <link rel="stylesheet" type="text/css" href="../../../css/HolyGrail.css">
            <link rel="stylesheet" type="text/css" href="../../../css/PersonalCMS.css">

            <script src="../../../js/io/Ajax.js"></script>
            <script src="../../../js/dom/SSQueryFW.js"></script>
            <script src="../../../js/dom/SuperDialog/SuperDialog.js"></script>
            <script src="../../../js/dom/TableTools.js"></script>

            <style>
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
                    var tablemodview = new TableTools();
                    tablemodmanager.Import(document.getElementById("TableModuleManager"));
                    tablemodview.Import(document.getElementById("TableModView"));

                    tablemodview.AddEventListener("click", function (e) {

                    });
                    // 
                    ss.S("#BNSelectInstall").Change(function (e) {
                        if (this.value == "1") {
                            ss.S("#TRFileUpload").Show();
                            ss.S("#TRDIRUpload").Hide();
                            ss.S("#DIRUpload").Empty();
                        } else {
                            ajax.Get("Action/ViewModuleFiles.php", function (data) {
                                data = JSON.parse(data);
                                ss.S("#TRDIRUpload").Show();
                                ss.S("#TRFileUpload").Hide();
                                ss.S("#DIRUpload").Empty();
                                ss.S("#FileUpload").Val("");
                                for (var i in data) {
                                    ss.S("#DIRUpload").Append(data[i], data[i]);
                                }

                            });
                        }
                    }).Change();

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
                     });
                     });
                     */

                    ss.S("#BNInstallMod").Click(function (e) {
                        var d = dialog.ImportOkCancel( "#TableInstaller", function () {
                            ajax.Post("Action/InstallModule.php", ss.S(".Installer").ValByName(), function (data) {
                                d.close();
                                ss.S(".Installer").Val("");
                            });
                        }).Title("Install");
                    });

                    ss.S("#BNViewModFile").Click(function (e) {
                        ajax.Get("Action/ViewModuleFiles.php", function (data) {
                            tablemodview.DeleteRowAfter(0);
                            data = JSON.parse(data);
                            for (var i in data) {
                                tablemodview.InsertRow();
                                tablemodview.InsertCellLastRow(data[i]);
                                tablemodview.InsertCellLastRow('<button class="BNView" data-value="' + data[i] + '">View</button>');
                                tablemodview.InsertCellLastRow('<button class="BNEdit" data-value="' + data[i] + '">Delete</button>');
                            }
                            dialog.Import( "#TableModView").Title("View Module File");

                        });
                    });
                    ss.S("#SearchBox").Input(function (e) {
                        ajax.Post("Action/SearchModule.php", {"name": this.value}, function (data) {
                            tablemodmanager.DeleteRowAfter(0);
                            data = JSON.parse(data);
                            for (var i in data) {
                                tablemodmanager.InsertRow();
                                tablemodmanager.InsertCellLastRow('<div style="text-align: center;"><input type="checkbox" class="UserSelect" value="' + data[i]["id"] + '" /></div>');
                                tablemodmanager.InsertCellLastRow(data[i]["dirname"]);
                                tablemodmanager.InsertCellLastRow(data[i]["classname"]);
                                if (data[i]["public"] == "1") {
                                    tablemodmanager.InsertCellLastRow('<input type="checkbox" data-id="' + data[i]["id"] + '"   checked="checked" />');
                                } else {
                                    tablemodmanager.InsertCellLastRow('<input type="checkbox" data-id="' + data[i]["id"] + '" />');
                                }

                                tablemodmanager.InsertCellLastRow('<input type="number" name="" value="' + data[i]["priority"] + '" />');
                                tablemodmanager.InsertCellLastRow('<button class="BNEdit" data-value="' + data[i]["id"] + '">Edit</button>');
                            }
                        });
                    });
                    ss.S("#SearchBox").Input();
                });

            </script>
        </head>
        <body class="HolyGrail"> 
            <header class="Header">
                <div style="width: 50%;"></div>
                <div style="width: 50%;text-align: right;">
                    <span style="font-weight: bold;cursor: default;">Root</span>
                    <a  class="MenuLink" style="display: inline;" href="../../Auth/ExitRoot.php">Exit</a>
                </div>
            </header>
            <div class="HolyGrail-body">
                <nav>
                    <?php
                    foreach ($uinav->FindAllMenuFile("../../App") as $key => $valueA) {
                        echo '<div class="BorderBlock">';
                        printf(' <div class="TitleCenter">%s</div>', $key);
                        foreach ($valueA as $valueB) {
                            printf('  <a  class="MenuLink" href="../../App/%s">%s</a>', $valueB["path"], $valueB["name"]);
                        }
                        echo '</div>';
                    }
                    ?>
                </nav>

                <main>
                    <div style="display: flex;flex-direction: row;   ">
                        <input   style="flex-grow: 1;" type="text" id="SearchBox" value="" />
                    </div>
                    <table id="TableModuleManager" style="text-align: center;width: 100%;box-sizing: border-box;">
                        <tr>
                            <th>select</th>
                            <th>classname</th>
                            <th>dirname</th>
                            <th>public</th>
                            <th>priority</th>
                            <th>config</th> 
                        </tr>
                    </table>

                </main>


                <aside>
                    <div class="BorderBlock">
                        <div class="TitleCenter">Module</div>
                        <a id="BNInstallMod" href="#"   class="MenuLink">Install</a>
                        <a id="BNViewModFile" href="#"   class="MenuLink">View Module File</a>
                    </div>
                </aside>

            </div>
            <footer>
                <span style="font-weight: bold;display: block;">
                    <?php
                    echo "&COPY;" . date("Y") . " " . $config->GetName();
                    ?>
                </span>  

            </footer>
            <table id="TableInstaller" style="display: none;">
                <tr>
                    <td>Install:</td>
                    <td>
                        <select id="BNSelectInstall" style="width: 100%;box-sizing: border-box;">
                            <option value="1">Upload</option>
                            <option value="2">Select DIR</option>
                        </select>
                    </td>
                </tr>
                <tr id="TRFileUpload">
                    <td>File:</td>
                    <td><input id="FileUpload" name="file"  type="file"  class="Installer" style="width: 100%;box-sizing: border-box;" /></td>
                </tr>
                <tr id="TRDIRUpload">
                    <td>Dir Name:</td>
                    <td><select id="DIRUpload" name="dirname"  type="text" class="Installer" style="width: 100%;box-sizing: border-box;"></select></td>
                </tr>           
                <tr>
                    <td>Public:</td>
                    <td>
                        <input name="public"  type="checkbox" class="Installer" value="1" />
                    </td>
                </tr>
                <tr>
                    <td>Priority:</td>
                    <td><input name="priority"  type="number" class="Installer"  style="width: 100%;box-sizing: border-box;"  /></td>
                </tr>
                <tr>
                    <td>Password:</td>
                    <td><input name="password"  type="password" class="Installer" style="width: 100%;box-sizing: border-box;"  /></td>
                </tr>
            </table>
            <table id="TableModView" style="display: none;width: 100%;">
                <tr>
                    <th>DirName</th>
                    <th>View</th>
                    <th>Action</th>
                </tr>

            </table>
        </body>
    </html>
    <?php
} else {
    header('Location: ../../Auth/index.php');
}