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
                    box-sizing: border-box;
                }

            </style>
            <script>
                var ss = new SSQueryFW();
                ss.DocumentReady(function () {
                    var ajax = new Ajax();
                    var ajaxsb = new Ajax_ScrollBar("Action/GetUserList.php", {"id": 0});
                    var dialog = new SuperDialog();
                    var userlist = new TableTools();
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
                                var d = dialog.ImportOkCancel("Edit", "#EditTable", function () {
                                    ajax.Post("Action/EditUserData.php", ss.S(".EditUser").ValByName(), function () {
                                        lastid = 0;
                                        userlist.DeleteRowAfter(0);
                                        ajaxsb.Param("id", lastid);
                                        ajaxsb.LoadAjax();
                                        d.Close();

                                    });
                                });
                            });
                        }
                    });

                    ss.S("#BNAddUser").Click(function () {
                        var d = dialog.ImportOkCancel( "#AddTable", function () {
                            ajax.Post("Action/AddUser.php", ss.S(".AddUser").ValByName(), function () {
                                ajaxsb.LoadAjax();
                                d.close();
                                ss.S(".AddUser").Val("");
                            });
                        }).Title("Add");
                    });

                    ss.S("#BNDeleteUser").Click(function () {
                       new SuperDialog_Template_MessageBox().Confirm("are you sure want to delete select user", function () {
                            var v = ss.S(".UserSelect").Val();
                            ajax.Post("Action/DeleteUser.php", {"UserID": v}, function (s) {
                                lastid = 0;
                                userlist.DeleteRowAfter(0);
                                ajaxsb.Param("id", lastid);
                                ajaxsb.LoadAjax();
                            });
                        });
                    });

                    ss.S("#SearchBox").Input(function (e) {
                        if (this.value !== "") {
                            ajax.Post("Action/SearchUser.php", {"data": this.value, "field": ss.S("#SearchOption").Val()}, function (data) {
                                lastid = 0;
                                ajaxsb.Param("id", lastid);
                                userlist.DeleteRowAfter(0);
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
                                }

                            });
                        } else {
                            lastid = 0;
                            userlist.DeleteRowAfter(0);
                            ajaxsb.Param("id", lastid);
                            ajaxsb.LoadAjax();
                        }
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
                            printf('  <a  class="MenuLink" href="../../App/%s">%s</a>', $valueB["path"], $valueB["name"]);
                        }
                        echo '</div>';
                    }
                    ?>
                </nav>

                <main>
                    <div style="display: flex;flex-direction: row;   ">
                        <input   style="flex-grow: 1;" type="text" id="SearchBox" value="" />
                        <select id="SearchOption">
                            <option value="alias">Alias</option>
                            <option value="email">Email</option>
                            <option value="phone">Phone</option>
                        </select>
                    </div>
                    <table id="UserList" style="text-align: center;">
                        <tr  class="Title">
                            <th>Select</th>
                            <th>UserID</th>
                            <th>Alias</th>
                            <th>Writable</th>
                            <th>Enable</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Edit</th>
                        </tr>
                    </table>
                </main> 

                <aside>
                    <div class="BorderBlock">
                        <div class="TitleCenter">User</div>
                        <a id="BNAddUser"  class="MenuLink" href="#">Add New User</a>
                        <a id="BNDeleteUser"  class="MenuLink" href="#">Delete User</a>                             
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
                        <td>Name:</td>
                        <td><input type="text" class="AddUser" name="Name" style="width: 100%;box-sizing: border-box;" value="" /></td>
                    </tr>
                    <tr>
                        <td>LastName:</td>
                        <td><input type="text" class="AddUser" name="LastName" style="width: 100%;box-sizing: border-box;" value="" /></td>
                    </tr>
                    <tr>
                        <td>Alias:</td>
                        <td><input type="text" class="AddUser" name="Alias" style="width: 100%;box-sizing: border-box;" value="" /></td>
                    </tr>
                    <tr>
                        <td>Password:</td>
                        <td><input type="password" class="AddUser" name="Password"  style="width: 100%;box-sizing: border-box;"  name="" value="" /></td>
                    </tr>
                </table>
                <table id="EditTable" style="width: 100%;">
                    <tr>
                        <td>id</td>
                        <td><input class="EditUser" style="width: 100%;box-sizing: border-box;"  type="text" name="id" value="" readonly="readonly" /></td>
                    </tr>
                    <tr>
                        <td>alias</td>
                        <td><input class="EditUser"  style="width: 100%;box-sizing: border-box;"  type="text" name="alias" value="" /></td>
                    </tr>
                    <tr>
                        <td>phone</td>
                        <td><input class="EditUser"  style="width: 100%;box-sizing: border-box;"  type="text" name="phone" value=""   /></td>
                    </tr>
                    <tr>
                        <td>email</td>
                        <td><input class="EditUser"  style="width: 100%;box-sizing: border-box;"  type="text" name="email" value=""  /></td>
                    </tr>
                    <tr>
                        <td>address</td>
                        <td>
                            <textarea class="EditUser" style="width: 100%;box-sizing: border-box;" name="address" rows="4" cols="20"></textarea>
                        </td>
                    </tr>
                    <tr>
                        <td>password</td>
                        <td>
                            <button style="width: 100%;box-sizing: border-box;">Reset</button>
                        </td>
                    </tr>
                    <tr>
                        <td>writable</td>
                        <td>
                            <select style="width: 100%;box-sizing: border-box;" class="EditUser" name="writable">
                                <option value="true">true</option>
                                <option  value="false">false</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>enable</td>
                        <td>
                            <select style="width: 100%;box-sizing: border-box;" class="EditUser" name="enable">
                                <option value="true">true</option>
                                <option  value="false">false</option>
                            </select>
                        </td>
                    </tr>
                </table>

            </div>
        </body>

    </html>
    <?php
} else {
    header('Location: ../../Auth/index.php');
}