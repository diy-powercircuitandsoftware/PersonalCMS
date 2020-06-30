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
            <title><?php echo basename(__FILE__, ".php"); ?></title>
          <link rel="stylesheet" type="text/css" href="../css/HolyGrail.css">
            <link rel="stylesheet" type="text/css" href="../css/PersonalCMS.css">
        </head>
        <body class="HolyGrail"> 
            
            <header class="Header">
                <div style="width: 50%;"></div>
                <div style="width: 50%;text-align: right;">
                    <span style="font-weight: bold;cursor: default;">Root</span>
                    <a  class="MenuLink" style="display: inline" href="Auth/ExitRoot.php">Exit</a>
                </div>
            </header>
            <div class="HolyGrail-body">
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
                 
                <main><h1>Main Page</h1></main>
                <aside></aside>
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
    header("location: Auth/index.php");
}