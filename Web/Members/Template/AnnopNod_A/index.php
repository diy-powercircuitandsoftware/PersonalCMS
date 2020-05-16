<?php
session_start();
include_once '../../../../Class/Core/Config/Config.php';
include_once '../../../../Class/Core/UI/NAV.php';
$config = new Config();
$uinav = new UINAV();
$hasauth = false;
if (isset($_SESSION["User"])) {
    if ($_SESSION["User"]["session_count"] == 0) {
        include_once '../../../../Class/Core/User/Database.php';
        include_once '../../../../Class/Core/User/Session.php';
        $session = new User_Session(new User_Database($config));
        if ($session->Registered(session_id())) {
            $_SESSION["User"]["session_count"] = 1;
            $hasauth = true;
        }
    } else {
        $_SESSION["User"]["session_count"] = ($_SESSION["User"]["session_count"] + 1) % 12;
        $hasauth = true;
    }
}
 
if ($config->IsOnline() && $hasauth) {
    ?>
    <!DOCTYPE html>
    <html>
        <head>
            <meta charset="UTF-8">
            <title>Root->MainPage</title>
            <link rel="stylesheet" href="App/css/Page.css">
        </head>
        <body> 
            <header id="mainheader">
                <div style="width: 50%;"></div>
                <div style="width: 50%;text-align: right;">
                    <?php
                        printf('<img src="../../Api/Action/Profile/GetUserIcon.php?id=%s"/>',$_SESSION["User"]["id"]);
                        printf('<span style="font-weight: bold;cursor: default;">%s</span>',$_SESSION["User"]["alias"]);
                    ?>       
                    <a style="font-weight: bold;" href="../../Auth/Action/Logout.php">Login</a>
                </div>
            </header>
            <div class="LMR157015">
                <div>
                    <nav>
                        <?php
                        foreach ($uinav->FindAllMenuFile("App") as $key => $valueA) {
                            echo '<div class="BorderBlock">';
                            printf(' <div class="TitleCenter">%s</div>', $key);
                            foreach ($valueA as $valueB) {
                                printf('  <a class="MenuLink" href="%s">%s</a>', $valueB["path"], $valueB["name"]);
                            }
                            echo '</div>';
                        }
                        ?>     
                    </nav>
                </div>
                <div><h1>Main Page</h1></div>
                <div></div>
            </div>
        </body>
    </html>
    <?php
} else {
    // header("location: Auth/index.php");
}