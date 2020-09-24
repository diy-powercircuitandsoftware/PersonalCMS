<?php
include_once '../../../../Class/Core/Config/Config.php';
include_once '../../../../Class/Core/UI/NAV.php';
include_once '../../../../Class/Com/Blog/Database.php';
include_once '../../../../Class/Com/Blog/Reader.php';
include_once '../../../../Class/Com/Event/Database.php';
include_once '../../../../Class/Com/Event/Reader.php';
include_once '../../../../Class/Core/Module/Database.php';
include_once '../../../../Class/SDK/Module/Basic.php';
$config = new Config();
$uinav = new UINAV();
$module = new Module_Database($config);
$blog = new Blog_Reader(new Blog_Database($config));
$event = new Event_Reader(new Event_Database($config));
if ($config->IsOnline()) {
    $modlist = array();
    foreach ($module->LoadModule(Module_Database::Access_Public) as $value) {
        include_once $module->ModulePath . $value["dirname"] . "/init.php";
        $modlist[] = new $value["classname"]();
    }
    ?>
    <!DOCTYPE html>
    <html>
        <head>
            <meta charset="UTF-8">
            <title><?php echo $config->GetName(); ?></title>

            <link rel="stylesheet" type="text/css" href="../../../../Web/css/PersonalCMS.css">
            <style>
                .DivList{
                    border-style: solid;
                }
            </style>
            <?php
            foreach ($modlist as $value) {
                echo $value->Execute(Module_SDK_Basic::Layout_Head);
            }
            ?>
        </head>
        <body >

            <header> 
                <h1 style="width: 100%;text-align: center;"><?php echo $config->GetName(); ?> Website</h1>
            </header>
            <nav>
                <?php
                foreach ($uinav->FindAllMenuFile("App") as $key => $valueA) {
                    echo '<div class="BorderBlock">';
                    printf(' <div class="TitleCenter">%s</div>', $key);
                    foreach ($valueA as $valueB) {
                        printf('  <a  class="MenuLink" href="%s">%s</a>', "App/" . $valueB["path"], $valueB["name"]);
                    }
                    echo '</div>';
                }
                ?>

                <div class="BorderBlock" style="margin-top: 1px;">
                    <div class="TitleCenter">Template</div>
                    <?php
                    foreach ($uinav->FindAllTemplate("../") as $key => $value) {
                        printf('  <a  class="MenuLink" href="%s">%s</a>', $value, $key);
                    }
                    ?>
                </div>
                <?php
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
            <main>
                <div>
                    <div  style="text-align: left;" class="TitleCenter">Last Blog</div>
                    <?php
                    foreach ($blog->GetLastBlogList(Blog_Database::Access_Public) as $value) {
                        echo '<div class="DivList">';
                        printf('<a class="MenuLink" href="App/Blog/Viewer.php?id=%d"><h2>%s</h2></a>', intval($value["id"]), $value["title"]);
                        echo $value["description"];
                        echo '</div>';
                    }
                    ?>
                </div>
                <div>
                    <div  style="text-align: left;" class="TitleCenter">Last Files</div>
                </div>
                <div>
                    <div  style="text-align: left;" class="TitleCenter">Welcome</div>
                </div>

            </main>
            <aside>
                <?php
                echo '<div class="BorderBlock" style="margin-top: 1px;">';
                echo '  <div class="TitleCenter">Event</div>';
                foreach ($event->GetComingEvent(Event_Database::Access_Public) as $value) {
                    echo '<div>';
                    printf('<a class="MenuLink" href="Event/index.php?id=%s"><span style="font-weight: bold;">%s</span>', $value["id"], $value["name"]);
                    printf('<div style="color: black;" >%s</div></a>', $value["description"]);
                    echo '</div><hr>';
                }
                echo '</div>';
                foreach ($modlist as $value) {
                    if ($value->SupportLayout(Module_SDK_Basic::Layout_Aside)) {
                        echo ' <div class="BorderBlock" style="margin-top: ๅpx;" >';
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
    header("location: ../../../../DefaultPages/Offline.php");
}
        