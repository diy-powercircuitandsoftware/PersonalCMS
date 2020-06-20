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
            <title><?php echo basename(__FILE__,".php");  ?></title>
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
                    ss.S("#BNSave").Click(function () {
                        var u = dialog.Confirm("Save!",function () {
                            var json = ss.S(".AjaxChangeValue").ValByName();
                            ajax.Post("Action/SaveBasicConfig.php", { "data": json}, function (data) {
                                if (data === "1") {
                                    u.Close();
                                }
                            });

                        }).ZIndex(999);
                    });

                    ss.S("#BNCHPW").Click(function () {
                         
                        var tl = dialog.ChangePassword(function (v) {
                            console.log(v);
                            ajax.Post("Action/ChangePassword.php", v, function (data) {
                                if (data === "1") {
                                    tl.Close();
                                }
                            });

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

                </div>
                <div>

                </div>
            </div>

        </body>
    </html>
    <?php
} else {
    header('Location: ../../Auth/index.php');
}