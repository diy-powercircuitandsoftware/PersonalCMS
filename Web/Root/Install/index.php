<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <meta charset="UTF-8">
        <title>Install</title>
        <script src="../../js/io/Ajax.js"></script>
        <script src="../../js/dom/SSQueryFW.js"></script>
        <script src="../../js/dom/FilesList.js"></script>
        <script>
            var ss = new SSQueryFW();
            ss.DocumentReady(function () {
                var ajax = new Ajax();
                var fl = new FilesList(document.getElementById("FileRS"));


                fl.OpenDir(function (v) {
                    ajax.Post("Action/ListDIR.php", {"Path": v}, function (data) {
                        fl.Clear();
                        data = JSON.parse(data);
                        for (var i in data) {
                            fl.AddDir(data[i]["name"], data[i]["realpath"], "-");
                        }

                        ss.S("#CHDIRList").Html((v));

                        fl.RemoveEditable();
                    });
                });
                fl.OpenDir("/");
                ss.S("#BNInstall").Click(function () {

                    var tin = ss.S(".TXTInput").ValByName();

                    tin.path = fl.GetSelectFiles(0) || "";

                    ajax.Post("Action/Install.php", tin, function (data) {
                        alert(data);
                    });
                });
//

            });

        </script>
    </head>
    <body style="background-color: cornsilk; ">

        <form action="Action/Install.php" method="POST" >
            <h1 style="text-align: center;">Install</h1>
            <div id="CHDIRList"></div>
            <div id="FileRS">

            </div>

            <?php
            $path = "../../../Class/Core/Config/";
            if (is_writable($path)) {
                echo '<div style="border-style: solid;border-width: thin;">
                <label>name:</label>
                  <input class="TXTInput" type="text" name="name" value="" />
                 <label>Password:</label>
                <input class="TXTInput" type="password" name="password" value="" />
                <input id="BNInstall" type="button" value="Install" />
                <a href="../../index.php"><input type="button" value="Back" /></a>
            </div>';
            } else {
                echo '<label>can not write config file(Class/Core/Config)</label>';
            }
            ?>



        </form>

    </body>
</html>
