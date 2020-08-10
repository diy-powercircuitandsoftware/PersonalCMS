<?php
include_once '../../../../../../Class/Core/Config/Config.php';
include_once '../../../../../../Class/Core/UI/NAV.php';
include_once '../../../../../../Class/Core/Module/Database.php';
include_once '../../../../../../Class/Core/User/Database.php';
include_once '../../../../../../Class/Core/User/Member.php';
include_once '../../../../../../Class/Com/Blog/Database.php';
include_once '../../../../../../Class/Com/Blog/Reader.php';
include_once '../../../../../../Class/Com/Category/Database.php';
include_once '../../../../../../Class/Com/Event/Database.php';
include_once '../../../../../../Class/Com/Event/Reader.php';
include_once '../../../../../../Class/SDK/Module/Basic.php';
$config = new Config();
$uinav = new UINAV();
$module = new Module_Database($config);
$blog = new Blog_Reader(new Blog_Database($config));
$event = new Event_Reader(new Event_Database($config));
$user = new User_Member(new User_Database($config));
$category = new Category_Database($config);
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
            <link rel="stylesheet" type="text/css" href="../../../../../css/HolyGrail.css">
            <link rel="stylesheet" type="text/css" href="../../../../../css/PersonalCMS.css">
            <script src="../../../../../js/dom/SSQueryFW.js"></script>
            <script src="../../../../../js/dom/SuperDialog.js"></script>
            <script src="../../../../../js/dom/SearchBox.js"></script>
            <script src="../../../../../js/io/Ajax.js"></script>
            <style>
                .BlogList{
                    margin-top: 1px;
                    border-style: solid;
                    border-width: thin;
                }
            </style>
            <script>
                var ss = new SSQueryFW();
                ss.DocumentReady(function () {
                    var ajax = new Ajax();
                    var AjaxSB = new AjaxScrollBar("../../../../Api/ShareAjax/Blog/SearchBlogUsingKeywordID.php");
                    var BlogSB = new SearchBox(document.getElementById("SearchBox"));
                    var lastid = 0;
                    BlogSB.ValueChange(function (v) {
                        ajax.Post("../../../../Api/Ajax/Category/SearchKeyword.php", {"Keyword": v}, function (data) {
                            data = JSON.parse(data);
                            for (var i = 0; i < data.length; i++) {
                                BlogSB.AddItem(data[i]["id"], data[i]["name"]);
                            }
                        });
                    });

                    BlogSB.Calllback(function (v) {
                        ss.S("#SearchRS,#HtmlReadable").Empty();
                        lastid = 0;
                        AjaxSB.Param("id", v);
                        AjaxSB.Param("startid", lastid);
                        AjaxSB.LoadAjax();

                    });
                    BlogSB.Enter = (function (v) {
                        //  ss.S("#SearchRS,#HtmlReadable").Empty();
                        //  wsl.Param["Keyword"] = v;
                        // wsl.Param["StartID"] = 0;
                        // wsl.Lock = false;
                        // wsl.LoadData();
                    });

                    AjaxSB.AddScrollEvent(function (data) {
                        try {

                            data = JSON.parse(data);
                            for (var i in data) {
                                ss.S("#SearchRS").Append('<div class="BlogList"><a class="MenuLink" href="Viewer.php?id=' + data[i]["id"] + '">' + data[i]["title"] + '</a>' + data[i]["description"] + '</div>');

                                lastid = Math.max(lastid, data[i]["id"]);
                            }
                            AjaxSB.Param("startid", lastid);
                        } catch (e) {

                        }
                    });

                });
            </script>
        </head>
        <body  class="HolyGrail">


            <header> 
                <h1 style="width: 100%;text-align: center;"><?php echo $config->GetName(); ?> Website</h1>
            </header>
            <div class="HolyGrail-body">

                <nav>
                    <?php
                    foreach ($uinav->FindAllMenuFile("../../App") as $key => $valueA) {
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
                        foreach ($uinav->FindAllTemplate("../../../") as $key => $value) {
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
                    <div style="width: 100%;" id="SearchBox"></div>
                    <div id="SearchRS" >
                        <?php
                        if (isset($_GET["category"])) {

                            /* foreach ($Blog->GetBlogListByCategoryID(Config_DB_Config::Access_Mode_Public) as $value) {
                              if (isset($_SESSION["Blog_Password"][$value["id"]])) {
                              $value["haspassword"] = 0;
                              }
                              if (intval($value["haspassword"]) == "1") {
                              printf('<div class="BlogList"><h3><a class="LinkOpen" href="#" data-password="1" data-id="%s" >%s</a></h3>%s</div>', $value["id"], $value["title"], $value["description"]);
                              } else {
                              printf('<div class="BlogList"><h3><a class="LinkOpen" href="index.php?id=%s">%s</a></h3>%s</div>', $value["id"], $value["title"], $value["description"]);
                              }
                              } */
                        }  
                        ?>
                    </div>
                    <div id="HtmlReadable" style="height: 100%;">
                        <?php
                        if (isset($_GET["id"])) {
                            printf('<iframe style="%s" src="../../../../Api/ShareAction/Blog/ReadBlog.php?id=%s"></iframe>', "width: 100%;height: 100%;box-sizing: border-box;", $_GET["id"]);
                        }
                        ?>
                    </div>
                </main>

                <aside>
                    <div class="BorderBlock" style="margin-top: 1px;">
                        <div class="TitleCenter">User</div>
                        <?php
                        foreach ($user->GetUserList() as $value) {
                            printf('<a style="display:block;" href="?userid=%s">%s</a>', $value["id"], $value["alias"]);
                        }
                        ?>
                    </div>


                    <div class="BorderBlock" style="margin-top: 1px;">
                        <div class="TitleCenter">Category</div>
                        <div>
                            <?php
                            foreach ($category->GetAllCategory() as $value) {
                                printf('<a style="display:block;" href="?category=%s">%s</a>', $value["id"], $value["name"]);
                            }
                            ?>
                        </div>

                    </div>
                    <?php
                    echo '<div class="BorderBlock" style="margin-top: 1px;">';
                    echo '  <div class="TitleCenter">Event</div>';
                    foreach ($event->GetComingEvent(Event_Database::Access_Public) as $value) {
                        echo '<div>';
                        printf('<a href="Event/index.php?id=%s"><span style="font-weight: bold;">%s</span>', $value["id"], $value["name"]);
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
            </div>
            <footer>
                <span style="font-weight: 700;display: block;">
                    <?php
                    echo "&COPY;" . date("Y") . " " . $config->GetName();
                    ?>
                </span>
            </footer>

        </body>
    </html>
    <?php
} else {
    header("location: ../Error/Offline.php");
}