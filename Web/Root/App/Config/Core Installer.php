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
            <script src="../../../js/dom/SuperDialog/Template/Basic/MessageBox.js"></script>
            <script src="../../../js/dom/SuperDialog/Template/Basic/Load.js"></script>
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
                        new SuperDialog_Template_Load().Load(this.href).close();
                    });

                    ss.S(".BNUnInstall").Click(function (e) {
                        e.preventDefault();
                        var ref = this.href;
                        var u = new SuperDialog_Template_MessageBox().Confirm("UnInstall",function () {
                            ajax.Post(ref, {}, function (s) {
                                if (s == "1") {
                                    u.close();
                                } else {
                                    new SuperDialog_Template_MessageBox().Alert(s);
                                    u.close();
                                }
                            });

                        }) ;

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
                            dialog.Import( "#TableViewer").Title("TableFields");
                            ss.S("#TableList").Change();
                        });

                    });

                });

            </script>
        </head>
        <body class="HolyGrail">

            <header  class="Header">
                <div style="width: 50%;"></div>
                <div style="width: 50%;text-align: right;">
                    <span style="font-weight: bold;cursor: default;">Root</span>
                    <a class="MenuLink" style="display: inline;" href="../../Auth/ExitRoot.php">Exit</a>
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





