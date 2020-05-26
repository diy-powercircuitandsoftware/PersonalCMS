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
                     }).ZIndex(999);
                     });
                     */

                    ss.S(".BNInstall").Click(function (e) {
                        ajax.Post("Action/InstallComponent.php", {"DIR": this.getAttribute("data-id")}, function (data) {
                            if (data !== "1") {
                                dialog.Alert(data);
                            }
                        });
                    });
                    ss.S(".BNUnInstall").Click(function (e) {
                        var dir = this.getAttribute("data-id");
                        var dia = dialog.UnLock(function (v) {
                            ajax.Post("Action/UnInstallComponent.php", {"password": v, "DIR": dir}, function (data) {
                                if (data == "1") {
                                    dia.Close();
                                } else {
                                    dia.Close();
                                    dialog.Alert(data);
                                }
                            });
                        });

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
                            var d = dialog.Import("View Module File", "#TableModView", {"OK": function () {
                                    d.Close();
                                }, "Cancel": function () {
                                    d.Close();
                                }});

                        });
                    });

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
                <div>

                    <table style="text-align: center;">
                        <tr>                              
                            <th>Class Name</th>
                            <th>View</th> 
                            <th>Install</th> 
                            <th>UnInstall</th> 
                        </tr>
                        <?php
                        foreach ($uinav->GetFilesList("../../../../Class/Com/") as $value) {
                            echo '<tr>';
                            printf('<td>%s</td>', $value);
                            printf('<td><button class="BNTableView"  class="" data-id="%s">View</button></td>', $value);
                            printf('<td><button   class="BNInstall" data-id="%s">Install</button></td><td><button   class="BNUnInstall" data-id="%s">Uninstall</button></td>', $value, $value);
                            echo '</tr>';
                        }
                        ?>
                    </table>

                </div>

                <div >

                </div>
            </div>


        </body>
    </html>
    <?php
} else {
    header('Location: ../../Auth/index.php');
}