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
        <script>
            var ss = new SSQueryFW();
            ss.DocumentReady(function () {
                var ajax = new Ajax();


                ss.S("#TXTDataPath").KeyUp(function () {
                    ajax.Post("Action/SimulationDataPath.php", {"Path": this.value}, function (s) {
                         ss.S("#Simpath").Html(s);
                    });
                });

            });

        </script>
    </head>
    <body style="background-color: cornsilk; ">

        <form action="Action/Install.php" method="POST">
            <div style="transform: translate(-50%,-50%);
                 position:absolute;
                 top:50%;
                 left:50%;">
                <table style=" border-style: solid;">
                    <thead>
                        <tr>
                            <th colspan="2">Install</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Password:</td>
                            <td><input style="width: 100%;box-sizing: border-box;" type="text" name="Password" value="" /></td>
                        </tr>
                        <tr>
                            <td>Data:</td>
                            <td>
                                <input style="width: 100%;box-sizing: border-box;"  id="TXTDataPath" type="text" name="DataPath" value="DefaultFiles" />
                                <div id="Simpath" style="word-wrap: break-word;">

                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2"><input type="submit" value="Add" style="width: 100%;" /></td>
                        </tr>
                    </tbody>

                </table>
                <?php
                if (isset($_GET["error"])) {
                    echo 'Error:' . $_GET["error"];
                }
                ?>
            </div>
        </form>

    </body>
</html>
