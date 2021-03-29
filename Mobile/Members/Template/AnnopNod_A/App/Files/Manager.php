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
            <style>
                .LinkDIR{
                    text-decoration: none;
                    color: blue; 
                }
                .LinkFile{
                    text-decoration: none;
                    color: blue;
                }
                .BNDownload{
                    text-decoration: none;
                    color: blue;
                }
            </style>
            <?php
            foreach ($modlist as $value) {
                echo $value->Execute(Module_SDK_Basic::Layout_Head);
            }
            ?>
            <script>
                var ss = new SSQueryFW();
                ss.DocumentReady(function () {
                    var ajax = new Ajax();
                    var ft = document.getElementById("FilesTable");
                    var uploadpath = "/";
                    var videopreview = document.getElementById("videopreview");
                    var audiopreview = document.getElementById("audiopreview");
                    var imgpreview = document.getElementById("imgpreview");
                    ss.S("#BNShowHideMenu").Click(function () {
                        if (this.getAttribute("data-lock") == "1") {
                            ss.S("#Menu").Show();
                            this.setAttribute("data-lock", "0");
                        } else {
                            ss.S("#Menu").Hide();
                            this.setAttribute("data-lock", "1");
                        }
                    });
                    ss.S("#BNClosePreview").Click(function () {
                        ss.S("#TableShowFiles").Show();
                        ss.S("#Preview").Hide();
                        videopreview.pause();
                        audiopreview.pause();
                        videopreview.style.display = "none";
                        audiopreview.style.display = "none";
                        imgpreview.style.display = "none";
                    });


                    ss.S("#BNDelete").Click(function () {
                        var id = [];
                        [].forEach.call(document.querySelectorAll(".CheckBoxSelect"), function (chkbox) {
                            if (chkbox.checked) {
                                id.push(chkbox.getAttribute("data-fullpath"));
                            }
                        });
                        /*  ajax.Post("../../../../Api/Ajax/Files/Manager/DeleteFiles.php", {"path": v}, function (data) {
                         if (data == "1") {
                         FL.OpenDir(fileupload.currentdir);
                         } else {
                         dialog.Alert(data);
                         }
                         });*/


                        console.log(id);
                    });

                    ss.S("#BNHome").Click(function () {
                        OpenDIR("/");
                    });
                    ss.S("#BNUpload").Change(function (e) {
                        if (e.target.files[0].size < 4194304) {
                            ajax.Post("../../../../Api/Ajax/Files/Manager/UploadFiles.php", {"file": e.target.files[0], "path": uploadpath}, function () {
                                OpenDIR(uploadpath);
                            });
                        } else {
                            alert("can not upload over size");
                        }

                    });
                    ft.addEventListener("click", function (e) {
                        if (e.target.getAttribute("class") == "LinkDIR") {
                            OpenDIR(e.target.getAttribute("data-fullpath"));
                        } else if (e.target.getAttribute("class") == "LinkFile") {
                            var v = e.target.getAttribute("data-fullpath");
                            var ext = v.split('.').pop().toLowerCase();

                            videopreview.pause();
                            audiopreview.pause();
                            videopreview.style.display = "none";
                            audiopreview.style.display = "none";
                            imgpreview.style.display = "none";
                            if (["mp4", "webm"].indexOf(ext) >= 0) {
                                ss.S("#Preview").Show();
                                ss.S("#TableShowFiles").Hide();
                                videopreview.style.display = "";
                                videopreview.src = "../../../../../../Web/Members/Api/Action/Files/Download/DownloadFiles.php?path=" + v;
                            } else if (["ogg", "mp3", "wma"].indexOf(ext) >= 0) {
                                ss.S("#Preview").Show();
                                ss.S("#TableShowFiles").Hide();
                                audiopreview.style.display = "";
                                audiopreview.src = "../../../../../../Web/Members/Api/Action/Files/Download/DownloadFiles.php?path=" + v;
                            } else if (["jpg", "gif", "png", "jpeg"].indexOf(ext) >= 0) {
                                ss.S("#Preview").Show();
                                ss.S("#TableShowFiles").Hide();
                                imgpreview.style.display = "";
                                imgpreview.src = "../../../../../../Web/Members/Api/Action/Files/Download/DownloadFiles.php?path=" + v;
                            } else {
                                ss.S("#Preview").Hide();
                            }


                            //                          
                            //
                        }
                    });
                    function OpenDIR(v) {
                        ajax.Post("../../../../../../Web/Members/Api/Ajax/Files/List/GetFilesListByExtension.php", {"Path": v}, function (data) {
                            uploadpath = v;
                            ft.innerHTML = "";
                            data = JSON.parse(data);
                            for (var i in data) {
                                var tr = ft.appendChild(document.createElement("TR"));
                                var chkbox = tr.appendChild(document.createElement("TD")).appendChild(document.createElement("INPUT"));
                                var download = tr.appendChild(document.createElement("TD")).appendChild(document.createElement("a"));
                                var label = tr.appendChild(document.createElement("TD")).appendChild(document.createElement("a"));
                                chkbox.type = "checkbox";
                                chkbox.setAttribute("data-fullpath", data[i]["fullpath"]);
                                chkbox.setAttribute("class", "CheckBoxSelect");
                                label.innerHTML = data[i]["name"];
                                label.style.cssText = "word-break:break-all;";
                                label.setAttribute("data-fullpath", data[i]["fullpath"]);
                                label.href = "#";
                                download.setAttribute("class", "BNDownload");
                                download.innerHTML = "Download";
                                download.setAttribute("href", "../../../../../../Web/Members/Api/Action/Files/Download/DownloadFiles.php?path=" + data[i]["fullpath"]);
                                if (data[i]["type"] == "DIR") {
                                    label.setAttribute("class", "LinkDIR");
                                } else if (data[i]["type"] == "FILE") {
                                    label.setAttribute("class", "LinkFile");
                                }
                            }
                            // ss.S("#CHDIRList").Html((v));
                        });
                    }
                    OpenDIR("/");
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
            <nav id="Menu"  style="display: none;">
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
                <div>
                    <a id="BNHome" style="display: inline;" class="MenuLink" href="#">Home</a>    
                    <?php
                    if ($_SESSION["User"]["writable"] == 1) {
                        ?>
                        <a style="display: inline;" class="MenuLink" href="#">Cut</a>
                        <a style="display: inline;" class="MenuLink" href="#">Copy</a>
                        <a style="display: inline;" class="MenuLink" href="#">Paste</a>
                        <a id="BNDelete" style="display: inline;" class="MenuLink" href="#">Delete</a>
                        <a style="display: inline;" class="MenuLink" href="#">Rename</a>
                        <a style="display: inline;" class="MenuLink" href="#">Share</a>
                        <div>
                            <label>Upload:</label>
                            <input type="file" id="BNUpload"  />
                        </div>

                        <?php
                    }
                    ?>

                </div>
                <table id="TableShowFiles" border="1" style="width: 100%;">
                    <thead>
                        <tr>
                            <th>Select</th>
                            <th>Download</th>
                            <th>Name</th>
                        </tr>
                    </thead>
                    <tbody id="FilesTable"  >

                    </tbody>
                </table>
                <div id="Preview" style="display: none;">
                    <div style="text-align: right;">
                        <a id="BNClosePreview" href="#" style="text-decoration: none;color: blue;">X</a>
                    </div>
                    <video style="display: none;width: 100%;" id="videopreview" controls="controls"></video>
                    <audio style="display: none;width: 100%;"  id="audiopreview"  controls="controls"></audio>
                    <img style="display: none;width: 100%;"  id="imgpreview" src=""/>
                </div>
            </main>
            <aside>

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