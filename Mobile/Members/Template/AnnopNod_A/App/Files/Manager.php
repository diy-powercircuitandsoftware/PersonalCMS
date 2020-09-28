<?php
session_start();
include_once '../../../../../../Class/Core/Config/Config.php';
include_once '../../../../../../Class/Core/UI/NAV.php';
include_once '../../../../../../Class/Core/Module/Database.php';
include_once '../../../../../../Class/Com/Event/Database.php';
include_once '../../../../../../Class/Com/Event/Reader.php';
include_once '../../../../../../Class/SDK/Module/Basic.php';
include_once '../../../../Auth/Action/VerifySession.php';
$config = new Config();
$uinav = new UINAV();
$module = new Module_Database($config);
$event = new Event_Reader(new Event_Database($config));
if ($config->IsOnline() && isset($_SESSION["User"])) {
    $modlist = array();
    foreach ($module->LoadModule(Module_Database::Access_Member) as $value) {

        include_once $module->ModulePath . $value["dirname"] . "/init.php";
        $cn = new $value["classname"]();
        $cn->SetUserID($_SESSION["User"]["id"]);
        $modlist[] = $cn;
    }
    ?>
    <!DOCTYPE html>
    <html>
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title><?php echo basename(__FILE__, ".php"); ?></title>
            <script src="../../../../../../Web/js/dom/SSQueryFW.js"></script>
            <script src="../../../../../../Web/js/dom/SearchBox.js"></script>
            <script src="../../../../../../Web/js/io/Ajax.js"></script>
            <link rel="stylesheet" type="text/css" href="../../../../../../Web/css/PersonalCMS.css">

            <?php
            foreach ($modlist as $value) {
                echo $value->Execute(Module_SDK_Basic::Layout_Head);
            }
            ?>
            <script>
                var ss = new SSQueryFW();
                ss.DocumentReady(function () {
                    ss.S("#BNShowHideMenu").Click(function () {
                        if (this.getAttribute("data-lock") == "1") {
                            ss.S("#Menu").Show();
                            this.setAttribute("data-lock", "0");
                        } else {
                            ss.S("#Menu").Hide();
                            this.setAttribute("data-lock", "1");
                        }


                    });
                });
            </script>
        </head>
        <body style="background-color: cornsilk;"> 
            <header>
                <div class="TitleCenter" style=" text-align: right;">
                    <a id="BNShowHideMenu" style="display: inline;"  class="MenuLink"  href="#">Menu</a>
                    <?php
                    printf('<span style="font-weight: bold;cursor: default;">%s</span>', $_SESSION["User"]["alias"]);
                    ?>       
                    <a style="display: inline;text-decoration: none;color: blue;" href="../../../../Auth/Action/Logout.php">LogOut</a>
                </div>
            </header>
            <nav id="Menu"  style="display: none;">
                <?php
                foreach ($uinav->FindAllMenuFile("../../App") as $key => $valueA) {
                    echo '<div class="MBorderBlock">';
                    printf(' <div class="TitleCenter">%s</div>', $key);
                    foreach ($valueA as $valueB) {
                        printf('  <a class="MenuLink" href="%s">%s</a>', "../../App/" . $valueB["path"], $valueB["name"]);
                    }
                    echo '</div>';
                }
                foreach ($modlist as $value) {
                    if ($value->SupportLayout(Module_SDK_Basic::Layout_Nav)) {
                        echo ' <div class="MBorderBlock" style="margin-top: 1px;" >';
                        printf('<div class="TitleCenter">%s</div>', $value->GetTitle());
                        echo $value->Execute(Module_SDK_Basic::Layout_Nav);
                        echo '</div>';
                    }
                }
                ?>     
            </nav>
            <main  style="border-style: solid;border-width: thin;">
                <div>
                    <a style="display: inline;" class="MenuLink" href="#">Home</a>
                    <a style="display: inline;" class="MenuLink" href="#">Open</a>
                    <a style="display: inline;" class="MenuLink" href="#">Download</a>
                    <?php
                    if ($_SESSION["User"]["writable"] == 1) {
                        ?>
                        <a style="display: inline;" class="MenuLink" href="#">Cut</a>
                        <a style="display: inline;" class="MenuLink" href="#">Copy</a>
                        <a style="display: inline;" class="MenuLink" href="#">Paste</a>
                        <a style="display: inline;" class="MenuLink" href="#">Delete</a>
                        <a style="display: inline;" class="MenuLink" href="#">Rename</a>
                        <a style="display: inline;" class="MenuLink" href="#">Share</a>
                        <a style="display: inline;" class="MenuLink" href="#">Upload</a>
                        <?php
                    }
                    ?>

                </div>
                <div>

                </div>
            </main>
            <aside>

                <?php
                foreach ($modlist as $value) {
                    if ($value->SupportLayout(Module_SDK_Basic::Layout_Aside)) {
                        echo ' <div class="MBorderBlock" style="margin-top: 1px;" >';
                        printf('<div class="TitleCenter">%s</div>', $value->GetTitle());
                        echo $value->Execute(Module_SDK_Basic::Layout_Aside);
                        echo '</div>';
                    }
                }
                ?>
            </aside>
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
    header("location: ../../../../Auth/Login.php");
}