<?php
session_start();
include_once '../../../../../Class/DB/Config/DB/Config.php';
include_once '../../../../../Class/DB/Config/DB/Software.php';
include_once '../../../../../Class/DB/Com/Blog/Viewer.php';
include_once '../../../../../Class/DB/Com/Category/Viewer.php';
include_once '../../../../../Class/DB/Com/Events/Viewer.php';
include_once '../../../../../Class/DB/Com/Module/LoadModule.php';
include_once '../../../../../Class/DB/Com/User/Profile.php';
$DBConfig = new Config_DB_Config();
$SC = new Config_DB_Software($DBConfig);
$Blog = new Com_Blog_Viewer($DBConfig);
$Category = new Com_Category_Viewer($DBConfig);
$Event = new Com_Events_Viewer($DBConfig);
$User = new Com_User_Profile($DBConfig);
$Module = new Com_Module_LoadModule($DBConfig);
$DBConfig->Open();
if ($SC->Online()) {
    ?>
    <!DOCTYPE html>
    <html>
        <head>
            <meta charset="UTF-8">
            <title><?php echo $SC->GetName() . ' Blog'; ?> </title>
            <link rel="stylesheet" type="text/css" href="../css/Page.css">
            <script src="../../../../js/dom/SSQueryFW.js"></script>
            <script src="../../../../js/dom/SuperDialog.js"></script>
            <script src="../../../../js/dom/SearchBox.js"></script>
            <script src="../../../../js/image/ImageList.js"></script>
            <script src="../../../../js/image/SlideShow.js"></script>
            <script src="../../../../js/player/PlayingList.js"></script>
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
                    var ImageShow = document.getElementById("ImageShow").appendChild(new SlideShow());
                    var ImagePlayList = document.getElementById("ImagePlayList").appendChild(new PlayingList());
                    var ImgList = new ImageList();
                    var SB = document.getElementById("SearchBox").appendChild(new SearchBox());
                    var SD = new SuperDialog();
                    AudioSrc.PlayIndex = 0;
                    AudioSrc.PlayList = [];

                    ImageShow.width = 1920;
                    ImageShow.height = 1080;

                    AudioSrc.addEventListener("ended", function () {
                        this.PlayIndex = (this.PlayIndex + 1) % this.PlayList.length;
                        this.src = this.PlayList[this.PlayIndex];
                        this.play();
                    });

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
                        rt(this.GetPreviousImage(), this.GetCurrentImage(), parseInt(ImageShow.change), 60, function () {
                            setTimeout(function () {
                                if (ss.S("#BNPlay").Attr("playing") == "1") {
                                    ImgList.Next();
                                } else {
                                    ss.S("#BNPlay").Attr("lock", "0");
                                }
                            }, parseInt(ImageShow.hold) * 1000);
                            ss.S("#ImageRangeViewer").Val(ImgList.Index);
                            ss.S("#LabPlayIndex").Html(ImgList.Index);
                        });
                    };
                    SB.Input = function (v) {
                        ss.Get("../../../Api/ShareAjax/User/SearchAlias.php", {"Alias": v}, function (data) {
                            data = JSON.parse(data);
                            for (var i = 0; i < data.length; i++) {
                                SB.AddList(data[i]["userid"], data[i]["alias"]);
                            }
                        });
                    };
                    SB.CallbackValue = function (v) {
                        ss.Post("../../../Api/ShareAjax/Photo/GetPlayListByUserID.php", {"UserID": v}, function (data) {
                            data = JSON.parse(data);
                            ImagePlayList.Empty();
                            for (var i = 0; i < data.length; i++) {
                                var l = ImagePlayList.AddList(data[i]["name"]);
                                l.setAttribute("data-haspassword", data[i]["haspassword"]);
                                l.setAttribute("data-id", data[i]["id"]);
                                l.setAttribute("data-hold", data[i]["hold_t"]);
                                l.setAttribute("data-change", data[i]["change_t"]);
                                l.setAttribute("class", "PlayItem");
                            }
                        });
                    };
                    ImagePlayList.Click = function (d) {
                        ImageShow.hold = d.getAttribute("data-hold");
                        ImageShow.change = d.getAttribute("data-change");

                        if (d.getAttribute("data-haspassword") == "1") {
                            SD.Login(function (sddata) {
                                ss.Post("../../../Api/ShareAjax/Photo/AuthPlayList.php", {"ID": d.getAttribute("data-id"), "AuthName": sddata["UserName"], "Password": sddata["Password"]}, function (auth) {
                                    if (auth == "1") {
                                        ss.Post("../../../Api/ShareAjax/Photo/GetFileFromPlayList.php", {"ID": d.getAttribute("data-id"), "StartID": 0}, function (data) {
                                            AudioSrc.pause();
                                            AudioSrc.PlayList = [];
                                            ImgList.ClearImageList();
                                            data = JSON.parse(data);
                                            for (var i = 0; i < data.length; i++) {
                                                var ext = data[i]["filepath"].split('.').pop();
                                                var qdata = ss.JsonToQueryString(data[i]);
                                                if (["jpg", "jpeg", "png", "gif"].indexOf(ext.toLowerCase()) >= 0) {
                                                    ImgList.AddImage("../../../Api/ShareAction/Photo/DownloadFileImageSlider.php" + qdata);
                                                } else if (["mp3", "wma"].indexOf(ext.toLowerCase()) >= 0) {
                                                    AudioSrc.PlayList.push("../../../Api/ShareAction/Photo/DownloadFileImageSlider.php" + qdata);
                                                }
                                            }
                                            if (AudioSrc.PlayList.length > 0) {
                                                AudioSrc.src = AudioSrc.PlayList[0];
                                            }
                                        });
                                    } else {
                                        SD.Alert("Password Incorrect").ZIndex(1000);
                                    }

                                });
                                return true;

                            }).ZIndex(999);
                        } else {
                            ss.Post("../../../Api/ShareAjax/Photo/GetFileFromPlayList.php", {"ID": d.getAttribute("data-id"), "StartID": 0}, function (data) {
                                AudioSrc.pause();
                                AudioSrc.PlayList = [];
                                ImgList.ClearImageList();
                                data = JSON.parse(data);
                                for (var i = 0; i < data.length; i++) {
                                    var ext = data[i]["filepath"].split('.').pop();
                                    var qdata = ss.JsonToQueryString(data[i]);
                                    if (["jpg", "jpeg", "png", "gif"].indexOf(ext.toLowerCase()) >= 0) {
                                        ImgList.AddImage("../../../Api/ShareAction/Photo/DownloadFileImageSlider.php" + qdata);
                                    } else if (["mp3", "wma"].indexOf(ext.toLowerCase()) >= 0) {
                                        AudioSrc.PlayList.push("../../../Api/ShareAction/Photo/DownloadFileImageSlider.php" + qdata);
                                    }
                                }
                                if (AudioSrc.PlayList.length > 0) {
                                    AudioSrc.src = AudioSrc.PlayList[0];
                                }

                            });
                        }


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
                            AudioSrc.pause();
                        } else {
                            this.setAttribute("playing", "1");
                            this.innerHTML = "Pause";
                            if (this.getAttribute("lock") == "0") {
                                this.setAttribute("lock", "1");
                                ImgList.Next();

                            }
                            AudioSrc.play();
                        }
                    });
                    ss.S("#ImageRangeViewer").Change(function () {
                        this.setAttribute("seek", "true");
                        this.setAttribute("current", this.value);
                    });
                    ss.S(".BNUserList").Click(function () {
                        SB.ChangeValue(this.innerHTML);
                       ss.Post("../../../Api/ShareAjax/Photo/GetPlayListByUserID.php", {"UserID": this.getAttribute("data-id")}, function (data) {
                            data = JSON.parse(data);
                            ImagePlayList.Empty();
                            for (var i = 0; i < data.length; i++) {
                                var l = ImagePlayList.AddList(data[i]["name"]);
                                l.setAttribute("data-haspassword", data[i]["haspassword"]);
                                l.setAttribute("data-id", data[i]["id"]);
                                l.setAttribute("data-hold", data[i]["hold_t"]);
                                l.setAttribute("data-change", data[i]["change_t"]);
                                l.setAttribute("class", "PlayItem");
                            }
                        });
                    });
                });
            </script>
        </head>
        <body >
            <div id="Header" style="position: static;">
                <h1  style="width: 100%;text-align: center;"><?php echo $SC->GetName(); ?> Blog</h1>
            </div>
            <div class="Container">
                <div class="Nav">
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
                        <a href="../Event/index.php">Viewer</a>
                    </div>
                    <div class="BorderBlock" style="margin-top: 1px;">
                        <label class="Title">Files</label>
                        <a href="../Files/index.php">Viewer </a>
                    </div>

                    <div class="BorderBlock" style="margin-top: 1px;">
                        <label class="Title">Photo</label>
                        <span style="font-weight: bold;">ImageSlider </span>
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
                <div class="Aside">
                    <div  class="BorderBlock" style="margin-top: 1px;">

                        <label class="Title">User</label>
                        <ul>
                            <?php
                            foreach ($User->GetUserList() as $value) {
                                printf('<li><a class="BNUserList" data-id="%s">%s</a></li>', $value["userid"], $value["alias"]);
                            }
                            ?>
                        </ul>

                        <div id="SearchBox" class="BorderBlock" style="margin-top: 1px;">

                        </div>
                    </div>
                    <div class="BorderBlock" id="ImagePlayList">
                        <span  class="Title">PlayList</span>

                    </div>
                    <div class="BorderBlock">
                        <span  class="Title">Event</span>
                        <?php
                        foreach ($Event->GetCurrentEvent(Config_DB_Config::Access_Mode_Public) as $value) {
                            echo '<div  >';
                            printf('<a href="../Event/index.php?id=%s"><span style="font-weight: bold;">%s</span>', $value["id"], $value["name"]);
                            printf('<div style="color: black;">%s</div></a>', $value["description"]);
                            echo '</div><hr>';
                        }
                        ?>
                    </div>
                    <?php
                    foreach ($Module->LoadModule(Com_Module_LoadModule::Layout_Aside, Config_DB_Config::Access_Mode_Public) as $value) {
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
    header("location: ../Error/Offline.php");
}
 