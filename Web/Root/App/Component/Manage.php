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
            <script src="../../../js/dom/SuperDialog.js"></script>
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
                    var tablelist = new TableTools();
                    tablelist.Import(document.getElementById("TableField"));
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


                    ss.S(".BNInstall").Click(function (e) {
                        ajax.Post("Action/InstallComponent.php", {"DIR": this.getAttribute("data-id")}, function (data) {
                            if (data !== "1") {
                                dialog.Alert(data);
                            }
                        });
                    });
                    ss.S(".BNUnInstall").Click(function (e) {
                        var dir = this.getAttribute("data-id");
                        var dia = dialog.Confirm("UnInstall", function () {
                            ajax.Post("Action/UnInstallComponent.php", {"DIR": dir}, function (data) {
                                if (data == "1") {
                                    dia.Close();
                                } else {
                                    dia.Close();
                                    dialog.Alert(data);
                                }
                            });
                        });

                    });
                    ss.S("#TableList").Change(function (e) {
                        ajax.Post("Action/GetTableFields.php", {"name": this.value}, function (data) {
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


                    ss.S(".BNTableView").Click(function (e) {
                        var dbname = this.getAttribute("data-id");
                        ajax.Post("Action/GetTableList.php", {"dir": dbname}, function (s) {
                            s = JSON.parse(s);
                            ss.S("#TableList").Empty();
                            for (var i in s) {
                                ss.S("#TableList").Append(dbname + "/" + s[i], s[i]);
                            }
                            dialog.Import("TableFields", "#TableViewer");
                            ss.S("#TableList").Change();
                        });

                    });

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
                            printf('  <a  class="MenuLink" href="%s">%s</a>', $valueB["path"], $valueB["name"]);
                        }
                        echo '</div>';
                    }
                    ?>
                </nav>

                <main>

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

                </main>

                <aside>

                </aside>
            </div>
            <footer>
                <span style="font-weight: bold;display: block;">
                    <?php
                    echo "&COPY;" . date("Y") . " " . $config->GetName();
                    ?>
                </span>  

            </footer>



            <div id="TableViewer" style="display: none;">
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
        </body>
    </html>
    <?php
} else {
    header('Location: ../../Auth/index.php');
}