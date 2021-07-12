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

                    var ajax = new Ajax();
                    var AjaxSB = new Ajax_ScrollBar("../../../../../../Web/Members/Api/Ajax/Blog/Share/SearchBlogUsingKeywordID.php");
                    var BlogSB = new SearchBox(document.getElementById("SearchBox"));
                    var lastid = 0;
                    BlogSB.ValueChange(function (v) {
                        ajax.Post("../../../../../../Web/Members/Api/Ajax/Category/List/SearchKeyword.php", {"Keyword": v}, function (data) {
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
                                ss.S("#SearchRS").Append('<div class="BlogList"><a class="MenuLink" href="View.php?id=' + data[i]["id"] + '">' + data[i]["title"] + '</a>' + data[i]["description"] + '</div>');

                                lastid = Math.max(lastid, data[i]["id"]);
                            }
                            AjaxSB.Param("startid", lastid);
                        } catch (e) {

                        }
                    });
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
            <nav id="Menu" style="display: none;">
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
                        printf('<iframe style="%s" src="../../../../../../Web/Members/Api/Action/Blog/Share/ReadBlog.php?id=%s"></iframe>', "width: 100%;height: 100vh;box-sizing: border-box;", $_GET["id"]);
                    }
                    ?>
                </div>
            </main>
            <aside>
                <div class="MBorderBlock" style="margin-top: 1px;">
                    <div class="TitleCenter">Event</div>
                    <?php
                    foreach ($event->GetComingEvent(Event_Database::Access_Member) as $value) {
                        echo '<div>';
                        printf('<a class="MenuLink" href="../Event/View.php?id=%s"><span style="font-weight: bold;">%s</span>', $value["id"], $value["name"]);
                        printf('<div style="color: black;" >%s</div></a>', $value["description"]);
                        echo '</div><hr>';
                    }
                    ?>
                </div>
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