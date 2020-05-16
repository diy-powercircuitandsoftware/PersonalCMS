<?php
session_start();
include_once '../../../../../Class/DB/Config/DB/Config.php';
include_once '../../../../../Class/DB/Config/DB/Software.php';
include_once '../../../../../Class/DB/Com/Module/LoadModule.php';
include_once '../../../../../Class/DB/Com/User/SessionManager.php';
include_once '../../../../../Class/DB/Com/User/Profile.php';
include_once '../../../../../Class/DB/Com/User/LoadModule.php';
include_once '../../../../../Class/DB/Com/Events/Manager.php';
include_once '../../../../../Class/DB/Com/Events/Viewer.php';
include_once '../../../../../Class/DB/Com/Audio/PlayList_Manager.php';
$DBConfig = new Config_DB_Config();
$SC = new Config_DB_Software($DBConfig);
$Sess = new Com_User_SessionManager($DBConfig);
$User = new Com_User_Profile($DBConfig);
$Module = new Com_Module_LoadModule($DBConfig);
$UModule = new Com_User_LoadModule($DBConfig);
$EventM = new Com_Events_Manager($DBConfig);
$Event = new Com_Events_Viewer($DBConfig);
$Audio = new Com_Audio_PlayList_Manager($DBConfig);
$DBConfig->Open();
if ($SC->Online() && isset($_SESSION["UserID"]) && $Sess->Registered(session_id())) {
    ?>
    <!DOCTYPE html>
    <html>
        <head>
            <meta charset="UTF-8">
            <title><?php echo $SC->GetName(); ?></title>
            <link rel="stylesheet" href="../css/Page.css">
            <script src="../../../../js/dom/SSQueryFW.js"></script>
            <script src="../../../../js/image/ImageList.js"></script>
            <script src="../../../../js/image/SlideShow.js"></script>
            <script src="../../../../js/player/PlayingList.js"></script>

                <style>
                    #AudioList{
                        cursor: pointer;
                    }
                    .Playing{
                        background-color: burlywood;
                        font-weight: bold;
                    }
                    #ImageShow{
                        border-style: solid;
                        margin: 0 auto;
                        display: table;
                        max-height: 100%;
                        max-width: 100%;
                        box-sizing: border-box;
                        background-color: black;
                    }
                </style>
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
                <script>
                    var ss = new SSQueryFW();
                    ss.DocumentReady(function () {
                        var AudioSrc = document.getElementById("AudioSrc");
                        var AudioList = document.getElementById("AudioList").appendChild(new PlayingList());
                        var ImageShow = document.getElementById("ImageShow").appendChild(new SlideShow());
                        var ImgList = new ImageList();
                        ImageShow.width = 1920;
                        ImageShow.height = 1080;

                        AudioSrc.addEventListener("ended", function () {
                            var next = AudioList.GetNext();
                            if (next !== null) {
                                next.click();
                            }
                        });
                        AudioList.Click = function (d) {
                            AudioSrc.src = "../../../Api/Action/Files/DownloadFile.php?id=" + btoa(d.getAttribute("url"));
                            var playPromise = AudioSrc.play();
                            if (this.Last !== undefined) {
                                this.Last.setAttribute("class", "");
                            }
                            d.setAttribute("class", "Playing");
                            if (playPromise !== undefined) {
                                playPromise.then(function () {
                                    // Automatic playback started!
                                }).catch(function (error) {
                                    // Automatic playback failed.
                                    // Show a UI element to let the user manually start playback.
                                });
                            }
                        };
                        ImgList.OnAfterAddImage = function () {
                            ss.S("#ImageRangeViewer").Attr("max", this.Count() - 1);
                            ss.S("#LabArrayCount").Html(this.Count());
                        };

                        ImgList.OnBeforeChangeIndex = function () {
                            if (ss.S("#ImageRangeViewer").Attr("seek") == "true") {
                                ImgList.Index = parseInt(ss.S("#ImageRangeViewer").Attr("current"));
                                ss.S("#ImageRangeViewer").Attr("seek", "");
                            }
                        };

                        ImgList.OnChangeIndex = function () {
                            var Transitions = ImageShow.Transitions;
                            var keys = Object.keys(Transitions)
                            var rt = Transitions[keys[ keys.length * Math.random() << 0]];
                            rt(this.GetPreviousImage(), this.GetCurrentImage(), ss.S("#OPTCTime").Val(), 60, function () {
                                setTimeout(function () {
                                    if (ss.S("#BNPlay").Attr("playing") == "1") {
                                        ImgList.Next();
                                    } else {
                                        ss.S("#BNPlay").Attr("lock", "0");
                                    }
                                }, parseInt(ss.S("#OPTHTime").Val()) * 1000);

                                ss.S("#ImageRangeViewer").Val(ImgList.Index);
                                ss.S("#LabPlayIndex").Html(ImgList.Index);
                            });
                        };
                        document.onkeyup = function (event) {
                            if (event.which == 27 || event.keyCode == 27) {
                                var domis = document.getElementById("ImageShow");
                                domis.removeAttribute("style");
                            }
                        }
                        window.onresize = function () {
                            if (window.screenTop && window.screenY) {
                                var domis = document.getElementById("ImageShow");
                                domis.removeAttribute("style");
                            }
                        };

                        ss.S("#BNFullScreen").Click(function () {
                            ss.S("#ImageShow").CSSText("background-color: black;position: fixed;width: 100%;height: 100%;left:0;top:0;z-index:9999;");
                        });

                        ss.S("#BNPlay").Click(function () {

                            if (this.getAttribute("playing") == "1") {
                                this.setAttribute("playing", "0");
                                this.innerHTML = "Play";
                            } else {
                                this.setAttribute("playing", "1");
                                this.innerHTML = "Pause";
                                if (this.getAttribute("lock") == "0") {
                                    this.setAttribute("lock", "1");
                                    ImgList.Next();
                                }
                            }
                        });
                        ss.S("#ImageRangeViewer").Change(function () {
                            this.setAttribute("seek", "true");
                            this.setAttribute("current", this.value);
                        });
                        ss.S("#OptAudioLibrary").Change(function (v) {

                            if (this.value == "-1") {

                                ss.Get("../../../Api/Ajax/AudioPlayer/GetAllAudioFilesName.php", {}, function (data) {
                                    data = JSON.parse(data);
                                    AudioList.Empty();

                                    for (var i in data) {
                                        AudioList.AddList(data[i]["name"]).setAttribute("url", data[i]["fullpath"]);

                                    }
                                });
                            } else if (this.value !== "") {
                                /* ss.Get("../../../Api/Ajax/Audio/GetMusicAlbumFiles.php", {"AlbumID": this.value}, function (data) {


                                 });*/
                            }
                        });

                        ss.S("#OptImageLibrary").Change(function (v) {
                            if (this.value == "-1") {
                                ss.Get("../../../Api/Ajax/PhotoPlayer/GetAllPhotoList.php", {}, function (data) {
                                    ImgList.ClearImageList();
                                    data = JSON.parse(data);
                                    for (var i in data) {
                                        ImgList.AddImage("../../../Api/Action/Files/DownloadFile.php?id=" + btoa(data[i]["fullpath"]));
                                    }
                                });
                            }
                        });

                    });
                </script>
            </head>
            <body  >
                <div id="Header" style="">
                    <div style="width: 50%;">
                        <a href="../index.php">
                            <img  src="../../../../../File/Resource/Logo.png"/>
                        </a>
                    </div>
                    <div  style="width: 50%;text-align: right;">
                        <a href="../index.php">MainPage</a>
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
                                <li style="font-weight: bold;">ImageSlider</li>
                                <li><a href="PlayList.php">PlayList</a></li>
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
                    <div class="Section" >

                        <div style="display:block; ">
                            <div id="ImageShow" style="width: 100%;" >

                            </div>
                            <div style="background-color: black;display: flex;flex-direction: row;">
                                <div>
                                    <a id="BNPlay" lock="0" style="text-decoration: none;color: white;cursor: pointer;">Play</a>
                                </div>
                                <div style=" flex-grow: 1;">
                                    <input id="ImageRangeViewer" type="range" min="0" max="0"    step="1" style="width: 98%;" />
                                </div>
                                <div style="color: white;">
                                    <label id="LabPlayIndex">0</label>
                                    <label>/</label>
                                    <label id="LabArrayCount">0</label>
                                </div>
                                <div>
                                    <img src="img/fullscreen.png" id="BNFullScreen"  alt="FullScreen"/>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="Aside">
                        <div class="BorderBlock">
                            <span class="Title" style="display: block ">Library</span>
                            <select id="OptImageLibrary" style="width: 99%;">
                                <option>==Select==</option>
                                <option value="-1">* All Image *</option>
                            </select>
                            <ul id="ImageList">

                            </ul>

                        </div>
                        <div class="BorderBlock" style="margin-top: 3px;">
                            <span class="Title" style="display: block;">Hold Time</span>
                            <select id="OPTHTime" style="width: 100%;box-sizing: border-box;">
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                                <option value="5">5</option>
                            </select>
                        </div>
                        <div class="BorderBlock" style="margin-top: 3px;">
                            <span class="Title" style="display: block;">Change Time</span>
                            <select id="OPTCTime" style="width: 100%;box-sizing: border-box;">
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                                <option value="5">5</option>
                            </select>
                        </div>
                        <div class="BorderBlock" style="margin-top: 3px;">
                            <span class="Title" style="display: block ">Audio</span>
                            <select id="OptAudioLibrary" style="width: 99%;">
                                <option>==Select==</option>
                                <option value="-1">* All Audio *</option>
                            </select>
                            <div id="AudioList">

                            </div>
                            <div>
                                <audio  style="width: 100%;box-sizing: border-box;" id="AudioSrc" controls="true"  ></audio>
                            </div>
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

                </div>
            </body>
        </html>
        <?php
    } else {
     header("location: ../../../Session/AuthUserID.php");
    session_destroy();
}
