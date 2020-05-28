<?php
session_start();
include_once '../../../../Class/Core/Config/Config.php';
include_once '../../../../Class/Core/UI/NAV.php';
include_once '../../../../Class/Core/Module/Database.php';
include_once '../../../../Class/Com/Event/Database.php';
include_once '../../../../Class/Com/Event/Manager.php';
include_once '../../../../Class/SDK/Module/Basic.php';
include_once '../../Auth/Action/VerifySession.php';
$config = new Config();
$uinav = new UINAV();
$module = new Module_Database($config);
 
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
            <title><?php echo basename(__FILE__, ".php"); ?></title>
            <link rel="stylesheet" href="App/css/Page.css">
            <?php
            foreach ($modlist as $value) {
                echo $value->Execute(Module_SDK_Basic::Layout_Head);
            }
            ?>
        </head>
        <body> 
            <header id="mainheader">
                <div style="width: 50%;"></div>
                <div style="width: 50%;text-align: right;">
                    <?php
                    printf('<img src="../../Api/Action/Profile/GetUserIcon.php?id=%s"/>', $_SESSION["User"]["id"]);
                    printf('<span style="font-weight: bold;cursor: default;">%s</span>', $_SESSION["User"]["alias"]);
                    ?>       
                    <a style="font-weight: bold;" href="../../Auth/Action/Logout.php">LogOut</a>
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
                        foreach ($modlist as $value) {
                            if ($value->SupportLayout(Module_SDK_Basic::Layout_Nav)) {
                                echo ' <div class="BorderBlock" style="margin-top: ๅpx;" >';
                                printf('<div class="TitleCenter">%s</div>', $value->GetTitle());
                                echo $value->Execute(Module_SDK_Basic::Layout_Nav);
                                echo '</div>';
                            }
                        }
                        ?>     
                    </nav>

                </div>
                <div><h1>Main Page</h1></div>
                <div>
                    <?php
                    foreach ($modlist as $value) {
                            if ($value->SupportLayout(Module_SDK_Basic::Layout_Aside)) {
                                echo ' <div class="BorderBlock" style="margin-top: ๅpx;" >';
                                printf('<div class="TitleCenter">%s</div>', $value->GetTitle());
                                echo $value->Execute(Module_SDK_Basic::Layout_Aside);
                                echo '</div>';
                            }
                        }
                    ?>
                </div>
            </div>
        </body>
    </html>
    <?php
} else {
    header("location: ../../Auth/Login.php");
}