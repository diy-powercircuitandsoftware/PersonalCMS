<?php
include_once '../../../../Class/Core/Config/Config.php';
include_once '../../../../Class/Core/UI/NAV.php';
include_once '../../../../Class/Com/Blog/Database.php';
include_once '../../../../Class/Com/Blog/Reader.php';
include_once '../../../../Class/Core/Module/Database.php';
include_once '../../../../Class/SDK/Module/Basic.php';
//include_once '../../../../Class/DB/Com/Events/Viewer.php';

$config = new Config();
$uinav = new UINAV();
$module = new Module_Database($config);
//$Blog = new Com_Blog_Viewer($DBConfig);
//$Event = new Com_Events_Viewer($DBConfig);

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
            <link rel="stylesheet" type="text/css" href="App/css/Page.css">
            <?php
            foreach ($modlist as $value) {
                echo $value->Execute(Module_SDK_Basic::Layout_Head);
            }
            ?>
        </head>
        <body>
            
            <header> 
                <h1 style="width: 100%;text-align: center;"><?php echo $config->GetName(); ?> Website</h1>
            </header>
            <div class="LMR157015">
                <div>
                    <nav>
                        <?php
                        foreach ($uinav->FindAllMenuFile("App") as $key => $valueA) {
                            echo '<div class="BorderBlock">';
                            printf(' <div class="TitleCenter">%s</div>', $key);
                            foreach ($valueA as $valueB) {
                                printf('  <a  class="MenuLink" href="%s">%s</a>', $valueB["path"], $valueB["name"]);
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
                </div>
                <div>
                    <div  style="text-align: center;border-style: dashed;background-color:#e9eaed ;">
                        <h2 >Sorry, this page isn't available</h2>
                        <h3>The link you followed may be broken, or the page may have been removed.</h3>
                        <img width="282" height="250" alt="" src="App/img/FBError.png">
                        <div style="">
                            <a  onclick="history.back();" href="#">Go back to the previous page</a>
                            <span>.</span>
                            <a  href="index.php">Go to the  Homepage</a>
                            <span>.</span>
                            <a  href="#">Visit the Help Center</a>
                        </div>
                    </div>
                    
                </div>
                <div>
                    
                </div>
            </div>
            <div class="Container">


            </div>
            <div class="Section">

                <div style="margin-top: 1px;">
                    <div style="width: 33%;border-style: solid;border-width: thin;">
                        <span  style="text-align: left;" class="Title">Last Blog</span>
                        <?php
                        $blog = $Blog->GetSimpleLastBlogList(Config_DB_Config::Access_Mode_Public);

                        foreach ($blog as $value) {
                            if (intval($value["haspassword"]) == 0) {
                                echo '<div style="border-style: solid;border-width: thin;margin-top: 1px;">';
                                printf('<h2><a href="Blog/index.php?id=%d">%s</a></h2>', intval($value["id"]), $value["title"]);
                                echo $value["description"];
                                echo '</div>';
                            }
                        }
                        if ($blog) {
                            echo '<div style="text-align: center;border-style: solid;border-width: thin;margin-top: 1px;"><a href="Blog/index.php">See More</a></div>';
                        }
                        ?>
                    </div>
                </div>


            </div>
            <div class="Aside" >
                <div class="BorderBlock">
                    <span  class="Title">Event</span>
                    <?php
                    foreach ($Event->GetCurrentEvent(Config_DB_Config::Access_Mode_Public) as $value) {
                        echo '<div  >';
                        printf('<a href="Event/index.php?id=%s"><span style="font-weight: bold;">%s</span>', $value["id"], $value["name"]);
                        printf('<div style="color: black;" >%s</div></a>', $value["description"]);
                        echo '</div><hr>';
                    }
                    ?>
                </div>
                <?php
                foreach ($Module->LoadModule(Com_Module_LoadModule::Layout_Aside, Config_DB_Config::Access_Mode_Public) as $value) {
                    try {
                        echo ' <div class="BorderBlock" style="margin-top: 3px;" >';
                        include_once '../../../../Class/DB/Module/' . $value["filename"];
                        $mod = new $value["classname"]($Module);
                        printf('<label class="Title">%s</label>', $mod->GetTitle());
                        $mod->SetModulePage("Module/Page.php");
                        $mod->SetModuleID($value["id"]);
                        echo $mod->Execute();
                        echo '</div>';
                    } catch (Exception $ex) {
                        
                    }
                }
                ?>

            </div>

            <footer>
                <span style="font-weight: bold;display: block;">
                    <?php
                    echo "&COPY;" . date("Y") . " " . $SC->GetName();
                    ?>
                </span>  
                <a href="../../../Root/index.php">Root</a>
            </footer>


        </body>
    </html>
    <?php
} else {
    header("location: ../../../../DefaultPages/Offline.php");
}
        