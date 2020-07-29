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
                    var ajax = new Ajax();
                    ImageShow.Size(800, 600);

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
                        ss.S("#ImageRangeViewer").Attr("max", this.GetImageCount() - 1);
                        ss.S("#LabArrayCount").Html(this.GetImageCount());


                    };

                    ImageShow.OnSelectedImage = function (v) {
                        ss.S("#ImageRangeViewer").Val(v);
                        ss.S("#LabPlayIndex").Html(v + 1);

                    }
                    AudioSrc.addEventListener("ended", function () {
                        if (!AudioList.Next()) {
                            AudioList.Frist();
                        }


                    });
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

                    document.onkeyup = function (event) {
                        event.preventDefault();

                        if (event.keyCode == 27) {
                            var domis = document.getElementById("FrameImageShow");
                            domis.removeAttribute("style");
                            ImageShow.Size(800, 600);
                        }

                    }


                    ss.S("#BNFullScreen").Click(function () {

                        var w = window.innerWidth;
                        var h = window.innerHeight;

                        ImageShow.Size(w, h);
                        ss.S("#FrameImageShow").CSS("background-color: black;position: fixed;width: 100%;height: 100%;left:0;top:0;z-index:9999;");
                    });

                    ss.S("#BNPlay").Click(function () {
                        var text = this.innerHTML.toLowerCase();
                        if (text === "play") {
                            this.innerHTML = "Stop";
                            ImageShow.Start();
                        } else if (text === "stop") {
                            this.innerHTML = "Play";
                            ImageShow.Stop();
                        }

                    });
                    ss.S("#ImageRangeViewer").Change(function () {
                        this.setAttribute("seek", "true");
                        this.setAttribute("current", this.value);
                    });

                    ajax.Post("../../../../Api/Ajax/Audio/GetPlayList.php", {}, function (data) {
                        data = JSON.parse(data);

                        for (var i in data) {
                            ss.S("#OptAudioLibrary").Append(data[i], data[i]);
                        }
                        ss.S("#OptAudioLibrary").Change();
                    });
                    ss.S("#OptAudioLibrary").Change(function (v) {
                        ajax.Get("../../../../Api/Ajax/Audio/GetAudioList.php", {"Name": this.value}, function (data) {
                            data = JSON.parse(data);
                            AudioList.Empty();
                            for (var i in data) {
                                AudioList.AddList(data [i]["path"], data [i]["name"]);
                            }

                        });
                    });



                    ss.S("#OPTChangeTime").Change(function () {
                        ImageShow.SetTransitionTime(parseInt(this.value) * 1000);
                    });
                    ss.S("#OPTHoldTime").Change(function (v) {
                        ImageShow.SetHoldTime(parseInt(this.value) * 1000);
                    });
                    ss.S("#OptImageLibrary").Change(function (v) {
                        if (this.value == "-1") {
                            ajax.Post("../../../../Api/Ajax/Files/SearchFiles.php", {"Path": "/", "Name": "jpg,jpeg,png"}, function (data) {
                                ImageShow.Clear();
                                data = JSON.parse(data);
                                for (var i in data) {
                                    ImageShow.AddImage("../../../../Api/Action/Files/DownloadFiles.php?path=" + (data[i]["fullpath"]));
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
                        <div id="FrameImageShow">
                            <div id="ImageShow" style="margin-left: auto;margin-right: auto;" >

                            </div>
                        </div>
                        <div style="background-color: black;display: flex;flex-direction: row;">
                            <div>
                                <a id="BNPlay"  style="text-decoration: none;color: white;cursor: pointer;">Play</a>
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


                    </div>
                    <div class="BorderBlock" style="margin-top: 3px;">
                        <div class="TitleCenter" >Hold Time</div>
                        <select id="OPTHoldTime" style="width: 100%;box-sizing: border-box;">
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                            <option value="5">5</option>
                        </select>
                    </div>
                    <div class="BorderBlock" style="margin-top: 3px;">
                        <div class="TitleCenter"  >Change Time</div>
                        <select id="OPTChangeTime" style="width: 100%;box-sizing: border-box;">
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
