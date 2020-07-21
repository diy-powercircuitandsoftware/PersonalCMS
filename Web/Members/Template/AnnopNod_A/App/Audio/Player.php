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
            <script src="../../../../../js/audio/Player.js"></script>
            <script src="../../../../../js/dom/SSQueryFW.js"></script>
            <script src="../../../../../js/dom/PlayingList.js"></script>
            <script src="../../../../../js/io/Ajax.js"></script>
            <style>
                #AudioList{
                    background-color: cornsilk;
                    margin-top: 3px;
                    cursor: pointer;
                }
                .Playing{
                    background-color: burlywood;
                    font-weight: bold;
                }

            </style>
            <script>
                var ss = new SSQueryFW();
                ss.DocumentReady(function () {
                    var ajax = new Ajax();
                    var audiosrc = document.getElementById("AudioSrc");
                    var playlist = new PlayingList(document.getElementById("AudioList"));


                    playlist.Select(function (v) {
                        audiosrc.pause();                     
                        audiosrc.src = "../../../../Api/Action/Files/DownloadFiles.php?path=" + v;
                        var pp = audiosrc.play();
                        if (pp !== undefined) {
                            pp.then(function () {
                                
                            }).catch(function (error) {
                                
                            });
                        }
                    });
                    ajax.Post("../../../../Api/Ajax/Audio/GetPlayList.php", {}, function (data) {
                        data = JSON.parse(data);

                        for (var i in data) {
                            ss.S("#OptLibrary").Append(data[i], data[i]);
                        }
                        ss.S("#OptSelectLib").Change();
                    });




                    ss.S("#OptLibrary").Change(function () {
                        ajax.Get("../../../../Api/Ajax/Audio/GetAudioList.php", {"Name": this.value}, function (data) {
                            data = JSON.parse(data);
                            playlist.Empty();
                            for (var i in data) {
                                playlist.AddList(data [i]["path"], data [i]["name"]);
                            }

                        });
                    });

                    return;
















                    var CanvasVisualizer = document.getElementById("CanvasVisualizer");
                    var audiomanager = new AudioManager();
                    var audiovisualizer = new AudioVisualizer();
                    var audiolist = document.getElementById("AudioList").appendChild(new PlayingList());
                    audiovisualizer.SetUp(audiomanager.GetAnalyser(), CanvasVisualizer);
                    audiomanager.SetUp(AudioSrc);
                    var Equalizer = audiomanager.Template.Equalizer();

                    audiolist.Click = function (d) {
                        AudioSrc.src = "../../../Api/Action/Files/DownloadFile.php?id=" + btoa(d.getAttribute("url"));
                        audiomanager.ResumeCTX();
                        AudioSrc.play();
                        if (this.Last !== undefined) {
                            this.Last.setAttribute("class", "");
                        }
                        d.setAttribute("class", "Playing");
                    };

                    for (var i in Equalizer.Filter) {
                        var f = audiomanager.AddFilter(Equalizer.Filter[i]);
                        var feq = Equalizer.Filter[i].frequency;
                        var inputrange = ss.S("#EqualizerList").Append("<div></div>").Html("<div style='text-align: center;'>" + feq + "</div>").Append('<input class="EqualizerGain" type="range" min="-12" max="12" value="0" step="0.25" />');
                        inputrange.Data("f_index", f.AddedIndex).Data("f", feq).Change(function () {
                            audiomanager.FilterGain(this.getAttribute("data-f_index"), this.value);
                            this.title = this.value;
                        });
                    }
                    for (var k in Equalizer.Presets) {
                        ss.S("#EqualizerPresetsList").Append('<option></option>').Val(k).Html(k);
                    }
                    ss.S("#EqualizerPresetsList").Change(function () {
                        var list = Equalizer.Presets[this.value];
                        for (var k in list) {
                            ss.S(".EqualizerGain[data-f='" + k + "']").Val(list[k]);
                        }
                        ss.S(".EqualizerGain").Change();
                    });

                    AudioSrc.addEventListener("ended", function () {
                        audiomanager.ResumeCTX();
                        var next = audiolist.GetNext();
                        var v = ss.S("#PlayMode").Val();
                        if (v == "0" && (next !== null) && !audiolist.IsLast()) {
                            next.click();
                        } else if (v == "1") {
                            AudioSrc.play();
                        } else if (v == "2" && (next !== null)) {
                            next.click();
                        } else if (v == "3") {
                            var rndnext = audiolist.GetRandom();
                            if (rndnext !== null) {
                                rndnext.click();
                            }
                        }

                    });

                    ss.S("#RangeVolume").Click(function (e) {
                        audiomanager.Volume(this.value);
                    });
                    window.onresize = function () {
                        var Rect = CanvasVisualizer.getBoundingClientRect();
                        CanvasVisualizer.width = Rect.width;
                        CanvasVisualizer.height = Rect.height;
                    };

                    for (var k in audiovisualizer.Visualizer) {
                        ss.S("#VisualizerList").Append("<option></option>").Val(k).Html(k);
                    }
                    function DrawVisualizer() {
                        var v = ss.S("#VisualizerList").Val();
                        setTimeout(function () {
                            audiovisualizer.Visualizer[v]();
                            requestAnimationFrame(DrawVisualizer);
                        }, (1000 / 25));

                    }
                    DrawVisualizer();
                    window.onresize();
                    audiomanager.Output();
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
                    <div style="display: flex;flex-direction: column;">
                        <div style="height: 80%;width: 100%;">
                            <canvas id="CanvasVisualizer" style="width: 100%;border-style: solid;border-width: thin;background-color: black;" ></canvas>
                            <audio id="AudioSrc" style="width: 100%;" controls></audio>
                        </div>

                        <div class="BorderBlock" style="margin-top: 1px;"  >

                            <div  id="EqualizerList" style="display: flex;flex-direction: row;flex-wrap: wrap;">
                            </div>
                        </div>

                    </div>
                </main>
                <aside>
                    <div class="BorderBlock">
                        <div class="TitleCenter" style="display: block ">Library</div>
                        <select id="OptLibrary" style="display: block;width: 100%;box-sizing: border-box;">
                            <option>==Select==</option>


                        </select>
                        <div id="AudioList"></div>
                    </div>
                    <div class="BorderBlock">
                        <div class="TitleCenter" style="display: block ">Play</div>
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
