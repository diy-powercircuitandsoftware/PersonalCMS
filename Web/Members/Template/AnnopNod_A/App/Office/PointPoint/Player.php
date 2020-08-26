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
            <title>Player</title>
            <link rel="stylesheet" type="text/css" href="../../../../../../css/HolyGrail.css">
            <link rel="stylesheet" type="text/css" href="../../../../../../css/PersonalCMS.css">
          
            <script>
                var ss = new SSQueryFW();
                ss.DocumentReady(function () {

                    var pp = new PointPoint();
                    var render = document.getElementById("DomRender").appendChild(new pp.SlidesPlayer());
                    var Slides = [];

                    render.addEventListener("mousedown", function (e) {
                       // if (e.target == this) {
                        render.Next();
                       // }
                    });
                    render.EndOfLayer = function () {
                        var val = parseInt(ss.S("#BNGoto").Val());
                        if (val < Slides.length - 1) {
                            ss.S("#BNGoto").Val(val + 1).Change();
                        } else {
                            render.PaperSize("800px", "600px");
                            var str = "<h1 style=\"text-align: center;\">End of Presentation</h1>";
                            render.Html(str);
                        }


                    };

                    window.addEventListener("resize", function () {
                        //    render.style.marginTop = ((window.innerHeight / 2) - parseInt(render.style.height) / 2) + "px";
                    });
                    window.addEventListener("keydown", function (e) {
                        if (e.keyCode == 17) {
                            render.ctrl = true;
                        }
                    });
                    window.addEventListener("keyup", function (e) {
                        if (e.keyCode == 17) {
                            render.ctrl = false;
                        } else if (e.keyCode == 36) {
                            render.SetSlide(0);
                        } else if (e.keyCode == 37) {
                            render.PrevSlide();
                        } else if (e.keyCode == 39) {
                            render.NextSlide();
                        }
                    });


                    if (ss.URLParam()["path"] !== undefined) {
                        ss.Post("../../../../Api/Ajax/PointPoint/GetMetadata.php", {"path": ss.URLParam()["path"]}, function (data) {
                            data = JSON.parse(data);
                            render.Metadata = data;
                            document.title = data["Name"];
                            var slidescount = parseInt(data["Data"]["slidescount"]);
                            for (var i = 0; i < slidescount; i++) {
                                Slides[i] = null;
                                ss.S("#BNGoto").Append("<option></option>").Val(i).Html(i + 1);
                            }
                            ss.S("#BNGoto").Val("-1").Change();
                        });
                    }
                    function LoadSlide(i) {
                        if (Slides[i] == null) {
                            var sndajax = {};
                            sndajax["path"] = ss.URLParam()["path"];
                            sndajax["index"] = i;
                            ss.Post("../../../../Api/Ajax/PointPoint/GetSlideIndex.php", sndajax, function (data) {
                                data = JSON.parse(data);
                                Slides[i] = {};
                                Slides[i].Metadata = data.Metadata;
                                Slides[i].Layer = data.ObjectData;
                                LoadSlide(i);
                            });
                        } else {
                            var data = Slides[i];
                            render.Clear();
                            render.PaperSize(data.Metadata.Dimension.Width, data.Metadata.Dimension.Height);
                            if (data.Metadata.Audio.AudioType == 1) {
                                render.PlayAudio("../../../../Api/Action/PointPoint/LoadAudio.php" + ss.JsonToQueryString({"path": ss.URLParam()["path"], "name": data.Metadata.Audio.AudioPath}));
                            } else if (data.Metadata.Audio.AudioType == 3) {
                                render.PlayAudio("../sound/pointpoint/" + data.Metadata.Audio.AudioPath);
                            }
                            render.Animation(data.Metadata.Animation.Animation, data.Metadata.Animation.AnimationTime);
                            var layer = Slides[i].Layer;

                            for (var i = 0; i < layer.length; i++) {
                                if (layer[i].ObjectType == "Text") {
                                    if (layer[i].Audio.AudioType == 3) {
                                        render.AddTextBox(layer[i].Code, layer[i].Css, layer[i].Animation.Animation, layer[i].Animation.AnimationTime, "../sound/pointpoint/" + layer[i].Audio.AudioPath);
                                    } else {
                                        render.AddTextBox(layer[i].Code, layer[i].Css, layer[i].Animation.Animation, layer[i].Animation.AnimationTime, "");
                                    }

                                } else if (layer[i].ObjectType == "Image") {
                                    if (layer[i].Audio.AudioType == 3) {
                                        var imgpath = "../../../../Api/Action/PointPoint/LoadImage.php" + ss.JsonToQueryString({
                                            "path": ss.URLParam()["path"],
                                            "imagepath": layer[i].Embed.Path,
                                            "width": layer[i].Dimension.Width,
                                            "height": layer[i].Dimension.Height
                                        });
                                        render.AddImage(imgpath, layer[i].Css, layer[i].Animation.Animation, layer[i].Animation.AnimationTime, "../sound/pointpoint/" + layer[i].Audio.AudioPath);

                                    } else {
                                        var imgpath = "../../../../Api/Action/PointPoint/LoadImage.php" + ss.JsonToQueryString({
                                            "path": ss.URLParam()["path"],
                                            "imagepath": layer[i].Embed.Path,
                                            "width": layer[i].Dimension.Width,
                                            "height": layer[i].Dimension.Height
                                        });
                                        render.AddImage(imgpath, layer[i].Css, layer[i].Animation.Animation, layer[i].Animation.AnimationTime, "");
                                    }
                                }

                            }

                        }
                    }
                    ss.S("#BNGoto").Change(function () {
                        if (this.value !== "-1") {
                            var toint=parseInt(this.value);
                            LoadSlide(toint);
                             ss.S("#LabPage").Html(toint+1);
                        } else {
                            render.PaperSize("800px", "600px");
                            var str = "<h1 style=\"text-align: center;\">" + render.Metadata.Name + "</h1>";
                            str = str + "<br>";
                            str = str + "<h3 style=\"text-align: center;\">" + render.Metadata.Data.author + "</h3>";
                            render.Html(str);
                             ss.S("#LabPage").Html("Title");
                        }
                    });

                    /* render.EndObject = function () {
                         
                     render.NextSlide();
                         
                     };
                     render.EndSlide = function () {
                     render.Clear();
                     render.AddTextBox("<h1>End Presentation</h1>", "text-align: center;");
                     render.style.background = "cornsilk";
                     document.getElementById("LabPage").innerHTML = "End";
                     };
                     render.MouseDown = function () {
                     animation.ClearAllTimer();
                     if (!this.ctrl) {
                     render.StopAudio();
                     render.NextObject();
                     }
                     };
                     render.ObjectChange = function (curobj) {
                     var cb = function (dom) {
                     if (curobj["Animation"]) {
                     var objanim = curobj["Animation"];
                     var func = animation.Animation[objanim.Animation];
                     if (typeof func === "function") {
                     var ani = func(dom);
                     ani.Start(objanim.AnimationTime);
                     }
                     }
                         
                     };
                     if (curobj["ObjectType"] == "Text") {
                     render.AddTextBox(curobj["Code"], curobj["Css"], cb);
                     } else if (curobj["ObjectType"] == "Image") {
                     if (curobj["FileType"] == "Embed") {
                     var imgpath = "../../../../Api/Action/PointPoint/LoadImage.php" + ss.JsonToQueryString({
                     "path": ss.URLParam()["path"],
                     "imagepath": curobj["Path"],
                     "width": curobj["Width"],
                     "height": curobj["Height"],
                     "cache": 60 * 15
                     });
                     render.AddImage(imgpath, curobj["Css"], cb);
                     }
                     }
                         
                     if (Object.keys(curobj["Audio"]).length > 0) {
                     if (curobj["Audio"].AudioType == "1") {
                     render.PlayAudio("../../../../Api/Action/PointPoint/LoadAudio.php" + ss.JsonToQueryString({"path": ss.URLParam()["path"], "name": curobj["Audio"].AudioPath}));
                     } else if (curobj["Audio"].AudioType == "3") {
                     render.PlayAudio("../sound/pointpoint/" + curobj["Audio"].AudioPath);
                     }
                     }
                         
                         
                     };
                     render.SlideChange = function (v) {
                     document.getElementById("LabPage").innerHTML = this.GetSlideIndex();
                     ss.S("#BNGoto").Val(this.GetSlideIndex());
                     render.Clear();
                     render.style.background = v.Background || "cornsilk";
                     render.ClearSlideObject();
                     for (var i = 0; i < v.Object.length; i++) {
                     render.AddSlideObject(v.Object[i]);
                     }
                     if (Object.keys(v.Animation).length > 0) {
                     var func = animation.Animation[v.Animation.Animation];
                     if (typeof func === "function") {
                     var ani = func(render);
                     ani.Start(v.Animation.AnimationTime);
                     }
                     }
                     if (Object.keys(v.Audio).length > 0) {
                     if (v.Audio.AudioType == "1") {
                     render.PlayAudio("../../../../Api/Action/PointPoint/LoadAudio.php" + ss.JsonToQueryString({"path": ss.URLParam()["path"], "name": v.Audio.AudioPath}));
                     } else if (v.Audio.AudioType == "3") {
                     render.PlayAudio("../sound/pointpoint/" + v.Audio.AudioPath);
                     }
                     }
                         
                         
                     };
                         
                         
                         
                     */


                });
            </script>
        </head>
        <body  class="HolyGrail">

            <div style="text-align: right;">
                <label id="LabPage" style="color: burlywood;font-size: xx-large;">Start</label>
            </div>
            <div id="DomRender" style="z-index: 1" >

            </div>
            <div style="color: burlywood;z-index: 2;position: fixed;bottom: 0;width: 100%;">
                <label>Goto:</label>
                <select id="BNGoto">
                    <option value="-1">Title</option>

                </select>
            </div>



        </body>
    </html>
    <?php
} else {
   header("location: ../../../../../Auth/Login.php");
    session_destroy();
}
