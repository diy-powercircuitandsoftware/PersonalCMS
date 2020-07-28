<?php
session_start();
include_once '../../../../../../Class/Core/Config/Config.php';
include_once '../../../../../../Class/Core/UI/NAV.php';
include_once '../../../../../../Class/Core/Module/Database.php';
include_once '../../../../../../Class/Com/Event/Database.php';
include_once '../../../../../../Class/Com/Event/Reader.php';
include_once '../../../../../../Class/Com/Blog/Database.php';
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
            <title><?php echo basename(__FILE__, ".php"); ?></title>
            <link rel="stylesheet" type="text/css" href="../../../../../css/HolyGrail.css">
            <link rel="stylesheet" type="text/css" href="../../../../../css/PersonalCMS.css">
            <?php
            foreach ($modlist as $value) {
                echo $value->Execute(Module_SDK_Basic::Layout_Head);
            }
            ?>
            <script src="../../../../../js/dom/SSQueryFW.js"></script>
            <script src="../../../../../js/dom/PlayingList.js"></script>
            <script src="../../../../../js/io/Ajax.js"></script>
            <script src="../../../../../js/image/SlideShow.js"></script>


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

            <script>
                var ss = new SSQueryFW();
                ss.DocumentReady(function () {
                    var AudioSrc = document.getElementById("AudioSrc");
                    var AudioList = new PlayingList(document.getElementById("AudioList"));
                    var ImageShow = new SlideShow(document.getElementById("ImageShow"));
  
                ImageShow.Size(800, 600);
                ImageShow.AddImage("image/1.jpg");
                ImageShow.AddImage("image/2.jpg");
                ImageShow.AddImage("image/3.jpg");
                ImageShow.AddImage("image/4.jpg");
                ImageShow.AddImage("image/5.jpg");
ImageShow.AddTransition(SlideShow_Transition_CircleOut);
                ImageShow.AddTransition(SlideShow_Transition_FadeOutFadeIn);
                ImageShow.AddTransition(SlideShow_Transition_Corner);
                ImageShow.AddTransition(SlideShow_Transition_BottomToTop);
                ImageShow.AddTransition(SlideShow_Transition_PageTurn);
                ImageShow.AddTransition(SlideShow_Transition_FromVerticalCenter);
                ImageShow.AddTransition(SlideShow_Transition_RightToLeft);
                ImageShow.AddTransition(SlideShow_Transition_StarOut);
                ImageShow.AddTransition(SlideShow_Transition_ToHorizontalCenter);
                ImageShow.AddTransition(SlideShow_Transition_FromHorizontalCenter);
                ImageShow.AddTransition(SlideShow_Transition_TopToBottom);
                ImageShow.AddTransition(SlideShow_Transition_LeftToRight);
                ImageShow.AddTransition(SlideShow_Transition_ToVerticalCenter);
                ImageShow.AddTransition(SlideShow_Transition_VerticalBlind);
                ImageShow.AddTransition(SlideShow_Transition_HorizontalBlind);
                ImageShow.AddTransition(SlideShow_Transition_HeartOut);
                ImageShow.AddTransition(SlideShow_Transition_RectWipe);
                ImageShow.AddTransition(SlideShow_Transition_SpinRight);
                ImageShow.AddTransition(SlideShow_Transition_ZoomInOut);


                ImageShow.OnImageListChange = function (v) {
                    if (v == 5) {
                        ImageShow.Start();
                    }
                };

                ImageShow.OnSelectedImage = function (v) {
                    console.log(this.GetImageCount());
                }
                    /* AudioSrc.addEventListener("ended", function () {
                     var next = AudioList.GetNext();
                     if (next !== null) {
                     next.click();
                     }
                     });*/
                    AudioList.Select(function (v) {
                        AudioSrc.pause();
                        AudioSrc.src = "../../../../Api/Action/Files/DownloadFiles.php?path=" + v;
                        var pp = AudioSrc.play();
                        if (pp !== undefined) {
                            pp.then(function () {

                            }).catch(function (error) {

                            });
                        }
                    });
                    /*ImgList.OnAfterAddImage = function () {
                        ss.S("#ImageRangeViewer").Attr("max", this.Count() - 1);
                        ss.S("#LabArrayCount").Html(this.Count());
                    };

                    ImgList.OnBeforeChangeIndex = function () {
                        if (ss.S("#ImageRangeViewer").Attr("seek") == "true") {
                            ImgList.Index = parseInt(ss.S("#ImageRangeViewer").Attr("current"));
                            ss.S("#ImageRangeViewer").Attr("seek", "");
                        }
                    };*/

                 /*   ImgList.OnChangeIndex = function () {
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
                    };*/
                  /*  document.onkeyup = function (event) {
                        if (event.which == 27 || event.keyCode == 27) {
                            var domis = document.getElementById("ImageShow");
                            domis.removeAttribute("style");
                        }
                    }*/
                    window.onresize = function () {
                        if (window.screenTop && window.screenY) {
                            var domis = document.getElementById("ImageShow");
                            domis.removeAttribute("style");
                        }
                    };

                    ss.S("#BNFullScreen").Click(function () {
                        ss.S("#ImageShow").CSS("background-color: black;position: fixed;width: 100%;height: 100%;left:0;top:0;z-index:9999;");
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
                            printf('  <a class="MenuLink" href="%s">%s</a>', $valueB["path"], $valueB["name"]);
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
                </main>
                <aside>
                    <div class="BorderBlock">
                        <div class="TitleCenter">Library</div>
                        <select id="OptImageLibrary" style="width: 99%;">
                            <option>==Select==</option>
                            <option value="-1">* All Image *</option>
                        </select>
                        <ul id="ImageList">

                        </ul>

                    </div>
                    <div class="BorderBlock" style="margin-top: 3px;">
                        <div class="TitleCenter" >Hold Time</div>
                        <select id="OPTHTime" style="width: 100%;box-sizing: border-box;">
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                            <option value="5">5</option>
                        </select>
                    </div>
                    <div class="BorderBlock" style="margin-top: 3px;">
                        <div class="TitleCenter"  >Change Time</div>
                        <select id="OPTCTime" style="width: 100%;box-sizing: border-box;">
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                            <option value="5">5</option>
                        </select>
                    </div>
                    <div class="BorderBlock" style="margin-top: 3px;">
                        <div class="TitleCenter" >Audio</div>
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
                    <div class="BorderBlock">
                        <div class="TitleCenter" style="display: block ">Library</div>
                        <select id="OptLibrary" style="display: block;width: 100%;box-sizing: border-box;">
                            <option>==Select==</option>


                        </select>
                        <div id="AudioList"></div>
                    </div>
                    <div class="BorderBlock">
                        <div class="TitleCenter"  >Play</div>
                        <select id="PlayMode"  style="display: block;width: 100%;box-sizing: border-box;">
                            <option value="0">None</option>
                            <option value="1">Repeat</option>
                            <option value="2">Repeat All</option>
                            <option value="3">Random</option>
                        </select>
                    </div>
                    <div class="BorderBlock">
                        <div class="TitleCenter" >Volume</div>
                        <input style="display: block;width: 100%;box-sizing: border-box;"  id="RangeVolume" type="range" min="0" max="1" step="0.1" value="1" />
                    </div>
                    <div class="BorderBlock">
                        <div class="TitleCenter" style="display: block ">Equalizer</div>
                        <select id="EqualizerPresetsList" style="display: block;width: 100%;box-sizing: border-box;" >
                        </select>
                    </div>
                    <div class="BorderBlock">
                        <div class="TitleCenter" style="display: block ">Visualizer</div>
                        <select id="VisualizerList" style="display: block;width: 100%;box-sizing: border-box;" >
                        </select>
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
