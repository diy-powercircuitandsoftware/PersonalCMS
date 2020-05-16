<?php
session_start();
include_once '../../Class/Core/Config/Config.php';
include_once '../../Class/Core/UI/NAV.php';
$config = new Config();
$uinav = new UINAV();
if (!$config->Installed()) {
    header("location: Install/index.php");
} else if ($config->HasRootAuth(session_id())) {
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
                    <span style="font-weight: bold;cursor: default;">Root</span>
                    <a style="font-weight: bold;" href="Auth/ExitRoot.php">Exit</a>
                </div>
            </header>
            <div class="LMR157015">
                <div>
                    <nav>
                        <?php
                        foreach ($uinav->FindAllMenuFile("App") as $key => $valueA) {
                            echo '<div class="BorderBlock">';
                            printf(' <div class="TitleCenter">%s</div>',$key);
                            foreach ($valueA as $valueB) {
                              printf('  <a class="MenuLink" href="%s">%s</a>',$valueB["path"],$valueB["name"]);
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
    header("location: Auth/index.php");
}