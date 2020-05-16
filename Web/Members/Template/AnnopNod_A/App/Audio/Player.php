<?php
session_start();
include_once '../../../../../Class/DB/Config/DB/Config.php';
include_once '../../../../../Class/DB/Config/DB/Software.php';
include_once '../../../../../Class/DB/Com/User/SessionManager.php';
include_once '../../../../../Class/DB/Com/User/Profile.php';
include_once '../../../../../Class/DB/Com/Audio/PlayList_Manager.php';
include_once '../../../../../Class/DB/Com/Events/Viewer.php';
include_once '../../../../../Class/DB/Com/Module/LoadModule.php';
include_once '../../../../../Class/DB/Com/User/LoadModule.php';
$DBConfig = new Config_DB_Config();
$SC = new Config_DB_Software($DBConfig);
$Sess = new Com_User_SessionManager($DBConfig);
$User = new Com_User_Profile($DBConfig);
$Event = new Com_Events_Viewer($DBConfig);
$Module = new Com_Module_LoadModule($DBConfig);
$PL = new Com_Audio_PlayList_Manager($DBConfig);
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
            <script src="../../../../js/audio/AudioManager.js"></script>
            <script src="../../../../js/audio/AudioVisualizer.js"></script>
            <script src="../../../../js/player/PlayingList.js"></script>
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
                    var AudioSrc = document.getElementById("AudioSrc");
                    var CanvasVisualizer = document.getElementById("CanvasVisualizer");
                    var audiomanager = new AudioManager();
                    var audiovisualizer = new AudioVisualizer();
                    var audiolist = document.getElementById("AudioList").appendChild(new PlayingList());
                    audiovisualizer.SetUp(audiomanager.GetAnalyser(), CanvasVisualizer);
                    audiomanager.SetUp(AudioSrc);
                    var Equalizer = audiomanager.Template.Equalizer();
                    ss.S("#OptLibrary").Change(function (v) {
                        if (this.value == "-1") {
                            ss.Get("../../../Api/Ajax/AudioPlayer/GetAllAudioFilesName.php", {}, function (data) {
                                audiolist.Empty();
                                data = JSON.parse(data);
                                for (var i in data) {
                                    audiolist.AddList(data[i]["name"]).setAttribute("url", data[i]["fullpath"]);
                                }
                            });
                        } else if (this.value !== "") {
                            ss.Get("../../../Api/Ajax/AudioPlayer/GetAudioFilesFromPlayList.php", {"PlayListID": this.value}, function (data) {
                                audiolist.Empty();
                                data = JSON.parse(data);
                                for (var i in data) {
                                    audiolist.AddList(data[i]["name"]).setAttribute("url", data[i]["fullpath"]);
                                }
                            });
                        }
                    });
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
        <body >

            <div id="Header" >
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
                            <li><span style="font-weight: bold;">Player</span></li>
                            <li><a href="PlayList.php">PlayList</a></li>
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
                <div class="Section">
                    <div style="display: flex;flex-direction: column;">
                        <div style="height: 80%;width: 100%;">
                            <canvas id="CanvasVisualizer" style="width: 100%;border-style: solid;border-width: thin;background-color: black;" ></canvas>
                            <audio id="AudioSrc" style="width: 100%;" controls></audio>
                        </div>

                        <div class="BorderBlock" style="margin-top: 1px;"  >
                            <label class="Title">Equalizer</label>
                            <span>Profile:</span>
                            <select id="EqualizerPresetsList">
                            </select>
                            <div  id="EqualizerList" style="display: flex;flex-direction: row;flex-wrap: wrap;">
                            </div>
                        </div>
                        <div class="BorderBlock" style="margin-top: 1px;" >
                            <label class="Title">Visualizer</label>
                            <span>Profile:</span>
                            <select id="VisualizerList">
                            </select>
                        </div>
                    </div>
                </div>
                <div class="Aside" style="">
                    <div class="BorderBlock">
                        <span class="Title" style="display: block ">Library</span>
                        <select id="OptLibrary" style="width: 99%;">
                            <option>==Select==</option>
                            <option value="-1">* All Audio *</option>
                            <?php
                            foreach ($PL->GetPlayList($_SESSION["UserID"]) as $value) {
                                printf('<option value="%s">%s</option>', $value["id"], $value["name"]);
                            }
                            ?>
                        </select>
                        <div id="AudioList"></div>
                    </div>
                    <div class="BorderBlock" style="margin-top: 1px;">
                        <label class="Title">Playing</label>
                        <table>
                            <tr>
                                <td>Volume:</td>
                                <td><input style="width: 100%;box-sizing: border-box;"  id="RangeVolume" type="range" min="0" max="1" step="0.1" value="1" /></td>
                            </tr>
                            <tr>
                                <td>Play Mode:</td>
                                <td><select id="PlayMode"  style="width: 100%;box-sizing: border-box;">
                                        <option value="0">None</option>
                                        <option value="1">Repeat</option>
                                        <option value="2">Repeat All</option>
                                        <option value="3">Random</option>
                                    </select></td>
                            </tr>
                        </table>
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


            <div class="Hidden">
                <div id="FilesList">

                </div>
            </div>
        </body>
    </html>
    <?php
} else {


     header("location: ../../../Session/AuthUserID.php");


    session_destroy();
}
