<?php
session_start();
include_once '../../../../../../Class/Core/Config/Config.php';
include_once '../../../../../../Class/Core/UI/NAV.php';
include_once '../../../../../../Class/Core/Module/Database.php';
include_once '../../../../../../Class/Com/Event/Database.php';
include_once '../../../../../../Class/Com/Event/Reader.php';
include_once '../../../../../../Class/Com/Blog/Database.php';
include_once '../../../../../../Class/Com/Category/Database.php';
include_once '../../../../../../Class/SDK/Module/Basic.php';
include_once '../../../../Auth/Action/VerifySession.php';

$config = new Config();
$uinav = new UINAV();
$module = new Module_Database($config);
$category = new Category_Database($config);
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
            <title><?php echo basename(__FILE__, ".php"); ?></title>
            <link rel="stylesheet" type="text/css" href="../../../../../css/HolyGrail.css">
            <link rel="stylesheet" type="text/css" href="../../../../../css/PersonalCMS.css">
            <style>
                .BlogList{
                    margin-top: 1px;
                    border-style: solid;
                    border-width: thin;
                }
            </style>
            <?php
            foreach ($modlist as $value) {
                echo $value->Execute(Module_SDK_Basic::Layout_Head);
            }
            ?>
            <script src="../../../../../js/io/Ajax.js"></script>
            <script src="../../../../../js/dom/SSQueryFW.js"></script>
            <script src="../../../../../js/dom/SearchBox.js"></script>
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
                               ss.S("#SearchRS").Append('<div class="BlogList"><a class="MenuLink" href="View.php?id='+data[i]["id"]+'">'+data[i]["title"]+'</a>'+data[i]["description"]+'</div>');
                               
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
            <header class="Header">
                <div style="width: 50%;"></div>
                <div style="width: 50%;text-align: right;">
                    <?php
                    printf('<img src="../../../../Api/Action/Profile/GetUserIcon.php?id=%s"/>', $_SESSION["User"]["id"]);
                    printf('<span style="font-weight: bold;cursor: default;">%s</span>', $_SESSION["User"]["alias"]);
                    ?>       
                    <a class="MenuLink" style="display: inline;" href="../../../../Auth/Action/Logout.php">LogOut</a>
                </div>
            </header>
            <div class="HolyGrail-body">
                <nav>
                    <?php
                    foreach ($uinav->FindAllMenuFile("../../App") as $key => $valueA) {
                        echo '<div class="BorderBlock">';
                        printf(' <div class="TitleCenter">%s</div>', $key);
                        foreach ($valueA as $valueB) {
                             
 printf('  <a class="MenuLink" href="%s">%s</a>', "../../App/".$valueB["path"], $valueB["name"]);
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
                <main>
                    <div id="SearchBox" class="BorderBlock" style="width:100%;"></div>
                    <div id="SearchRS">
                        <?php
                        /* if (isset($_GET["Category"])) {
                          foreach ($BlogManager->GetBlogListByCategoryID($_SESSION["UserID"], $_GET["Category"]) as $value) {
                          printf('<div class="BlogList"><h3><a class="LinkOpen" href="View.php?id=%s">%s</a></h3>%s</div>', $value["id"], $value["title"], $value["description"]);
                          }
                          } else if (!isset($_GET["id"])) {
                          foreach ($BlogManager->GetSimpleLastBlogList($_SESSION["UserID"]) as $value) {
                          printf('<div class="BlogList"><h3><a class="LinkOpen" href="View.php?id=%s">%s</a></h3>%s</div>', $value["id"], $value["title"], $value["description"]);
                          }
                          } */
                        ?>
                    </div>

                    <div id="HtmlReadable" style="height: 100%;" >
                        <?php
                        if (isset($_GET["id"])) {
                                printf('<iframe style="%s" src="../../../../Api/ShareAction/Blog/ReadBlog.php?id=%s"></iframe>', "width: 100%;height: 100%;box-sizing: border-box;", $_GET["id"]);
                        }
                        ?>
                    </div>
                </main>
                <aside>
                    <div class="BorderBlock" style="margin-top: 1px;">
                        <div class="TitleCenter">View</div>
                        <a class="MenuLink" href="View.php">View Last Blog</a>
                    </div>
                    <div class="BorderBlock" style="margin-top: 1px;">
                        <div class="TitleCenter">Category</div>

                        <?php
                        foreach ($category->GetAllCategory() as $value) {
                            printf('<a class="MenuLink" href="View.php?Category=%s">%s</a>', $value["id"], $value["name"]);
                        }
                        ?>

                    </div>
                    <div class="BorderBlock" style="margin-top: 1px;">
                        <div class="TitleCenter">Event</div>
                        <?php
                        foreach ($event->GetComingEvent(Event_Database::Access_Member) as $value) {
                            echo '<div>';
                            printf('<a href="../Event/View.php?id=%s"><span style="font-weight: bold;">%s</span>', $value["id"], $value["name"]);
                            printf('<div style="color: black;" >%s</div></a>', $value["description"]);
                            echo '</div><hr>';
                        }
                        ?>
                    </div>
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
                </aside>

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
    header("location: ../../../../Auth/Login.php");
    session_destroy();
}
