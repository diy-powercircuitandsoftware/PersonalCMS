<?php
session_start();
include_once '../../../Class/Core/Config/Config.php';
$config = new Config();
if ($config->Installed() && isset($_POST["Superuser"]) && isset($_POST["Password"])) {
    if (($config->CanAuth())) {
        if ($config->Auth($_POST["Superuser"], $_POST["Password"])) {
            if ($config->SessionRegister(session_id())) {
                header("location: ../index.php");
            } else {
                header("location: index.php?error='Session Register Error'");
            }
        } else {
            header("location: index.php?error='Password Error'");
        }
    } else {
        header("location: index.php?error='Can Not Auth Database Not Exists'");
    }
} else if (!$config->Installed()) {
    header("location: ../Install/index.php");
} else {
    ?>

    <!DOCTYPE html>
    <html>
        <head>
            <meta charset="UTF-8">
            <title>Login</title>
        </head>
        <body style="background-color: cornsilk;">
            <form action="index.php" method="POST">
                <div style="transform: translate(-50%,-50%);
                     position:absolute;
                     top:50%;
                     left:50%;">

                    <table style="border-style: solid;">
                        <thead>
                            <tr style="background-color: burlywood;">
                                <th colspan="2">Login</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Superuser:</td>
                                <td><input type="text" name="Superuser" style="width: 100%;box-sizing: border-box;" /></td>
                            </tr>
                            <tr>
                                <td>Password:</td>
                                <td><input type="password" name="Password" style="width: 100%;box-sizing: border-box;" /></td>
                            </tr>
                            <tr>
                                <td colspan="2"><input type="submit" value="Login" style="width: 100%;box-sizing: border-box;" /></td>
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
    <?php
}