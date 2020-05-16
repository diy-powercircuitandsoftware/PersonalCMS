<?php
session_start();
include_once '../../../../../Class/DB/Config/DB/Config.php';
include_once '../../../../../Class/DB/Config/DB/Software.php';
include_once '../../../../../Class/DB/Com/Blog/Viewer.php';
include_once '../../../../../Class/DB/Com/Category/Viewer.php';
include_once '../../../../../Class/DB/Com/Events/Viewer.php';
include_once '../../../../../Class/DB/Com/Module/LoadModule.php';
$DBConfig = new Config_DB_Config();
$SC = new Config_DB_Software($DBConfig);
$Blog = new Com_Blog_Viewer($DBConfig);
$Category = new Com_Category_Viewer($DBConfig);
$Event = new Com_Events_Viewer($DBConfig);
$Module = new Com_Module_LoadModule($DBConfig);
$DBConfig->Open();
if ($SC->Online()) {
    ?>
    <!DOCTYPE html>
    <html>
        <head>
            <meta charset="UTF-8">
            <title><?php echo $SC->GetName() . ' Blog'; ?> </title>
            <link rel="stylesheet" type="text/css" href="../css/Page.css">
            <script src="../../../../js/dom/SSQueryFW.js"></script>
            <script src="../../../../js/dom/SuperDialog.js"></script>
            <script src="../../../../js/dom/SearchBox.js"></script>
            <style>

                </style>
                <script>
                    var SS = new SSQueryFW();


                    SS.DocumentReady(function () {
                        var BlogSB = document.getElementById("Blog_SearchBox").appendChild(new SearchBox());
                        var Dialog = new SuperDialog();
                            BlogSB.Input=function (v) {
                                SS.Get("../../../Api/ShareAjax/Keyword/SearchKeyword.php", {"Keyword": v}, function (data) {
                                data = JSON.parse(data);
                                for (var i = 0; i < data.length; i++) {
                                    BlogSB.AddList(data[i]["id"], data[i]["name"]);
                                }
                            });
                            };
                            BlogSB.CallbackValue=function (v) {
                                SS.S("#Blog_SearchRS,#HtmlReadable").Empty();
                            wsl.Param = {"KeywordID": v, "StartID": 0};
                            wsl.Lock = false;
                            wsl.LoadData();
                            };
                            BlogSB.Action=function (v) {
                                SS.S("#Blog_SearchRS,#HtmlReadable").Empty();
                                wsl.Param = {"KeywordID": v, "StartID": 0};
                                wsl.Lock = false;
                                wsl.LoadData();
                                };

                        var wsl = SS.WindowScrollLoad();
                        wsl.URL = "../../../Api/ShareAjax/Blog/SearchBlogUsingKeywordID.php";
                        wsl.Done=(function (data) {
                            data = JSON.parse(data);
                            for (var i = 0; i < data.length; i++) {
                                var rs = SS.S("#Blog_SearchRS").Append('<div  class="BlogList"></div>');
                                if (parseInt(data[i]["haspassword"]) == 1) {
                                    rs.Append("<h2></h2>").Append("<a data-password='1' href='#'></a>").Data("id", data[i]["id"]).Append(data[i]["title"]);
                                } else {
                                    rs.Append("<h2></h2>").Append("<a></a>").Url("index.php", {"id": data[i]["id"]}).Append(data[i]["title"]);
                                }
                                rs.Append("<span></span>").Append(data[i]["description"]);
                                wsl.Param["StartID"] = Math.max(parseInt(data[i]["id"]), wsl.Param["StartID"]);
                                    }
                                    wsl.Lock = false;
                                });
                                SS.S("#BNClearPassword").Click(function () {
                                        SS.Post("../../../Api/ShareAjax/Blog /ClearPassword.php", '', function (rs) {
                                            location.reload();
                                    });
                                });
                                SS.S("#Blog_SearchRS").Click(function (e) {
                                    if (e.target.getAttribute("data-password") == "1") {
                                        Dialog.Login(function (pw) {
                                            SS.Post("../../../Api/ShareAjax/Blog/AuthBlogID.php", {"ID": e.target.getAttribute("data-id"), "UserName": pw["UserName"], "Password": pw["Password"]}, function (data) {
                                                if (data == "1") {
                                                    window.location = "index.php?id=" + e.target.getAttribute("data-id");
                                                } else {
                                                    Dialog.Alert("Password Incorrect").ZIndex(1000);
                                                }
                                            });
                                        }).ZIndex(999);
                                    }
                                });

                        wsl.AddEventListener();
                    });
                </script>
            </head>
            <body >
                <div id="Header" style="position: static;">
                    <h1  style="width: 100%;text-align: center;"><?php echo $SC->GetName(); ?> Blog</h1>
                </div>
                <div class="Container">
                    <div class="Nav">
                        <div class="BorderBlock" style="margin-top: 1px;">
                            <label class="Title">About</label>
                            <a href="../About/index.php">About</a>
                        </div>
                        <div class="BorderBlock" style="margin-top: 1px;">
                            <label class="Title">App</label>
                            <a href="../App/index.php">Player</a>
                        </div>
                        <div class="BorderBlock" style="margin-top: 1px;">
                            <label class="Title">Blog</label>
                            <span style="font-weight: bold;">Viewer</span>
                        </div>
                        <div class="BorderBlock" style="margin-top: 1px;">
                            <label class="Title">Event</label>
                            <a href="../Event/index.php">Viewer</a>
                        </div>
                        <div class="BorderBlock" style="margin-top: 1px;">
                            <label class="Title">Files</label>
                            <a href="../Files/index.php">Viewer </a>
                        </div>

                        <div class="BorderBlock" style="margin-top: 1px;">
                            <label class="Title">Photo</label>
                            <a href="../Photo/ImageSlider.php">ImageSlider </a>
                        </div>
                        <div class="BorderBlock" style="margin-top: 1px;">
                            <label class="Title">Template</label>
                            <?php
                            $filelist = array_diff(scandir("../../"), array('.', '..'));
                            foreach ($filelist as $value) {
                                if (is_dir("../../" . $value)) {
                                    printf('<a style="display:block;" href="../../%s">%s</a>', $value, $value);
                                }
                            }
                            ?>

                        </div>
                        <div class="BorderBlock" style="margin-top: 1px;">
                            <label class="Title">User</label>
                           <a href="../../../../Members/Session/AuthUserID.php?tp=AnnopNod_A">Login</a>
                        </div>
                        <?php
                        foreach ($Module->LoadModule(Com_Module_LoadModule::Layout_Nav, Config_DB_Config::Access_Mode_Public) as $value) {
                        try {
                            echo ' <div class="BorderBlock" style="margin-top: 3px;" >';
                            include_once '../../../../../Class/DB/Module/' . $value["filename"];
                            $mod = new $value["classname"]($Module);
                            printf('<label class="Title">%s</label>', $mod->GetTitle());
                            $mod->SetModulePage("../Module/Page.php");
                            $mod->SetModuleID($value["id"]);
                            echo $mod->Execute();
                            echo '</div>';
                        } catch (Exception $ex) {
                                
                            }
                        }
                    ?>

                    </div>
                    <div class="Section">
                        <div>
                            <div style="width: 100%;" id="Blog_SearchBox" class="BorderBlock">

                            </div>
                            <div id="Blog_SearchRS" >
                                <?php
                                if (isset($_GET["Category"])) {

                                foreach ($Blog->GetBlogListByCategoryID(Config_DB_Config::Access_Mode_Public) as $value) {
                                    if (isset($_SESSION["Blog_Password"][$value["id"]])) {
                                        $value["haspassword"] = 0;
                                    }
                                    if (intval($value["haspassword"]) == "1") {
                                        printf('<div class="BlogList"><h3><a class="LinkOpen" href="#" data-password="1" data-id="%s" >%s</a></h3>%s</div>', $value["id"], $value["title"], $value["description"]);
                                    } else {
                                        printf('<div class="BlogList"><h3><a class="LinkOpen" href="index.php?id=%s">%s</a></h3>%s</div>', $value["id"], $value["title"], $value["description"]);
                                    }
                                }
                            } else if (!isset($_GET["id"])) {
                                $blog = $Blog->GetSimpleLastBlogList(Config_DB_Config::Access_Mode_Public);

                                foreach ($blog as $value) {
                                    if ($value["haspassword"] && !isset($_SESSION["Blog_Password"][$value["id"]])) {

                                        echo '<div class="BlogList">';
                                        printf('<h2><a data-password="1" data-id="%s" href="#">%s</a></h2>', intval($value["id"]), $value["title"]);
                                        echo $value["description"];
                                        echo '</div>';
                                    } else {
                                        echo '<div class="BlogList">';
                                        printf('<h2><a href="index.php?id=%d">%s</a></h2>', intval($value["id"]), $value["title"]);
                                        echo $value["description"];
                                        echo '</div>';
                                    }
                                }
                            }
                            ?>
                            </div>

                        </div>
                        <div id="HtmlReadable" style="height: 100%;">
                            <?php
                            if (isset($_GET["id"])) {
                            printf('<iframe style="%s" src="../../../Api/ShareAction/Blog/ReadBlog.php?id=%s"></iframe>', "width: 100%;height: 100%;box-sizing: border-box;", $_GET["id"]);
                        }
                        ?>
                        </div>
                    </div>
                    <div class="Aside">
                        <div class="BorderBlock">
                            <span  class="Title">Blog</span>
                                <a id="BNClearPassword" href="#" style="display: block;">ClearPassword</a>
                                    <a href="index.php" style="display: block;">GetLastBlog</a>
                            </div>
                        <div class="BorderBlock" style="margin-top: 1px;">
                            <label class="Title">Category</label>
                            <ul>
                                <?php
                                foreach ($Category->GetAllCategory() as $value) {
                                printf('<li><a href="index.php?Category=%s">%s</a></li>', $value["id"], $value["name"]);
                            }
                            ?>
                            </ul>
                        </div>
                        <div class="BorderBlock">
                            <span  class="Title">Event</span>
                            <?php
                            foreach ($Event->GetCurrentEvent(Config_DB_Config::Access_Mode_Public) as $value) {
                            echo '<div  >';
                            printf('<a href="../Event/index.php?id=%s"><span style="font-weight: bold;">%s</span>', $value["id"], $value["name"]);
                            printf('<div style="color: black;">%s</div></a>', $value["description"]);
                            echo '</div><hr>';
                        }
                        ?>
                        </div>
                        <?php
                        foreach ($Module->LoadModule(Com_Module_LoadModule::Layout_Aside, Config_DB_Config::Access_Mode_Public) as $value) {
                        try {
                            echo ' <div class="BorderBlock" style="margin-top: 3px;" >';
                            include_once '../../../../../Class/DB/Module/' . $value["filename"];
                            $mod = new $value["classname"]($Module);
                            printf('<label class="Title">%s</label>', $mod->GetTitle());
                            $mod->SetModulePage("../Module/Page.php");
                            $mod->SetModuleID($value["id"]);
                            echo $mod->Execute();
                            echo '</div>';
                        } catch (Exception $ex) {
                                
                            }
                        }
                    ?>
                    </div>
                </div>
                <div>
                    <span style="font-weight: 700;display: block;">
                        <?php
                        echo "&COPY;" . date("Y") . " " . $SC->GetName();
                    ?>
                    </span>
                </div>
            </body>
        </html>
        <?php
    } else {
    header("location: ../Error/Offline.php");
}