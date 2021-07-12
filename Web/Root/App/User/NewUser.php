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
                #UserList{
                    width: 100%;
                    border-style: solid;
                    border-width: thin;
                    margin-left: auto;
                    margin-right: auto;
                }
                table button{
                    width: 100%;

                }

            </style>
            <script>
                var ss = new SSQueryFW();
                ss.DocumentReady(function () {
                    var ajax = new Ajax();
                    var ajaxsb = new Ajax_ScrollBar("Action/GetRegister.php", {"phone": 0});
                    var dialog = new SuperDialog();
                    var userlist = new TableTools();
                    var lastid = 0;
                    userlist.Import(document.getElementById("UserList"));
                    ajaxsb.AddScrollEvent(function (data) {
                        data = JSON.parse(data);
                        for (var i in data) {
                            userlist.InsertRow();
                            userlist.InsertCellLastRow('<div style="text-align: center;"><input type="checkbox" class="UserSelect" value="' + data[i]["id"] + '" /></div>');
                            userlist.InsertCellLastRow(data[i]["id"]);
                            userlist.InsertCellLastRow(data[i]["alias"]);
                            userlist.InsertCellLastRow(data[i]["writable"]);
                            userlist.InsertCellLastRow(data[i]["enable"]);
                            userlist.InsertCellLastRow('<button data-value="' + data[i]["userid"] + '">Edit</button>');
                            userlist.InsertCellLastRow('<button class="BNStorage" data-value="' + data[i]["userid"] + '">Check</button>');
                            lastid = Math.max(lastid, data[i]["id"]);
                        }
                        ajaxsb.Param("phone", lastid);
                    });

                    ss.S("#BNAddUser").Click(function () {
                        var d = dialog.ImportOkCancel("Add", "#AddTable", {"OK"sss: function () {
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




                });

            </script>
        </head>
        <body class="HolyGrail"> 
            <header  class="Header">
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
                    <div style="display: flex;flex-direction: row;">
                        <input style="flex-grow: 1;" type="text" id="SearchBox" value="" />
                        <select id="SearchOption">
                            <option value="alias">Alias</option>
                            <option value="email">Email</option>
                            <option value="phone">Phone</option>
                        </select>
                    </div>
                    <table id="UserList" style="text-align: center;">
                        <tr  class="Title">
                            <th>Phone</th>
                            <th>Email</th>
                            <th>Alias</th>

                        </tr>
                    </table>
                </main> 

                <aside>
                    <div class="BorderBlock">
                        <div class="TitleCenter">User</div>
                        <a id="BNAcceptUser"  class="MenuLink" href="#">Accept Request</a>
                        <a id="BNRejectUser"  class="MenuLink" href="#">Reject Request</a>

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
            <div id="AllDialog" style="display: none;">
                <table id="AddTable" style="width: 100%;">
                    <tr>
                        <td>Alias:</td>
                        <td><input type="text" class="AddUser" name="Alias" style="width: 98%;" value="" /></td>
                    </tr>
                    <tr>
                        <td>Password:</td>
                        <td><input type="password" class="AddUser" name="Password"  style="width: 98%;"  name="" value="" /></td>
                    </tr>
                </table>
            </div>
        </body>

    </html>
    <?php
} else {
    header('Location: ../../Auth/index.php');
}