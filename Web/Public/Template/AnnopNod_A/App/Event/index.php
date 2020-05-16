<?php
session_start();
ob_start();
include_once '../../../../../Class/DB/Config/DB/Config.php';
include_once '../../../../../Class/DB/Config/DB/Software.php';
include_once '../../../../../Class/DB/Config/Api/Place.php';
include_once '../../../../../Class/DB/Com/Events/Viewer.php';
include_once '../../../../../Class/DB/Com/Module/LoadModule.php';
$DBConfig = new Config_DB_Config();
$SC = new Config_DB_Software($DBConfig);
$Event = new Com_Events_Viewer($DBConfig);
$Module = new Com_Module_LoadModule($DBConfig);
$PlaceC = new Config_Api_Place($DBConfig);
if ($DBConfig->Open()) {
    if ($SC->Online()) {
        ?>
        <!DOCTYPE html>
        <html>
            <head>
                <meta charset="UTF-8">
                <title><?php echo $SC->GetName(); ?></title>
                <link rel="stylesheet" type="text/css" href="../css/Page.css">
                <style>
                            .OpenAPP{
                                width: 100px;
                                height: 100px;
                                word-wrap: break-word;
                                margin-left: 3px;
                                margin-top: 3px;
                                text-align: center;
                            }
                            .EventList{
                                margin-top: 1px;
                                background-color: Wheat;
                                display:block;
                                border-style: solid;
                                border-width: 1px;
                            }
                            .EventListDate {
                                display: flex;
                            }
                            .LinkViewEventData {
                                display: flex;
                            }

                            .EventListDate span{
                                width: 50%;
                            }
                            .LinkViewEventData div{
                                width: 50%;
                            }
                            .EList{
                                background-color: Wheat ;
                                width: 98%;
                                margin-left: auto;
                                margin-right: auto;
                                border-style: solid;
                                border-width: thin;
                                margin-top: 3px;
                            }
                        </style>
                    </head>
                    <body >
                        <div id="Header" style="position: static;">
                            <h1 style="width: 100%;text-align: center;"><?php echo $SC->GetName(); ?> Website</h1>
                        </div>
                        <div class="Container">
                            <div class="Nav" >
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
                                    <a href="../Blog/index.php">Viewer</a>
                                </div>
                                <div class="BorderBlock" style="margin-top: 1px;">
                                    <label class="Title">Event</label>
                                    <span style="font-weight: bold;">Viewer</span>
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
                            <div class="Section"  >

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
                                    } else {
                                        ?>
                                        <form action="index.php" method="POST" style="">
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
                                    $A = $Event->AuthEvent($_POST["blogid"], $_POST["authname"], $_POST["password"], Config_DB_Config::Access_Mode_Public);

                                    if ($A) {
                                        if (!isset($_SESSION["Event_Password"])) {
                                            $_SESSION["Event_Password"] = array();
                                        }
                                        $_SESSION["Event_Password"][$_POST["blogid"]] = array("UserName" => $_POST["authname"], "Password" => $_POST["password"]);
                                        header("location: index.php?id=" . $_POST["blogid"]);
                                    } else {
                                        header("location: index.php?id=" . $_POST["blogid"]);
                                    }
                                }
                                ?>

                            </div>

                            <div class="Aside" >
                                <div class="BorderBlock" style="margin-top: 1px;" >
                                    <label class="Title">Event</label>
                                    <a href="index.php">LoadEvent</a>
                                </div>
                                <?php
                                foreach ($Module->LoadModule(Com_Module_LoadModule::Layout_Aside, Config_DB_Config::Access_Mode_Public) as $value) {
                                    try {
                                        echo ' <div class="BorderBlock" style="margin-top: 1px;" >';
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
                header("location: Error/Offline.php");
            }
        } else {
    header("location: ../../../Root/index.php");
}
ob_end_flush();
