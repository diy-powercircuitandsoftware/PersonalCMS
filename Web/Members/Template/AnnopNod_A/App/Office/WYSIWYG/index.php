<?php
session_start();
include_once '../../../../../../../Class/Core/Config/Config.php';
include_once '../../../../../../../Class/Core/UI/NAV.php';
include_once '../../../../../../../Class/Core/Module/Database.php';
include_once '../../../../../../../Class/Com/Event/Database.php';
include_once '../../../../../../../Class/Com/Event/Reader.php';
include_once '../../../../../../../Class/SDK/Module/Basic.php';
include_once '../../../../../Auth/Action/VerifySession.php';
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
            <title>Untitled Document</title>
            <link rel="stylesheet" type="text/css" href="../../../../../../css/HolyGrail.css">
            <link rel="stylesheet" type="text/css" href="../../../../../../css/PersonalCMS.css">
            <?php
            foreach ($modlist as $value) {
                echo $value->Execute(Module_SDK_Basic::Layout_Head);
            }
            ?>
            <script src="../../../../../../js/dom/SuperDialog.js"></script>
            <script src="../../../../../../js/dom/SSQueryFW.js"></script>
            <script src="../../../../../../js/file/FilesList.js"></script>
            <script>
                var ss = new SSQueryFW();
                ss.DocumentReady(function () {
                    var sd = new SuperDialog();
                    var fd = document.getElementById("FilesList").appendChild(new FilesList(false));


                    fd.Mutilselect = false;
                    fd.ChDir = function (v) {
                        ss.Post("../../../../Api/Ajax/Editor/GetHtmlList.php", {"Location": v}, function (data) {
                            ss.S("#FileLocation").Val((v));
                            fd.currentdir = v
                            fd.ClearFileList();
                            data = JSON.parse(data);
                            for (var i in data) {
                                fd.AddFile(data[i]["name"], data[i]["fullpath"], "", data[i]["size"], data[i]["modified"], data[i]["type"]);
                            }
                              fd.RemoveEditable();
                        });
                    };
                    fd.ChDir("/");

                    fd.OpenFile = function (v) {
                        window.location.replace("Editor.php?path=" + (v));
                    };

                    ss.S("#BNNew").Click(function () {
                        sd.Prompt("Enter File Name", function (v) {
                            ss.Post("../../../../Api/Ajax/Editor/SaveHtmlFile.php", {"FullPath": (fd.currentdir + "/" + (v) + ".html"), "HTML": ""}, function (data) {
                                data = JSON.parse(data);
                                fd.ChDir(fd.currentdir);
                            });
                        });
                    });



                });
            </script>
        </head>
        <body class="HolyGrail">
            <header class="Header">
                <div style="width: 50%;"></div>
                <div style="width: 50%;text-align: right;">
                    <?php
                    printf('<img src="../../../../../Api/Action/Profile/Basic/GetUserIcon.php?id=%s"/>', $_SESSION["User"]["id"]);
                    printf('<span style="font-weight: bold;cursor: default;">%s</span>', $_SESSION["User"]["alias"]);
                    ?>       
                    <a class="MenuLink" style="display: inline;" href="../../../../../Auth/Action/Logout.php">LogOut</a>
                </div>
            </header>
            <div class="HolyGrail-body">
                <nav>
                    <?php
                    foreach ($uinav->FindAllMenuFile("../../../App") as $key => $valueA) {
                        echo '<div class="BorderBlock">';
                        printf(' <div class="TitleCenter">%s</div>', $key);
                        foreach ($valueA as $valueB) {

                            printf('  <a class="MenuLink" href="%s">%s</a>', "../../../App/" . $valueB["path"], $valueB["name"]);
                        }
                        echo '</div>';
                    }
                    foreach ($modlist as $value) {
                        if ($value->SupportLayout(Module_SDK_Basic::Layout_Nav)) {
                            echo ' <div class="BorderBlock" style="margin-top: 1px;" >';
                            printf('<div class="TitleCenter">%s</div>', $value->GetTitle());
                            echo $value->Execute(Module_SDK_Basic::Layout_Nav);
                            echo '</div>';
                        }
                    }
                    ?>     
                </nav>
                <main>
                    <div class="Section" style="box-sizing: border-box;">
                        <div style="display: flex;flex-direction: row;">
                            <span>Location:</span>
                            <input  readonly="readonly" type="text" id="FileLocation" style="flex-grow: 1;" />
                            <button id="BNNew">New</button>
                        </div>
                        <div id="FilesList" ></div>

                    </div>
                </main>
                <aside>
                    <div class="BorderBlock" style="margin-top: 1px;">
                        <div class="TitleCenter">Event</div>
                        <?php
                        foreach ($event->GetComingEvent(Event_Database::Access_Member) as $value) {
                            echo '<div>';
                            printf('<a class="MenuLink" href="../../Event/View.php?id=%s"><span style="font-weight: bold;">%s</span>', $value["id"], $value["name"]);
                            printf('<div style="color: black;" >%s</div></a>', $value["description"]);
                            echo '</div><hr>';
                        }
                        ?>
                    </div>
                    <?php
                    foreach ($modlist as $value) {
                        if ($value->SupportLayout(Module_SDK_Basic::Layout_Aside)) {
                            echo ' <div class="BorderBlock" style="margin-top: 1px;" >';
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
   header("location: ../../../../../Auth/Login.php");
    session_destroy();
}
