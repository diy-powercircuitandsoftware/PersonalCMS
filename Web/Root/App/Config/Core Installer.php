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
            <style>
                #ConfigList{
                    width: 95%;
                    border-style: solid;
                    border-width: thin;
                    margin-left: auto;
                    margin-right: auto;
                }
                #ConfigList input[type='text']{
                    width: 99%;
                    box-sizing: border-box;

                }
            </style>
            <script>
                var ss = new SSQueryFW();
                ss.DocumentReady(function () {
                    var dialog = new SuperDialog();
                    var ajax = new Ajax();
                    var tablelist = new TableTools();
                    tablelist.Import(document.getElementById("TableField"));
                    ss.S(".BNInstall").Click(function (e) {
                        e.preventDefault();
                        dialog.Load(this.href);
                    });

                    ss.S(".BNUnInstall").Click(function (e) {
                        e.preventDefault();
                        var ref = this.href;
                        var u = dialog.Confirm("UnInstall",function () {
                            ajax.Post(ref, {}, function (s) {
                                if (s == "1") {
                                    u.Close();
                                } else {
                                    dialog.Alert(s);
                                    u.Close();
                                }
                            });

                        }).ZIndex(999);

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

                    <table>
                        <tr>
                            <th>Core</th>
                            <th>Table View</th>
                           
                            <th>Install</th>
                            <th>UnInstall</th>
                        </tr>
                        <?php
                        $path = '../../../../Class/Core/';
                        foreach (scandir($path) as $first) {
                            foreach (scandir($path . $first) as $sec) {
                                if ($sec == "Database.php") {
                                    echo '<tr>';
                                    printf('<td>%s</td>', $first);
                                    printf('<td><button class="BNTableView" data-id="%s">Table View</button></td>', $first);                                  
                                    printf('<td><a class="BNInstall" href="Action/CoreInstall.php?dir=%s"><button>Install</button></a></td>'
                                            . '<td><a class="BNUnInstall" href="Action/CoreUnInstall.php?dir=%s"><button>UnInstall</button></a></td>', $first, $first);
                                    echo '</tr>';
                                }
                            }
                        }
                        ?>
                    </table>

                </div>
                <div>

                </div>
            </div>
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





