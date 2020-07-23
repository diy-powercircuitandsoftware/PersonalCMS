<?php
session_start();
ob_start();
include_once '../../../../../Class/DB/Config/DB/Config.php';
include_once '../../../../../Class/DB/Config/DB/Software.php';
include_once '../../../../../Class/DB/Config/Api/Place.php';
include_once '../../../../../Class/DB/Com/User/SessionManager.php';
include_once '../../../../../Class/DB/Com/User/Profile.php';
include_once '../../../../../Class/DB/Com/Events/Manager.php';
include_once '../../../../../Class/DB/Com/Events/Viewer.php';
include_once '../../../../../Class/DB/Com/Module/LoadModule.php';
include_once '../../../../../Class/DB/Com/User/LoadModule.php';
include_once '../../../../../Class/FileIO/VirtualDirectory.php';
$DBConfig = new Config_DB_Config();
$SC = new Config_DB_Software($DBConfig);
$PlaceC = new Config_Api_Place($DBConfig);
$Sess = new Com_User_SessionManager($DBConfig);
$User = new Com_User_Profile($DBConfig);
$EventM = new Com_Events_Manager($DBConfig);
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
            <link rel="stylesheet" href="../css/Page.css">

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

                    <script>
                    var ss = new SSQueryFW();
                    ss.DocumentReady(function () {
                        var sd = new SuperDialog();
                            

                    });
                </script>
            </head>
            <body >
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
                                <li><a href="../Blog/Manage.php">Manage</a></li>
                                <li><a href="../Blog/View.php">View</a></li>
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
                                <li><a href="BlogViewer.php">Blog</a></li>
                                <li style="font-weight: bold;"> Event</li>
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
                    <div class="Section" >
                        <div id="HtmlReadable" style="word-wrap: break-word;">
                            <?php
                            if (isset($_GET["id"])) {

                                $Auth = "";
                                $Pass = "";
                                if (isset($_SESSION["Event_Password"][$_GET["id"]])) {
                                    $Auth = $_SESSION["Event_Password"][$_GET["id"]]["UserName"];
                                    $Pass = $_SESSION["Event_Password"][$_GET["id"]]["Password"];
                                }
                                if ($Event->AuthEvent($_GET["id"], $Auth, $Pass, Config_DB_Config::Access_Mode_Public)) {
                                    foreach ($Event->GetEventForRead($_GET["id"], $Auth, $Pass, Config_DB_Config::Access_Mode_Public) as $value) {
                                        echo "<div>";
                                        echo "<h3 style='color: blue'>" . $value["name"] . "</h3>";
                                        printf('<div>%s</div>', nl2br($value["htmlcode"]));
                printf('<div>Start Date:%s</div>', $value["startdate"]);
                                        printf('<div>End Date:%s</div>', $value["stopdate"]);
                                        printf('<div>Place:%s,Latitude:%s,Longitude:%s</div>', $value["placename"], $value["latitude"], $value["longitude"]);
                                        echo $PlaceC->EXEMapApi($value["api"], $value["longitude"], $value["latitude"]);
                                        echo "</div>";
                                    }
                                } else if ($Event->AuthEvent($_GET["id"], $Auth, $Pass, Config_DB_Config::Access_Mode_Members)) {
                                    foreach ($Event->GetEventForRead($_GET["id"], $Auth, $Pass, Config_DB_Config::Access_Mode_Members) as $value) {
                                        echo "<div>";
                                        echo "<h3 style='color: blue'>" . $value["name"] . "</h3>";
                                        printf('<div>%s</div>', nl2br($value["htmlcode"]));
                printf('<div>Start Date:%s</div>', $value["startdate"]);
                                        printf('<div>End Date:%s</div>', $value["stopdate"]);
                                        printf('<div>Place:%s,Latitude:%s,Longitude:%s</div>', $value["placename"], $value["latitude"], $value["longitude"]);
                                        echo $PlaceC->EXEMapApi($value["api"], $value["longitude"], $value["latitude"]);
                                        echo "</div>";
                                    }
                                } else {
                                    ?>
                                    <form action="EventViewer.php" method="POST" style="">
                                        <table style="border-style: solid;margin-left: auto;margin-right: auto;">
                                            <tr>
                                                <td colspan="2">
                                                    <label class="Title">Event Is Lock</label>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>UserName:</td>
                                                <td><input style="width: 98%;" type="text" name="authname" value="" /></td>
                                            </tr>
                                            <tr>
                                                <td>Password:</td>
                                                <td> <input style="width: 98%; " type="password" name="password" value="" /></td>
                                            </tr>
                                            <tr style="display: none;">
                                                <td>BlogID:</td>
                                                <td> <input style="width: 98%; " type="hidden" name="blogid" value="<?php echo $_GET["id"]; ?>" /></td>
                                            </tr>
                                            <tr>
                                                <td   colspan="2"> <input  type="submit" value="Login!" style="width: 100%;box-sizing: border-box;" /></td>
                                            </tr>
                                        </table>
                                    </form>
                                    <?php
                                }
                            } else if (isset($_POST["authname"]) && isset($_POST["password"]) && isset($_POST["blogid"])) {
                                $A1 = $Event->AuthEvent($_POST["blogid"], $_POST["authname"], $_POST["password"], Config_DB_Config::Access_Mode_Public);
                                $A2 = $Event->AuthEvent($_POST["blogid"], $_POST["authname"], $_POST["password"], Config_DB_Config::Access_Mode_Members);
                                if ($A1 || $A2) {
                                    if (!isset($_SESSION["Event_Password"])) {
                                        $_SESSION["Event_Password"] = array();
                                    }
                                    $_SESSION["Event_Password"][$_POST["blogid"]] = array("UserName" => $_POST["authname"], "Password" => $_POST["password"]);
                                    header("location: EventViewer.php?id=" . $_POST["blogid"]);
                                } else {
                                    header("location: EventViewer.php?id=" . $_POST["blogid"]);
                                }
                            }
                            ?>
                        </div>
                    </div>
                    <div class="Aside">
                        <div class="BorderBlock" style="margin-top: 1px;">
                            <label class="Title">Event</label>

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
                                printf('<a href="EventViewer.php?id=%s"><span style="font-weight: bold;">%s</span>', $value["id"], $value["name"]);
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
            ob_end_flush();
            