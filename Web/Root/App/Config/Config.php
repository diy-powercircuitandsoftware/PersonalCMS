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
            <script src="../../../js/dom/SuperDialog/Template/Basic/Personal.js"></script>
            <script src="../../../js/dom/SuperDialog/Template/Basic/MessageBox.js"></script>
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
                    ss.S("#BNSave").Click(function () {
                        var u = new SuperDialog_Template_MessageBox().Confirm("Save!", function () {
                            var json = ss.S(".AjaxChangeValue").ValByName();
                            ajax.Post("Action/SaveBasicConfig.php", {"data": json}, function (data) {
                                if (data === "1") {
                                    u.close();
                                }
                            });

                        }) ;
                    });

                    ss.S("#BNCHPW").Click(function () {

                        var tl = new SuperDialog_Template_Personal().ChangePassword(function (v) {
                            
                            ajax.Post("Action/ChangePassword.php", v, function (data) {
                                if (data === "1") {
                                    tl.close();
                                }
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
                    <span style="font-weight: bold;cursor: default;">Root</span>
                    <a  class="MenuLink" style="display: inline;"  href="../../Auth/ExitRoot.php">Exit</a>
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
                    <span style="font-weight: bold;">Software</span>
                    <table id="ConfigList">
                        <tr class="Title">
                            <th>Key</th>
                            <th>Value</th>
                        </tr>
                        <tr>
                            <td>Online:</td>
                            <td>
                                <select  class="AjaxChangeValue" style="width:99%;" name="online" >
                                    <option value="0">false</option>
                                    <option value="1"
                                    <?php
                                    if ($config->IsOnline()) {
                                        echo ' selected';
                                    }
                                    ?>>true</option>
                                </select>
                            </td>
                        </tr>


                        <tr>
                            <td>Name:</td>
                            <td>
                                <input class="AjaxChangeValue" style="width:99%;" type="text" name="name" value="<?php
                                echo $config->GetName();
                                ?>" />
                            </td>
                        </tr>

                        <tr>
                            <td>Password:</td>
                            <td>
                                <button style="width:99%;" id="BNCHPW">Change Password</button>
                            </td>
                        </tr>
                        <tr  >
                            <td>UserDIR:</td>
                            <td>
                                <input id="TXTUserDIR" class="AjaxChangeValue" type="text" name="data" value="<?php echo $config->GetDataPath(); ?>" />

                            </td>
                        </tr>
                    </table>
                    <div style="width: 95%;margin-left: auto;margin-right: auto;margin-top: 7px;">
                        <button id="BNSave">Save!</button>
                    </div>

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
        </body>
    </html>
    <?php
} else {
    header('Location: ../../Auth/index.php');
}