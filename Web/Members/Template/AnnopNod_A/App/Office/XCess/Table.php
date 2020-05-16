<?php
session_start();
include_once '../../../../../../Class/DB/Config/DB/Config.php';
include_once '../../../../../../Class/DB/Config/DB/Software.php';
include_once '../../../../../../Class/DB/Com/User/SessionManager.php';
include_once '../../../../../../Class/DB/Com/User/Profile.php';
include_once '../../../../../../Class/DB/Com/Events/Viewer.php';
include_once '../../../../../../Class/DB/Com/Module/LoadModule.php';
include_once '../../../../../../Class/DB/Com/User/LoadModule.php';
include_once '../../../../../../Class/DB/Com/User/Permission.php';
$DBConfig = new Config_DB_Config();
$SC = new Config_DB_Software($DBConfig);
$Sess = new Com_User_SessionManager($DBConfig);
$User = new Com_User_Profile($DBConfig);
$Event = new Com_Events_Viewer($DBConfig);
$Module = new Com_Module_LoadModule($DBConfig);
$UModule = new Com_User_LoadModule($DBConfig);
$Permission = new Com_User_Permission($DBConfig);
$DBConfig->Open();
if ($SC->Online() && isset($_SESSION["UserID"]) && $Sess->Registered(session_id())) {
   if (isset($_GET["path"]) && $Permission->Writable($_SESSION["UserID"])) {
    ?>
    <!DOCTYPE html>
    <html>
        <head>
            <meta charset="UTF-8">
            <title><?php echo urldecode($_GET["path"]); ?></title>
            <link rel="stylesheet" href="../../css/Page.css">
            <style>
                .SQLTab{
                    display: none;
                }
            </style>
            <?php
            foreach ($UModule->LoadModule($_SESSION["UserID"], Com_User_LoadModule::Layout_Head) as $value) {
                try {
                    include_once '../../../../../../Class/DB/UserModule/' . $value["filename"];
                    $mod = new $value["classname"]($UModule);
                    $mod->LoadConfig($value["config"]);
                    echo $mod->Execute();
                } catch (Exception $ex) {
                    
                }
            }
            ?>

            <script src="../../../../../js/dom/SSQueryFW.js"></script>
            <script src="../../../../../js/dom/SuperDialog.js"></script>
            <script src="../../../../../js/dom/TableTools.js"></script>
            <script>
                var ss = new SSQueryFW();
                ss.DocumentReady(function () {

                    var sd = new SuperDialog();
                    var TableList = new TableTools().Import(document.getElementById("TableList"));
                    TableList.addEventListener("click", function (e) {
                        if (e.target.getAttribute("class") == "BNRenameTableList") {
                            sd.Prompt("New Name", function (newname) {
                                ss.Post("../../../../Api/Ajax/SQLite/RenameTable.php", {"Path": ss.URLParam()["path"], "OldTableName": e.target.value, "NewTableName": newname}, function (data) {
                                    LoadTable();
                                });
                            }).Title("Rename").ZIndex(999);
                        }
                    });
                    function LoadTable() {
                        ss.Post("../../../../Api/Ajax/SQLite/GetTable.php", {"path": ss.URLParam()["path"]}, function (data) {
                            TableList.DeleteRowAfter(0);
                            data = JSON.parse(data)
                            for (var i in data) {
                                TableList.InsertRow();
                                TableList.InsertCellLastRow('<input type="checkbox" class="CheckTableList" value="' + data[i] + '" />');
                                TableList.InsertCellLastRow(data[i]);
                                TableList.InsertCellLastRow('<button class="BNRenameTableList" value="' + data[i] + '">Rename</button>');
                            }
                        });
                    }
                    ss.S("#BNDeleteTable").Click(function () {
                        sd.Confirm("Do You Delete This Table", function () {
                            ss.Post("../../../../Api/Ajax/SQLite/DeleteTable.php", {"path": ss.URLParam()["path"], "TableName": ss.S(".CheckTableList").Val()}, function (data) {
                                LoadTable();
                            });
                        }).ZIndex(999).Title("Delete???");
                    });

                    LoadTable();

                });
            </script>
        </head>
        <body style="">

            <div id="Header" style="position: absolute;" >
                <div style="width: 50%;">
                    <a href="../../index.php">
                        <img  src="../../../../../../File/Resource/Logo.png"/>
                    </a>
                </div>
                <div  style="width: 50%;text-align: right;">
                    <a href="../../index.php">MainPage</a>
                    <?php
                    $Dat = $User->GetBasicUserData($_SESSION["UserID"]);
                    printf('<img  src="../../../../Api/Action/Profile/GetUserIcon.php?id=%s" />', $Dat["userid"]);
                    echo '<span>' . $Dat["alias"] . '</span>';
                    ?>
                    <a href="../../Config/Config.php">Config</a>

                     <a  href="../../../../Session/Action/Logout.php">Logout</a>
                </div>
            </div>
            <div class="Container">
                <div class="Nav">
                    <div class="BorderBlock" style="margin-top: 1px;">
                        <span class="Title" style="display: block;">Audio</span>
                        <ul>
                            <li><a href="../../Audio/Player.php">Player</a></li>
                            <li><a href="../../Audio/PlayList.php">PlayList</a></li>

                        </ul>
                    </div>
                    <div class="BorderBlock" style="margin-top: 1px;">
                        <span class="Title" style="display: block;">Blog</span>
                        <ul>
                            <li><a href="../../Blog/Manage.php">Manage</a></li>
                            <li><a href="../../Blog/View.php">View</a></li>
                        </ul>
                    </div>

                    <div class="BorderBlock" style="margin-top: 1px;">
                        <span class="Title" style="display: block;">Event</span>
                        <ul>
                            <li><a href="../../Event/Manage.php">Manage</a></li>
                            <li><a href="../../Event/View.php">View</a></li>
                        </ul>
                    </div>
                    <div class="BorderBlock" style="margin-top: 1px;">
                        <span class="Title" style="display: block;">Files</span>
                        <ul>
                            <li><a href="../../Files/Manager.php">Manager</a></li>
                            <li><a href="../../Files/Temp.php">Temp</a></li>
                            <li><a href="../../Files/Trash.php">Trash</a></li>
                        </ul>
                    </div>
                    <div class="BorderBlock" style="margin-top: 1px;">
                        <span class="Title" style="display: block;">Office</span>
                        <ul>
                            <li><a href="../FinFin/MainPage.php">FinFin</a></li>
                            <li><a href="../FlowFlow/MainPage.php">FlowFlow</a></li>
                            <li><a href="../Image/MainPage.php">Image</a></li>
                            <li><a href="../PointPoint/MainPage.php">PointPoint</a></li>
                            <li><a href="../Statistics/MainPage.php">Statistics</a></li>
                            <li><a href="../WordWord/MainPage.php">WordWord</a></li>
                            <li><a href="../WYSIWYG/NewDoc.php">WYSIWYG</a></li>
                            <li><a href="../XCell/MainPage.php">XCell</a></li>
                            <li style="font-weight: bold;">XCess</li>
                        </ul>
                    </div>
                    <div class="BorderBlock" style="margin-top: 1px;">
                        <span class="Title" style="display: block;">Photo</span>
                        <ul>
                            <li><a href="../../Photo/ImageSlider.php">ImageSlider</a></li>
                            <li><a href="../../Photo/PlayList.php">PlayList</a></li>
                        </ul>
                    </div>
                    <div class="BorderBlock" style="margin-top: 1px;">
                        <span class="Title" style="display: block;">Share</span>
                        <ul>
                            <li><a href="../../Share/BlogViewer.php">Blog</a></li>
                            <li><a href="../../Share/EventViewer.php">Event</a></li>
                        </ul>
                    </div>
                    <?php
                    $Dat = array_merge($Module->LoadModule(Com_Module_LoadModule::Layout_Nav, Config_DB_Config::Access_Mode_Members), $Module->LoadModule(Com_Module_LoadModule::Layout_Nav, Config_DB_Config::Access_Mode_Public));
                    foreach ($Dat as $value) {
                        try {
                            echo ' <div class="BorderBlock" style="margin-top: 3px;" >';
                            include_once '../../../../../../Class/DB/Module/' . $value["filename"];
                            $mod = new $value["classname"]($Module);
                            printf('<label class="Title">%s</label>', $mod->GetTitle());
                            $mod->SetModuleID($value["id"]);
                            $mod->SetModulePage("../../Module/Page.php");
                            $mod->SetUserID($_SESSION["UserID"]);
                            echo $mod->Execute();
                            echo '</div>';
                        } catch (Exception $ex) {
                            
                        }
                    }
                    ?>
                </div>
                <div class="Section" style="box-sizing: border-box;">

                    <table id="TableList" border="1">
                        <tr>
                            <th>Select</th>
                            <th>Table Name</th>
                            <th>Rename</th>
                        </tr>
                    </table>
                    <div>
                        <button id="BNDeleteTable">Delete</button>
                    </div>
                </div>
                <div class="Aside" >
                    <div id="DatabaseManager" class="BorderBlock" style="margin-top: 1px;">
                        <label class="Title">Database Manager</label>
                        <ul>
                            <li> <a href="MainPage.php">Database</a></li>
                            <li style="font-weight: bold;"> Table </li>
                            <li><a href="<?php echo "Field.php?path=" . urlencode($_GET["path"]); ?>">Field</a></li>
                            <li> <a href="<?php echo "Data.php?path=" . urlencode($_GET["path"]); ?>">Data</a></li>
                            <li><a href="<?php echo "SQL.php?path=" . urlencode($_GET["path"]); ?>">SQL</a></li>
                        </ul>

                    </div>
                    <div class="BorderBlock" style="margin-top: 1px;">
                        <label class="Title">Form</label>
                        <a href="#">Create Form</a>
                    </div>

                    <div class="BorderBlock" style="margin-top: 1px;">
                        <label class="Title">My Event</label>
                        <?php
                        foreach ($Event->GetCurrentMyEvent($_SESSION["UserID"]) as $value) {
                            echo '<div  >';
                            printf('<a href="../../Event/View.php?id=%s"><span style="font-weight: bold;">%s</span>', $value["id"], $value["name"]);
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
                            printf('<a href="../../Share/EventViewer.php?id=%s"><span style="font-weight: bold;">%s</span>', $value["id"], $value["name"]);
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
                            include_once '../../../../../../Class/DB/Module/' . $value["filename"];
                            $mod = new $value["classname"]($Module);
                            printf('<label class="Title">%s</label>', $mod->GetTitle());
                            $mod->SetModuleID($value["id"]);
                            $mod->SetModulePage("../../Module/Page.php");
                            $mod->SetUserID($_SESSION["UserID"]);
                            echo $mod->Execute();
                            echo '</div>';
                        } catch (Exception $ex) {
                            
                        }
                    }
                    ?>
                </div>
            </div>

        </body>
    </html>
    <?php
    } else {
        header("location: MainPage.php");
    }
} else {
    header("location: ../../../../Session/AuthUserID.php");
    session_destroy();
}
