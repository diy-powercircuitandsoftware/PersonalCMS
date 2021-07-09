<?php
include_once '../../../../../../Class/Core/Config/Config.php';
include_once '../../../../../../Class/Core/UI/NAV.php';
include_once '../../../../../../Class/Core/Module/Database.php';
include_once '../../../../../../Class/Core/User/Database.php';
include_once '../../../../../../Class/Core/User/Member.php';
include_once '../../../../../../Class/Com/Blog/Database.php';
include_once '../../../../../../Class/Com/Blog/Reader.php';
include_once '../../../../../../Class/Com/Event/Database.php';
include_once '../../../../../../Class/Com/Event/Reader.php';
include_once '../../../../../../Class/SDK/Module/Basic.php';
$config = new Config();
$uinav = new UINAV();
$module = new Module_Database($config);
$blog = new Blog_Reader(new Blog_Database($config));
$event = new Event_Reader(new Event_Database($config));
$user = new User_Member(new User_Database($config));
if ($config->IsOnline()) {
    $modlist = array();
    foreach ($module->LoadModule(Module_Database::Access_Public) as $value) {

        include_once $module->ModulePath . $value["dirname"] . "/init.php";
        $modlist[] = new $value["classname"]();
    }
    ?>
    <!DOCTYPE html>
    <html>
        <head>
            <meta charset="UTF-8">
            <title><?php echo $config->GetName(); ?></title>

            <link rel="stylesheet" type="text/css" href="../../../../../css/HolyGrail.css">
            <link rel="stylesheet" type="text/css" href="../../../../../css/PersonalCMS.css">
            <script src="../../../../../js/dom/SSQueryFW.js"></script>
            <script src="../../../../../js/dom/SuperDialog/SuperDialog.js"></script>
            <script src="../../../../../js/io/Ajax.js"></script>

            <script src="../../../../../js/image/SlideShow2D/SlideShow2D.js"></script>
            <script src="../../../../../js/image/SlideShow2D/Transition/Basic/Blind.js"></script>
            <script src="../../../../../js/image/SlideShow2D/Transition/Basic/Circle.js"></script>
            <script src="../../../../../js/image/SlideShow2D/Transition/Basic/Fade.js"></script>
            <script src="../../../../../js/image/SlideShow2D/Transition/Basic/Mosaic.js"></script>

            <script src="../../../../../js/image/SlideShow2D/Transition/Basic/Rectangle.js"></script>
            <script src="../../../../../js/image/SlideShow2D/Transition/Basic/Spin.js"></script>
            <script src="../../../../../js/image/SlideShow2D/Transition/Basic/Wipe.js"></script>
            <script src="../../../../../js/image/SlideShow2D/Transition/Basic/Zoom.js"></script>

            <script src="../../../../../js/image/SlideShow2D/Transition/Shape/Heart.js"></script>
            <script src="../../../../../js/image/SlideShow2D/Transition/Shape/PageTurn.js"></script>
            <script src="../../../../../js/image/SlideShow2D/Transition/Shape/Star.js"></script>
            <script src="../../../../../js/image/SlideShow2D/Transition/Shape/Polygons.js"></script>



            <style>
                .BNUserList{
                    cursor: pointer;
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
                .PlayItem{
                    cursor: pointer;
                }
            </style>
            <script>
                var ss = new SSQueryFW();
                ss.DocumentReady(function () {
                    var AudioSrc = document.getElementById("AudioSrc");
                    var ImageShow = new SlideShow2D(document.getElementById("ImageShow"));
                    var ajax = new Ajax();
                    ImageShow.Size(800, 600);
                    AudioSrc.PlayList = [];
                    AudioSrc.PlayListIndex = 0;

                    ImageShow.AddTransition(SlideShow2D_Transition_Blind_BottomUp);
                    ImageShow.AddTransition(SlideShow2D_Transition_Blind_LeftRight);
                    ImageShow.AddTransition(SlideShow2D_Transition_Blind_RightLeft);
                    ImageShow.AddTransition(SlideShow2D_Transition_Blind_TopDown);
                    ImageShow.AddTransition(SlideShow2D_Transition_CircleIn);
                    ImageShow.AddTransition(SlideShow2D_Transition_CircleOut);

                    ImageShow.AddTransition(SlideShow2D_Transition_FadeOutFadeIn);
                    ImageShow.AddTransition(SlideShow2D_Transition_Mosaic);
                    ImageShow.AddTransition(SlideShow2D_Transition_BubbleMosaic);
                    ImageShow.AddTransition(SlideShow2D_Transition_BottomToTop);
                    ImageShow.AddTransition(SlideShow2D_Transition_FromVerticalCenter);
                    ImageShow.AddTransition(SlideShow2D_Transition_ToHorizontalCenter);
                    ImageShow.AddTransition(SlideShow2D_Transition_FromHorizontalCenter);
                    ImageShow.AddTransition(SlideShow2D_Transition_TopToBottom);
                    ImageShow.AddTransition(SlideShow2D_Transition_LeftToRight);
                    ImageShow.AddTransition(SlideShow2D_Transition_RightToLeft);
                    ImageShow.AddTransition(SlideShow2D_Transition_ToVerticalCenter);
                    ImageShow.AddTransition(SlideShow2D_Transition_CornerLeftToRight);
                    ImageShow.AddTransition(SlideShow2D_Transition_PolygonsIn_Hexagon);
                    ImageShow.AddTransition(SlideShow2D_Transition_PolygonsIn_Pentagon);
                    ImageShow.AddTransition(SlideShow2D_Transition_PolygonsIn_Square);
                    ImageShow.AddTransition(SlideShow2D_Transition_PolygonsIn_Triangle);
                    ImageShow.AddTransition(SlideShow2D_Transition_PolygonsOut_Hexagon);
                    ImageShow.AddTransition(SlideShow2D_Transition_PolygonsOut_Pentagon);
                    ImageShow.AddTransition(SlideShow2D_Transition_PolygonsOut_Square);
                    ImageShow.AddTransition(SlideShow2D_Transition_PolygonsOut_Triangle);
                    ImageShow.AddTransition(SlideShow2D_Transition_Spin);
                    ImageShow.AddTransition(SlideShow2D_Transition_RectWipe);
                    ImageShow.AddTransition(SlideShow2D_Transition_ZoomOutZoomIn);
                    ImageShow.AddTransition(SlideShow2D_Transition_HeartIn);
                    ImageShow.AddTransition(SlideShow2D_Transition_HeartOut);
                    ImageShow.AddTransition(SlideShow2D_Transition_PageTurn_BottomToTop);
                    ImageShow.AddTransition(SlideShow2D_Transition_PageTurn_TopDown);
                    ImageShow.AddTransition(SlideShow2D_Transition_StarIn);
                    ImageShow.AddTransition(SlideShow2D_Transition_StarOut);
                    ImageShow.AddTransition(SlideShow2D_Transition_Wiper_LeftToRight);



                    AudioSrc.onended = function () {
                        this.pause();
                        this.PlayListIndex = (this.PlayListIndex + 1) % this.PlayList.length;
                        this.src = this.PlayList[ this.PlayListIndex];
                        this.play();
                    };

                    ImageShow.Load(function (v) {
                        ss.S("#ImageRangeViewer").Attr("max", v);
                        ss.S("#LabArrayCount").Html(v);
                        if (v == 2) {
                            ImageShow.Start();
                            ImageShow.ToggleFPSPlayer();
                            ss.S("#BNPlay").Html("Play");
                        }
                        if (AudioSrc.PlayList.length === 1) {
                            AudioSrc.src = AudioSrc.PlayList[0];
                        }

                    });

                    ImageShow.ImageIndexChange = function (v) {
                        ss.S("#ImageRangeViewer").Val(v);
                        ss.S("#LabPlayIndex").Html(v + 1);
                    };



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

                        if (ImageShow.ToggleFPSPlayer()) {
                            AudioSrc.play();
                            this.innerHTML = "Stop";
                        } else {
                            this.innerHTML = "Play";
                            AudioSrc.pause();
                        }
                    });
                    ss.S("#ImageShow").Click(function () {
                        ss.S("#BNPlay").Click();
                    });


                    ss.S("#ImageRangeViewer").Change(function () {
                        this.setAttribute("seek", "true");
                        this.setAttribute("current", this.value);
                    });

                    ajax.Get("../../../../Api/Ajax/Photo/SlideShow/Share/GetShareList.php", {"user": ss.S("#OPTUser").Val()}, function (data) {
                        data = JSON.parse(data);

                        for (var i in data) {
                            ss.S("#OptLibrary").Append(data[i], data[i]);
                        }
                        ss.S("#OptLibrary").Change();
                    });

                    ss.S("#OPTChangeTime").Change(function () {
                        ImageShow.AnimateTime(parseInt(this.value) * 1000);
                    });
                    ss.S("#OPTHoldTime").Change(function (v) {
                        ImageShow.HoldTime(parseInt(this.value) * 1000);
                    });
                    ss.S("#OptLibrary").Change(function (v) {

                        if (this.value != "") {

                            ajax.Get("../../../../Api/Ajax/Photo/SlideShow/Share/GetShareFileList.php", {"name": this.value, "user": ss.S("#OPTUser").Val()}, function (data) {
                                ImageShow.Clear();
                                data = JSON.parse(data);
                                for (var i in data) {
                                    var param = ss.URLParam(data[i]);
                                    var path = "../../../../Api/Ajax/Photo/SlideShow/Share/DownloadShareFile.php";

                                    if (["jpg", "png", "gif"].indexOf(param["ext"].toLowerCase()) >= 0) {
                                        ImageShow.AddImage(path);
                                    } else if (["ogg", "mp3", "wma"].indexOf(param["ext"].toLowerCase()) >= 0) {
                                        AudioSrc.PlayList.push(path);
                                    }
                                }

                            });
                        }
                    });

                });
            </script>
        </head>
        <body class="HolyGrail">
            <header> 
                <h1 style="width: 100%;text-align: center;"><?php echo $config->GetName(); ?> Website</h1>
            </header>
            <div class="HolyGrail-body">
                <nav>
                    <?php
                    foreach ($uinav->FindAllMenuFile("../../App") as $key => $valueA) {
                        echo '<div class="BorderBlock">';
                        printf(' <div class="TitleCenter">%s</div>', $key);
                        foreach ($valueA as $valueB) {
                            printf('<a class="MenuLink" href="%s">%s</a>', "../../App/" . $valueB["path"], $valueB["name"]);
                        }
                        echo '</div>';
                    }
                    ?>

                    <div class="BorderBlock" style="margin-top: 1px;">
                        <div class="TitleCenter">Template</div>
                        <?php
                        foreach ($uinav->FindAllTemplate("../../../") as $key => $value) {
                            printf('  <a  class="MenuLink" href="%s">%s</a>', $value, $key);
                        }
                        ?>
                    </div>
                    <?php
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
                    <div class="BorderBlock" style="margin-top: 1px;">
                        <div class="TitleCenter">User</div>
                        <select id="OPTUser" style="width: 100%;box-sizing: border-box;">
                            <?php
                            foreach ($user->GetUserList() as $value) {
                                printf('<option value="%s">%s</option>', $value["id"], $value["alias"]);
                            }
                            ?>
                        </select>

                    </div>
                    <div class="BorderBlock">
                        <div class="TitleCenter">Library</div>
                        <select id="OptLibrary" style="width: 99%;">                        
                        </select>
                        <audio id="AudioSrc"></audio>

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
                    <?php
                    echo '<div class="BorderBlock" style="margin-top: 1px;">';
                    echo '  <div class="TitleCenter">Event</div>';
                    foreach ($event->GetComingEvent(Event_Database::Access_Public) as $value) {
                        echo '<div>';
                        printf('<a class="MenuLink" href="../Event/Viewer.php?id=%s"><span style="font-weight: bold;">%s</span>', $value["id"], $value["name"]);
                        printf('<div style="color: black;" >%s</div></a>', $value["description"]);
                        echo '</div><hr>';
                    }
                    echo '</div>';
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
    header("location: ../../../../../../DefaultPages/Offline.php");
}
return;
?>
<div class="Container">

    <div class="Section">
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

            <audio id="AudioSrc" ></audio>


        </div>
    </div>

</div>