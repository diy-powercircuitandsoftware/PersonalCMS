<?php
session_start();
include_once '../../../../../Class/DB/Config/DB/Config.php';
include_once '../../../../../Class/DB/Config/DB/Software.php';
include_once '../../../../../Class/DB/Com/User/SessionManager.php';
include_once '../../../../../Class/DB/Com/User/Profile.php';
include_once '../../../../../Class/DB/Com/Blog/Manager.php';
include_once '../../../../../Class/DB/Com/Category/Viewer.php';
include_once '../../../../../Class/DB/Com/Events/Viewer.php';
include_once '../../../../../Class/DB/Com/Module/LoadModule.php';
include_once '../../../../../Class/DB/Com/User/LoadModule.php';
include_once '../../../../../Class/FileIO/VirtualDirectory.php';
$DBConfig = new Config_DB_Config();
$SC = new Config_DB_Software($DBConfig);
$Sess = new Com_User_SessionManager($DBConfig);
$User = new Com_User_Profile($DBConfig);
$BlogManager = new Com_Blog_Manager($DBConfig);
$Category = new Com_Category_Viewer($DBConfig);
$Event = new Com_Events_Viewer($DBConfig);
$Module = new Com_Module_LoadModule($DBConfig);
$UModule = new Com_User_LoadModule($DBConfig);
$DBConfig->Open();
if ($SC->Online() && isset($_SESSION["UserID"]) && $Sess->Registered(session_id())) {
    ?>
    <!DOCTYPE html>
    <html>
        <head>
            <meta charset="UTF-8">
            <title><?php echo $SC->GetName(); ?></title>
           <link rel="stylesheet" type="text/css" href="../../../../../css/HolyGrail.css">
            <link rel="stylesheet" type="text/css" href="../../../../../css/PersonalCMS.css">

            <?php
            foreach ($UModule->LoadModule($_SESSION["UserID"], Com_User_LoadModule::Layout_Head) as $value) {
                try {
                    include_once '../../../../../Class/DB/UserModule/' . $value["filename"];
                    $mod = new $value["classname"]($UModule);
                    $mod->LoadConfig($value["config"]);
                    echo $mod->Execute();
                } catch (Exception $ex) {
                    
                }
            }
            ?>
            <script src="../../../../js/dom/SSQueryFW.js"></script>
            <script src="../../../../js/dom/SuperDialog.js"></script>
            <script src="../../../../js/dom/SearchBox.js"></script>
            <script>
                var ss = new SSQueryFW();
                ss.DocumentReady(function () {
                    var sd = new SuperDialog();
                    var BlogSB = document.getElementById("SearchBox").appendChild(new SearchBox());
                    BlogSB.Input = function (v) {
                        ss.Get("../../../Api/Ajax/Keyword/SearchKeyword.php", {"Keyword": v}, function (data) {
                            data = JSON.parse(data);
                            for (var i = 0; i < data.length; i++) {
                                BlogSB.AddList(data[i]["id"], data[i]["name"]);
                            }
                        });
                    };

                    BlogSB.CallbackValue = function (v) {
                        ss.S("#SearchRS,#HtmlReadable").Empty();
                        wsl.Param["KeywordID"] = v;
                        wsl.Param["StartID"] = 0;
                        wsl.Lock = false;
                        wsl.LoadData();
                    };
                    BlogSB.Action = function (v) {
                        ss.S("#SearchRS,#HtmlReadable").Empty();
                        //  wsl.Param["Keyword"] = v;
                        // wsl.Param["StartID"] = 0;
                        // wsl.Lock = false;
                        // wsl.LoadData();
                    };





                    var wsl = ss.WindowScrollLoad();
                    wsl.URL = "../../../Api/Ajax/Blog/SearchBlogUsingKeywordID.php";
                    wsl.Param = {"StartID": 0};
                    wsl.Done = (function (data) {
                        data = JSON.parse(data);
                        for (var i = 0; i < data.length; i++) {
                            var q = ss.S("#SearchRS").Append('<div class="BlogList"></div>');
                            if (data[i]["haspassword"] === undefined || data[i]["haspassword"] == "0") {
                                q.Append("<h3></h3>").Append("<a class='LinkOpen'></a>").Url("View.php?id=" + data[i]["id"]).Append(data[i]["title"]);
                            } else {
                                q.Append("<h3></h3>").Append("<a class='LinkOpen' href='#' data-password='1' ></a>").Data("id", data[i]["id"]).Append(data[i]["title"]);
                            }
                            q.Append(data[i]["description"]);
                            wsl.Param["StartID"] = Math.max(parseInt(data[i]["id"]), wsl.Param["StartID"]);
                        }
                        wsl.Lock = false;
                    });

                    wsl.AddEventListener();

                });
            </script>
        </head>
        <body  class="HolyGrail">
            <div id="Header">
                <div style="width: 50%;">
                    <a href="../index.php">
                        <img  src="../../../../../File/Resource/Logo.png"/>
                    </a>
                </div>
                <div  style="width: 50%;text-align: right;">
                    <?php
                    $Dat = $User->GetBasicUserData($_SESSION["UserID"]);
                    printf('<img  src="../../../Api/Action/Profile/GetUserIcon.php?id=%s" />', $Dat["userid"]);
                    echo '<span>' . $Dat["alias"] . '</span>';
                    ?>
                    <a href="../Config/Config.php">Config</a>
                    <a href="../../../Session/Action/Logout.php">Logout</a>
                </div>
            </div>
            <div class="Container">
                <div class="Nav">
                    <div class="BorderBlock" style="margin-top: 1px;">
                        <span class="Title" style="display: block;">Audio</span>
                        <ul>
                            <li><a href="../Audio/Player.php">Player</a></li>
                            <li><a href="../Audio/PlayList.php">PlayList</a></li>
                        </ul>
                    </div>
                    <div class="BorderBlock" style="margin-top: 1px;">
                        <span class="Title" style="display: block;">Blog</span>
                        <ul>
                            <li><a href="Manage.php">Manage</a></li>
                            <li style="font-weight: bold;">View</li>
                        </ul>
                    </div>

                    <div class="BorderBlock" style="margin-top: 1px;">
                        <span class="Title" style="display: block;">Event</span>
                        <ul>
                            <li><a href="../Event/Manage.php">Manage</a></li>
                            <li><a href="../Event/View.php">View</a></li>
                        </ul>
                    </div>
                    <div class="BorderBlock" style="margin-top: 1px;">
                        <span class="Title" style="display: block;">Files</span>
                        <ul>
                            <li><a href="../Files/Manager.php">Manager</a></li>
                            <li><a href="../Files/Temp.php">Temp</a></li>
                            <li><a href="../Files/Trash.php">Trash</a></li>
                        </ul>
                    </div>
                    <div class="BorderBlock" style="margin-top: 1px;">
                        <span class="Title" style="display: block;">Office</span>
                        <ul>
                            <li><a href="../Office/FinFin/MainPage.php">FinFin</a></li>
                            <li><a href="../Office/FlowFlow/MainPage.php">FlowFlow</a></li>
                            <li><a href="../Office/Image/MainPage.php">Image</a></li>
                            <li><a href="../Office/PointPoint/MainPage.php">PointPoint</a></li>
                            <li><a href="../Office/Statistics/MainPage.php">Statistics</a></li>
                            <li><a href="../Office/WordWord/MainPage.php">WordWord</a></li>
                            <li><a href="../Office/WYSIWYG/NewDoc.php">WYSIWYG</a></li>
                            <li><a href="../Office/XCell/MainPage.php">XCell</a></li>
                            <li><a href="../Office/XCess/MainPage.php">XCess</a></li>
                        </ul>
                    </div>
                    <div class="BorderBlock" style="margin-top: 1px;">
                        <span class="Title" style="display: block;">Photo</span>
                        <ul>
                            <li><a href="../Photo/ImageSlider.php">ImageSlider</a></li>
                            <li><a href="../Photo/PlayList.php">PlayList</a></li>
                        </ul>
                    </div>
                    <div class="BorderBlock" style="margin-top: 1px;">
                        <span class="Title" style="display: block;">Share</span>
                        <ul>
                            <li><a href="../Share/BlogViewer.php">Blog</a></li>
                            <li><a href="../Share/EventViewer.php">Event</a></li>
                        </ul>
                    </div>
                    <?php
                    $Dat = array_merge($Module->LoadModule(Com_Module_LoadModule::Layout_Nav, Config_DB_Config::Access_Mode_Members), $Module->LoadModule(Com_Module_LoadModule::Layout_Nav, Config_DB_Config::Access_Mode_Public));
                    foreach ($Dat as $value) {
                        try {
                            echo ' <div class="BorderBlock" style="margin-top: 3px;" >';
                            include_once '../../../../../Class/DB/Module/' . $value["filename"];
                            $mod = new $value["classname"]($Module);
                            printf('<label class="Title">%s</label>', $mod->GetTitle());
                            $mod->SetModuleID($value["id"]);
                            $mod->SetModulePage("../Module/Page.php");
                            $mod->SetUserID($_SESSION["UserID"]);
                            echo $mod->Execute();
                            echo '</div>';
                        } catch (Exception $ex) {
                            
                        }
                    }
                    ?>
                </div>
                <div class="Section">
                    <div id="SearchBox" class="BorderBlock" style="width:100%;">

                    </div>
                    <div id="SearchRS">
                        <?php
                        if (isset($_GET["Category"])) {
                            foreach ($BlogManager->GetBlogListByCategoryID($_SESSION["UserID"], $_GET["Category"]) as $value) {
                                printf('<div class="BlogList"><h3><a class="LinkOpen" href="View.php?id=%s">%s</a></h3>%s</div>', $value["id"], $value["title"], $value["description"]);
                            }
                        } else if (!isset($_GET["id"])) {
                            foreach ($BlogManager->GetSimpleLastBlogList($_SESSION["UserID"]) as $value) {
                                printf('<div class="BlogList"><h3><a class="LinkOpen" href="View.php?id=%s">%s</a></h3>%s</div>', $value["id"], $value["title"], $value["description"]);
                            }
                        }
                        ?>
                    </div>

                    <div id="HtmlReadable" style="height: 100%;" >
                        <?php
                        if (isset($_GET["id"])) {
                            printf('<iframe style="%s" src="../../../Api/Action/Blog/ReadBlog.php?id=%s"></iframe>', "width: 100%;height: 100%;box-sizing: border-box;", $_GET["id"]);
                        }
                        ?>
                    </div>
                </div>
                <div class="Aside">
                    <div class="BorderBlock" style="margin-top: 1px;">
                        <label class="Title">View</label>
                        <a href="View.php">View Last Blog</a>
                    </div>
                    <div class="BorderBlock" style="margin-top: 1px;">
                        <label class="Title">Category</label>
                        <ul>
                            <?php
                            foreach ($Category->GetAllCategory() as $value) {
                                printf('<li><a href="View.php?Category=%s">%s</a></li>', $value["id"], $value["name"]);
                            }
                            ?>
                        </ul>
                    </div>

                    <div class="BorderBlock" style="margin-top: 1px;">
                        <label class="Title">My Event</label>
                        <?php
                        foreach ($Event->GetCurrentMyEvent($_SESSION["UserID"]) as $value) {
                            echo '<div  >';
                            printf('<a href="../Event/View.php?id=%s"><span style="font-weight: bold;">%s</span>', $value["id"], $value["name"]);
                            printf('<div style="color: black;" >%s</div></a>', $value["description"]);
                            echo '</div><hr>';
                        }
                        ?>
                    </div>
                    <div class="BorderBlock" style="margin-top: 1px;">
                        <label class="Title">Other Event</label>
                        <?php
                        $Dat = array_merge($Event->GetCurrentEventNotUserID(Config_DB_Config::Access_Mode_Members, $_SESSION["UserID"]), $Event->GetCurrentEventNotUserID(Config_DB_Config::Access_Mode_Public, $_SESSION["UserID"]));
                        foreach ($Dat as $value) {
                            echo '<div  >';
                            printf('<a href="../Share/EventViewer.php?id=%s"><span style="font-weight: bold;">%s</span>', $value["id"], $value["name"]);
                            printf('<div style="color: black;" >%s</div></a>', $value["description"]);
                            echo '</div><hr>';
                        }
                        ?>
                    </div>
                    <?php
                    $Dat = array_merge($Module->LoadModule(Com_Module_LoadModule::Layout_Aside, Config_DB_Config::Access_Mode_Members), $Module->LoadModule(Com_Module_LoadModule::Layout_Aside, Config_DB_Config::Access_Mode_Public));
                    foreach ($Dat as $value) {
                        try {
                            echo ' <div class="BorderBlock" style="margin-top: 3px;" >';
                            include_once '../../../../../Class/DB/Module/' . $value["filename"];
                            $mod = new $value["classname"]($Module);
                            printf('<label class="Title">%s</label>', $mod->GetTitle());
                            $mod->SetModuleID($value["id"]);
                            $mod->SetModulePage("../Module/Page.php");
                            $mod->SetUserID($_SESSION["UserID"]);
                            echo $mod->Execute();
                            echo '</div>';
                        } catch (Exception $ex) {
                            
                        }
                    }
                    ?>

                </div>
        </body>
    </html>
    <?php
} else {
    header("location: ../../../../Auth/Login.php");
    session_destroy();
}
