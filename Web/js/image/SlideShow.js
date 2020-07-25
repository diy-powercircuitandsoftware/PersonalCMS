class SlideShow {
    constructor(...args) {
        if (args.length === 1 && typeof args[0] === 'string' || args[0] instanceof String) {
            this.canvas = document.querySelector(args[0]).appendChild(document.createElement("CANVAS"));
        } else if (args.length === 1 && args[0] instanceof HTMLElement) {
            this.canvas = args[0].appendChild(document.createElement("CANVAS"));
        } else {
            this.canvas = document.body.appendChild(document.createElement("CANVAS"));
        }

        this.Running = false;
        this.Render = new SlideShow_RenderEngine();
        this.Render.ImageList = new SlideShow_ImageList();
        this.Render.ImageList.ref = this;
        this.Render.ImageList.index = 0;
        this.Render.t = 0;
        this.Render.maxt = 3 * 1000;
        this.Render.transitionslist = [];
        this.Render.transition = null;
        this.Render.canvas = this.canvas;

        this.Render.ImageList.ImageChange = function (v) {
            this.ref.ImageChange(v);
        };
    }
    AddImage(path) {
        this.Render.ImageList.AddImage(path);
    }
    AddTransition(te) {
        if (new te() instanceof SlideShow_TransitionsEngine) {
            this.Render.transitionslist.push(te);
        }
    }
    ImageChange() {

    }
    Size(w, h) {
        this.canvas.width = w;
        this.canvas.height = h;

    }
    Start() {

        if (!this.Running) {
            this.Running = true;

            this.Render.SetAnimate(function (v) {

                if (this.transition !== null) {
                    var command = [];
                    if (this.t === 0) {
                        command = this.transition.Start();
                        this.t = this.t + v;
                    } else if (this.t > this.maxt) {
                        command = this.transition.Finish();
                        this.transition = null;
                        this.t = 0;
                        this.ImageList.index++;
                    } else {
                        command = this.transition.Running(this.t / this.maxt);
                        this.t = this.t + v;
                    }

                    var ctx = this.canvas.getContext('2d');

                    for (var i in command) {
                        var cmd = command[i];
                        if (cmd.command === "Arc") {
                            ctx.arc(cmd.x, cmd.y, cmd.r, cmd.sa, cmd.ea, cmd.acw);
                        } else if (cmd.command === "BeginPath") {
                            ctx.beginPath();
                        } else if (cmd.command === "ClearRect") {
                            ctx.clearRect(cmd.x, cmd.y, cmd.width, cmd.height);
                        } else if (cmd.command === "Clip") {
                            ctx.clip();
                        } else if (cmd.command === "ClosePath") {
                            ctx.closePath();
                        } else if (cmd.command === "DrawImage") {
                            var image = null;
                            if (cmd.image === 1) {
                                image = this.ImageList.GetImage(this.ImageList.index);
                            } else if (cmd.image === 2) {
                                image = this.ImageList.GetImage(this.ImageList.index + 1);
                            }
                            if (image !== null) {
                                ctx.drawImage(image,
                                        cmd.src.x, cmd.src.y, cmd.src.width, cmd.src.height,
                                        cmd.dest.x, cmd.dest.y, cmd.dest.width, cmd.dest.height);
                            }

                        } else if (cmd.command === "Fill") {
                            ctx.fill();
                        } else if (cmd.command === "GlobalAlpha") {
                            ctx.globalAlpha = cmd.value;
                        } else if (cmd.command === "GlobalCompositeOperation") {
                            ctx.globalCompositeOperation = cmd.value;
                        }else if (cmd.command === "Rect") {
                            ctx.rect(cmd.x, cmd.y, cmd.width, cmd.height);
                        }  else if (cmd.command === "Restore") {
                            ctx.restore();
                        } else if (cmd.command === "Save") {
                            ctx.save();
                        }
                    }

                } else {
                    var current = this.ImageList.index;
                    if (current < this.ImageList.Count() - 1) {
                        var rndnum = Math.floor(Math.random() * Math.floor(this.transitionslist.length));
                        var img1 = this.ImageList.GetImageSize(current);
                        var img2 = this.ImageList.GetImageSize(current + 1);
                        this.transition = new this.transitionslist[rndnum](this.canvas, img1, img2);
                    } else if (current === this.ImageList.Count() - 1) {
                        this.ImageList.index = 0;
                    }

                }
            });

            this.Render.Start();
        }
    }
    Stop() {
        this.Render.Stop();
        this.Running = false;
    }

}

class SlideShow_ImageList {
    constructor() {
        this.ImageList = [];
    }
    AddImage(path) {
        var xhttp = new XMLHttpRequest();
        var ref = this;
        xhttp.onreadystatechange = function () {
            if (this.readyState === 4 && this.status === 200) {
                var img = new Image();
                var blob = new Blob([this.response]);
                var reader = new FileReader();
                reader.onload = function (e) {
                    img.src = e.target.result;
                    ref.ImageList.push(img);
                    ref.ImageChange(ref.ImageList.length);
                };
                reader.readAsDataURL(blob);
            }
        };

        xhttp.responseType = "arraybuffer";
        xhttp.open("GET", path, true);
        xhttp.send();

    }
    Clear() {
        this.ImageList = [];
    }
    Count() {
        return   this.ImageList.length;
    }
    GetImage(index) {
        if (index < this.ImageList.length) {
            return  this.ImageList[index];
        }
        return null;
    }

    GetImageSize(index) {
        if (index < this.ImageList.length) {
            return  {
                "width": this.ImageList[index].width,
                "height": this.ImageList[index].height
            };
        }
        return  null;
    }
    ImageChange() {

    }

}

class SlideShow_RenderEngine {
    constructor(fps = 60) {
        this.requestID = 0;
        this.fps = fps;
        this.animate = function () {};
    }
    SetAnimate(animate) {
        this.animate = animate;
    }

    Start() {
        let then = performance.now();
        const interval = 1000 / this.fps;
        const tolerance = 0.1;
        const animateLoop = (now) => {

            const delta = now - then;
            if (delta >= interval - tolerance) {
                then = now - (delta % interval);
                this.animate(delta);
            }
            this.requestID = requestAnimationFrame(animateLoop);
        };
        this.requestID = requestAnimationFrame(animateLoop);
    }

    Stop() {
        cancelAnimationFrame(this.requestID);
    }
}
class SlideShow_TransitionsEngine {
    constructor(canvassize, image1size, image2size) {
        this.canvassize = canvassize;
        this.image1size = image1size;
        this.image2size = image2size;
    }
    Start() {
        var stack = [];
        return stack;
    }
    Running(time) {
        var stack = [];
        return stack;
    }
    Finish() {
        var stack = [];
        return stack;
    }
    Center(rect, ref, ratio = 1) {

        var w = rect.width * ratio;
        var h = rect.height * ratio;
        var x = ref.width / 2 - w / 2;
        var y = ref.height / 2 - h / 2;
        return {"x": x, "y": y, "width": w, "height": h, "ratio": ratio};
    }
    Rect(x, y, w, h) {
        return {
            "x": x,
            "y": y,
            "width": w,
            "height": h
        };
    }
    Scale(src, dest) {
        return    Math.min(dest.width / src.width, dest.height / src.height);
    }
}
class SlideShow_Transition_FadeOutFadeIn extends SlideShow_TransitionsEngine {
    Start() {
        this.CenterA = this.Center(this.image1size, this.canvassize, this.Scale(this.image1size, this.canvassize));
        this.CenterB = this.Center(this.image2size, this.canvassize, this.Scale(this.image2size, this.canvassize));
        return[{
                "command": "GlobalAlpha",
                "value": 1
            }, {
                "command": "DrawImage",
                "image": 1,
                "src": this.Rect(0, 0, this.image1size.width, this.image1size.height),
                "dest": this.CenterA
            }];
    }
    Running(time) {
        var stack = [];
        stack.push({
            "command": "ClearRect",
            "x": 0,
            "y": 0,
            "width": this.canvassize.width,
            "height": this.canvassize.height,
        });
        if (time < 0.5) {

            stack.push({
                "command": "GlobalAlpha",
                "value": 1 - (2 * time)
            }, {
                "command": "DrawImage",
                "image": 1,
                "src": this.Rect(0, 0, this.image1size.width, this.image1size.height),
                "dest": this.CenterA
            });

        }
        if (time > 0.5) {
            stack.push({
                "command": "GlobalAlpha",
                "value": (2 * time) - 1
            }, {
                "command": "DrawImage",
                "image": 2,
                "src": this.Rect(0, 0, this.image2size.width, this.image2size.height),
                "dest": this.CenterB
            });
        }
        return stack;
    }
    Finish() {
        return[{
                "command": "GlobalAlpha",
                "value": 1
            }];
    }
}
;
class SlideShow_Transition_CircleOut extends SlideShow_TransitionsEngine {
    Start() {
        this.CenterA = this.Center(this.image1size, this.canvassize, this.Scale(this.image1size, this.canvassize));
        this.CenterB = this.Center(this.image2size, this.canvassize, this.Scale(this.image2size, this.canvassize));
        this.MaxCircle = Math.min(this.canvassize.width, this.canvassize.height);
        return[{
                "command": "DrawImage",
                "image": 1,
                "src": this.Rect(0, 0, this.image1size.width, this.image1size.height),
                "dest": this.CenterA
            }];
    }
    Running(time) {
        var stack = [];
        stack.push({
            "command": "Save"
        }, {
            "command": "BeginPath"
        }, {
            "command": "Arc",
            "x": this.canvassize.width / 2,
            "y": this.canvassize.height / 2,
            "r": this.MaxCircle * time,
            "sa": 0,
            "ea": 2 * Math.PI,
            "acw": false
        }, {
            "command": "ClosePath"
        }, {
            "command": "GlobalCompositeOperation",
            "value": "destination-out"
        }, {
            "command": "Fill"
        }, {
            "command": "GlobalCompositeOperation",
            "value": "source-over"
        }, {
            "command": "Clip"
        }, {
            "command": "DrawImage",
            "image": 2,
            "src": this.Rect(0, 0, this.image2size.width, this.image2size.height),
            "dest": this.CenterB
        }, {
            "command": "Restore"
        });
        return stack;
    }
    Finish() {
        return[{
                "command": "ClearRect",
                "x": 0,
                "y": 0,
                "width": this.canvassize.width,
                "height": this.canvassize.height
            }, {
                "command": "DrawImage",
                "image": 2,
                "src": this.Rect(0, 0, this.image2size.width, this.image2size.height),
                "dest": this.CenterB
            }];
    }
};
class SlideShow_Transition_Corner extends SlideShow_TransitionsEngine {
    Start() {
        this.CenterA = this.Center(this.image1size, this.canvassize, this.Scale(this.image1size, this.canvassize));
        this.CenterB = this.Center(this.image2size, this.canvassize, this.Scale(this.image2size, this.canvassize));
        this.MaxCircle = Math.min(this.canvassize.width, this.canvassize.height);
        return[{
                "command": "ClearRect",
                "x": 0,
                "y": 0,
                "width": this.canvassize.width,
                "height": this.canvassize.height
            },{
                "command": "DrawImage",
                "image": 1,
                "src": this.Rect(0, 0, this.image1size.width, this.image1size.height),
                "dest": this.CenterA
            }];
    }
    Running(time) {
        var stack = [];
        stack.push({
            "command": "Save"
        }, {
            "command": "BeginPath"
        }, {
             "command": "Rect",
                "x": 0,
                "y": 0,
                "width": this.canvassize.width*time,
                "height": this.canvassize.height*time
        }, {
            "command": "ClosePath"
        }, {
            "command": "GlobalCompositeOperation",
            "value": "destination-out"
        }, {
            "command": "Fill"
        }, {
            "command": "GlobalCompositeOperation",
            "value": "source-over"
        }, {
            "command": "Clip"
        }, {
            "command": "DrawImage",
            "image": 2,
            "src": this.Rect(0, 0, this.image2size.width, this.image2size.height),
            "dest": this.CenterB
        }, {
            "command": "Restore"
        });
        return stack;
    }
    Finish() {
        return[ ];
    }
};
/*
   
 
 
 function SlideShow() {
 var Method = document.createElement("canvas");
 Method.style.cssText = "width:100%";
 Method.FPS = 60;
 Method.Transitions = {};
 
 Method.Math.SplitBlock = function (xcount, ycount, width, height) {
 var barwidth = width / xcount;
 var barheight = height / ycount;
 var tilesdata = [];
 for (var yi = 0; yi < ycount; yi++) {
 for (var xi = 0; xi < xcount; xi++) {
 tilesdata.push({
 "x": xi * barwidth,
 "y": yi * barheight,
 "width": barwidth,
 "height": barheight
 });
 }
 }
 return tilesdata;
 };
 
 
 Method.Transitions.BottomToTop = function (imagea, imageb, s, fps, finish) {
 var CenterA = Method.Math.Center(imagea, Method, Method.Math.Scale(imagea, Method));
 var CenterB = Method.Math.Center(imageb, Method, Method.Math.Scale(imageb, Method));
 var ctx = Method.getContext('2d');
 ctx.drawImage(imagea, 0, 0, imagea.width, imagea.height, CenterA.x, CenterA.y, CenterA.width, CenterA.height);
 Method.Render(fps, 0, s * 1000, function (r) {
 ctx.save();
 ctx.beginPath();
 ctx.rect(0, Method.height * (1 - r.ratio), Method.width, Method.height * r.ratio);
 ctx.closePath();
 ctx.globalCompositeOperation = 'destination-out';
 ctx.fill();
 ctx.globalCompositeOperation = 'source-over';
 ctx.clip();
 ctx.drawImage(imageb, 0, 0, imageb.width, imageb.height, CenterB.x, CenterB.y, CenterB.width, CenterB.height);
 ctx.restore();
 }, function () {
 ctx.clearRect(0, 0, Method.width, Method.height);
 ctx.drawImage(imageb, 0, 0, imageb.width, imageb.height, CenterB.x, CenterB.y, CenterB.width, CenterB.height);
 finish();
 });
 };
 
 
 
 
 };
 Method.Transitions.Eraser = function (imagea, imageb, s, fps, finish) {
 var CenterA = Method.Math.Center(imagea, Method, Method.Math.Scale(imagea, Method));
 var CenterB = Method.Math.Center(imageb, Method, Method.Math.Scale(imageb, Method));
 var ctx = Method.getContext('2d');
 var tilesdata = Method.Math.SplitBlock(12, 12, Method.width, Method.height);
 var arrcount = tilesdata.length;
 ctx.drawImage(imagea, 0, 0, imagea.width, imagea.height, CenterA.x, CenterA.y, CenterA.width, CenterA.height);
 Method.Render(fps, 0, s * 1000, function (r) {
 ctx.save();
 ctx.beginPath();
 
 while ((tilesdata.length / arrcount) > (1 - r.ratio)) {
 var index = tilesdata.splice(0, 1)[0];
 ctx.rect(index["x"], index["y"], index["width"], index["height"]);
 }
 
 ctx.closePath();
 ctx.globalCompositeOperation = 'destination-out';
 ctx.fill();
 ctx.globalCompositeOperation = 'source-over';
 ctx.clip();
 ctx.drawImage(imageb, 0, 0, imageb.width, imageb.height, CenterB.x, CenterB.y, CenterB.width, CenterB.height);
 ctx.restore();
 }, function () {
 ctx.clearRect(0, 0, Method.width, Method.height);
 ctx.drawImage(imageb, 0, 0, imageb.width, imageb.height, CenterB.x, CenterB.y, CenterB.width, CenterB.height);
 finish();
 });
 };
 
 Method.Transitions.FromHorizontalCenter = function (imagea, imageb, s, fps, finish) {
 var CenterA = Method.Math.Center(imagea, Method, Method.Math.Scale(imagea, Method));
 var CenterB = Method.Math.Center(imageb, Method, Method.Math.Scale(imageb, Method));
 var ctx = Method.getContext('2d');
 var halfheight = Method.height / 2;
 ctx.drawImage(imagea, 0, 0, imagea.width, imagea.height, CenterA.x, CenterA.y, CenterA.width, CenterA.height);
 Method.Render(fps, 0, s * 1000, function (r) {
 ctx.save();
 ctx.beginPath();
 ctx.rect(0, halfheight, Method.width, halfheight * r.ratio);
 ctx.rect(0, halfheight * (1 - r.ratio), Method.width, halfheight * r.ratio);
 ctx.closePath();
 ctx.globalCompositeOperation = 'destination-out';
 ctx.fill();
 ctx.globalCompositeOperation = 'source-over';
 ctx.clip();
 ctx.drawImage(imageb, 0, 0, imageb.width, imageb.height, CenterB.x, CenterB.y, CenterB.width, CenterB.height);
 ctx.restore();
 }, function () {
 ctx.clearRect(0, 0, Method.width, Method.height);
 ctx.drawImage(imageb, 0, 0, imageb.width, imageb.height, CenterB.x, CenterB.y, CenterB.width, CenterB.height);
 finish();
 });
 };
 Method.Transitions.FromVerticalCenter = function (imagea, imageb, s, fps, finish) {
 var CenterA = Method.Math.Center(imagea, Method, Method.Math.Scale(imagea, Method));
 var CenterB = Method.Math.Center(imageb, Method, Method.Math.Scale(imageb, Method));
 var ctx = Method.getContext('2d');
 var halfwidth = Method.width / 2;
 ctx.drawImage(imagea, 0, 0, imagea.width, imagea.height, CenterA.x, CenterA.y, CenterA.width, CenterA.height);
 Method.Render(fps, 0, s * 1000, function (r) {
 ctx.save();
 ctx.beginPath();
 ctx.rect(halfwidth, 0, halfwidth * r.ratio, Method.height);
 ctx.rect(halfwidth * (1 - r.ratio), 0, halfwidth * r.ratio, Method.height);
 ctx.closePath();
 ctx.globalCompositeOperation = 'destination-out';
 ctx.fill();
 ctx.globalCompositeOperation = 'source-over';
 ctx.clip();
 ctx.drawImage(imageb, 0, 0, imageb.width, imageb.height, CenterB.x, CenterB.y, CenterB.width, CenterB.height);
 ctx.restore();
 }, function () {
 ctx.clearRect(0, 0, Method.width, Method.height);
 ctx.drawImage(imageb, 0, 0, imageb.width, imageb.height, CenterB.x, CenterB.y, CenterB.width, CenterB.height);
 finish();
 });
 };
 Method.Transitions.HeartOut = function (imagea, imageb, s, fps, finish) {
 var CenterA = Method.Math.Center(imagea, Method, Method.Math.Scale(imagea, Method));
 var CenterB = Method.Math.Center(imageb, Method, Method.Math.Scale(imageb, Method));
 var centerx = Method.width / 2;
 var centerylock = Method.height / 2;
 var max = Math.max(Method.width, Method.height);
 var ctx = Method.getContext('2d');
 ctx.drawImage(imagea, 0, 0, imagea.width, imagea.height, CenterA.x, CenterA.y, CenterA.width, CenterA.height);
 Method.Render(fps, 0, s * 1000, function (r) {
 var r = max * r.ratio;
 var centery = centerylock - (r / 3);
 ctx.save();
 ctx.beginPath();
 ctx.moveTo(centerx, centery);
 ctx.quadraticCurveTo(centerx + (r * 0.5), centery - r, centerx + r, centery);
 ctx.quadraticCurveTo(centerx + (r / 0.90), centery + (r * 0.25), centerx, centery + r);
 ctx.moveTo(centerx, centery);
 ctx.quadraticCurveTo(centerx - (r * 0.5), centery - r, centerx - r, centery);
 ctx.quadraticCurveTo(centerx - (r / 0.90), centery + (r * 0.25), centerx, centery + r);
 ctx.closePath();
 ctx.globalCompositeOperation = 'destination-out';
 ctx.fill();
 ctx.globalCompositeOperation = 'source-over';
 ctx.clip();
 ctx.drawImage(imageb, 0, 0, imageb.width, imageb.height, CenterB.x, CenterB.y, CenterB.width, CenterB.height);
 ctx.restore();
 }, function () {
 ctx.clearRect(0, 0, Method.width, Method.height);
 ctx.drawImage(imageb, 0, 0, imageb.width, imageb.height, CenterB.x, CenterB.y, CenterB.width, CenterB.height);
 finish();
 });
 };
 Method.Transitions.HorizontalBlind = function (imagea, imageb, s, fps, finish) {
 var CenterA = Method.Math.Center(imagea, Method, Method.Math.Scale(imagea, Method));
 var CenterB = Method.Math.Center(imageb, Method, Method.Math.Scale(imageb, Method));
 var ctx = Method.getContext('2d');
 var bar = 12;
 var barheight = Method.height / bar;
 ctx.drawImage(imagea, 0, 0, imagea.width, imagea.height, CenterA.x, CenterA.y, CenterA.width, CenterA.height);
 Method.Render(fps, 0, s * 1000, function (r) {
 ctx.save();
 ctx.beginPath();
 for (var i = 0; i < bar; i++) {
 ctx.rect(0, barheight * i, Method.width, barheight * r.ratio);
 }
 ctx.closePath();
 ctx.globalCompositeOperation = 'destination-out';
 ctx.fill();
 ctx.globalCompositeOperation = 'source-over';
 ctx.clip();
 ctx.drawImage(imageb, 0, 0, imageb.width, imageb.height, CenterB.x, CenterB.y, CenterB.width, CenterB.height);
 ctx.restore();
 }, function () {
 ctx.clearRect(0, 0, Method.width, Method.height);
 ctx.drawImage(imageb, 0, 0, imageb.width, imageb.height, CenterB.x, CenterB.y, CenterB.width, CenterB.height);
 finish();
 });
 };
 Method.Transitions.LeftToRight = function (imagea, imageb, s, fps, finish) {
 var CenterA = Method.Math.Center(imagea, Method, Method.Math.Scale(imagea, Method));
 var CenterB = Method.Math.Center(imageb, Method, Method.Math.Scale(imageb, Method));
 var ctx = Method.getContext('2d');
 ctx.drawImage(imagea, 0, 0, imagea.width, imagea.height, CenterA.x, CenterA.y, CenterA.width, CenterA.height);
 Method.Render(fps, 0, s * 1000, function (r) {
 ctx.save();
 ctx.beginPath();
 ctx.rect(Method.width * r.ratio, 0, Method.width * r.ratio, Method.height);
 ctx.closePath();
 ctx.globalCompositeOperation = 'destination-out';
 ctx.fill();
 ctx.globalCompositeOperation = 'source-over';
 ctx.clip();
 ctx.drawImage(imageb, 0, 0, imageb.width, imageb.height, CenterB.x, CenterB.y, CenterB.width, CenterB.height);
 ctx.restore();
 }, function () {
 ctx.clearRect(0, 0, Method.width, Method.height);
 ctx.drawImage(imageb, 0, 0, imageb.width, imageb.height, CenterB.x, CenterB.y, CenterB.width, CenterB.height);
 finish();
 });
 };
 
 Method.Transitions.Mosaic = function (imagea, imageb, s, fps, finish) {
 var CenterA = Method.Math.Center(imagea, Method, Method.Math.Scale(imagea, Method));
 var CenterB = Method.Math.Center(imageb, Method, Method.Math.Scale(imageb, Method));
 var ctx = Method.getContext('2d');
 var tilesdata = Method.Math.SplitBlock(12, 12, Method.width, Method.height);
 var arrcount = tilesdata.length;
 ctx.drawImage(imagea, 0, 0, imagea.width, imagea.height, CenterA.x, CenterA.y, CenterA.width, CenterA.height);
 Method.Render(fps, 0, s * 1000, function (r) {
 ctx.save();
 ctx.beginPath();
 
 while ((tilesdata.length / arrcount) > (1 - r.ratio)) {
 var index = tilesdata.splice(tilesdata.length * Math.random() | 0, 1)[0];
 ctx.rect(index["x"], index["y"], index["width"], index["height"]);
 }
 
 ctx.closePath();
 ctx.globalCompositeOperation = 'destination-out';
 ctx.fill();
 ctx.globalCompositeOperation = 'source-over';
 ctx.clip();
 ctx.drawImage(imageb, 0, 0, imageb.width, imageb.height, CenterB.x, CenterB.y, CenterB.width, CenterB.height);
 ctx.restore();
 }, function () {
 ctx.clearRect(0, 0, Method.width, Method.height);
 ctx.drawImage(imageb, 0, 0, imageb.width, imageb.height, CenterB.x, CenterB.y, CenterB.width, CenterB.height);
 finish();
 });
 };
 
 Method.Transitions.PageTurn = function (imagea, imageb, s, fps, finish) {
 var CenterA = Method.Math.Center(imagea, Method, Method.Math.Scale(imagea, Method));
 var CenterB = Method.Math.Center(imageb, Method, Method.Math.Scale(imageb, Method));
 var ctx = Method.getContext('2d');
 var p = (Math.sqrt(Math.pow(Method.width, 2) + Math.pow(Method.height, 2))) * 1.2;
 
 Method.Render(fps, 0, s * 1000, function (r) {
 var x = (p * r.ratio) + 0.1;
 var y = (p * r.ratio) + 0.1;
 ctx.save();
 ctx.fillRect(0, 0, Method.width, Method.height);
 ctx.drawImage(imageb, 0, 0, imageb.width, imageb.height, CenterB.x, CenterB.y, CenterB.width, CenterB.height);
 ctx.beginPath();
 ctx.moveTo(y * y / 2 / x + x / 2, 0);
 ctx.lineTo(Method.width * 2, 0);
 ctx.lineTo(0, Method.height * 2);
 ctx.lineTo(0, x * x / 2 / y + y / 2);
 ctx.closePath();
 ctx.globalCompositeOperation = 'destination-out';
 ctx.fill();
 ctx.globalCompositeOperation = 'source-over';
 ctx.clip();
 ctx.drawImage(imagea, 0, 0, imagea.width, imagea.height, CenterA.x, CenterA.y, CenterA.width, CenterA.height);
 ctx.translate(x, y);
 ctx.rotate(Math.atan2(y, x) * 2);
 ctx.scale(-1, 1);
 ctx.drawImage(imagea, 0, 0, imagea.width, imagea.height, CenterA.x, CenterA.y, CenterA.width, CenterA.height);
 ctx.restore();
 
 }, function () {
 ctx.clearRect(0, 0, Method.width, Method.height);
 ctx.drawImage(imageb, 0, 0, imageb.width, imageb.height, CenterB.x, CenterB.y, CenterB.width, CenterB.height);
 finish();
 });
 };
 
 Method.Transitions.RightToLeft = function (imagea, imageb, s, fps, finish) {
 var CenterA = Method.Math.Center(imagea, Method, Method.Math.Scale(imagea, Method));
 var CenterB = Method.Math.Center(imageb, Method, Method.Math.Scale(imageb, Method));
 var ctx = Method.getContext('2d');
 ctx.drawImage(imagea, 0, 0, imagea.width, imagea.height, CenterA.x, CenterA.y, CenterA.width, CenterA.height);
 Method.Render(fps, 0, s * 1000, function (r) {
 ctx.save();
 ctx.beginPath();
 ctx.rect(Method.width * (1 - r.ratio), 0, Method.width * r.ratio, Method.height);
 ctx.closePath();
 ctx.globalCompositeOperation = 'destination-out';
 ctx.fill();
 ctx.globalCompositeOperation = 'source-over';
 ctx.clip();
 ctx.drawImage(imageb, 0, 0, imageb.width, imageb.height, CenterB.x, CenterB.y, CenterB.width, CenterB.height);
 ctx.restore();
 }, function () {
 ctx.clearRect(0, 0, Method.width, Method.height);
 ctx.drawImage(imageb, 0, 0, imageb.width, imageb.height, CenterB.x, CenterB.y, CenterB.width, CenterB.height);
 finish();
 });
 };
 Method.Transitions.SpinRight = function (imagea, imageb, s, fps, finish) {
 var CenterA = Method.Math.Center(imagea, Method, Method.Math.Scale(imagea, Method));
 var CenterB = Method.Math.Center(imageb, Method, Method.Math.Scale(imageb, Method));
 var max = Math.max(Method.width, Method.height);
 var p = 5;
 var m = 0.5;
 var ctx = Method.getContext('2d');
 ctx.drawImage(imagea, 0, 0, imagea.width, imagea.height, CenterA.x, CenterA.y, CenterA.width, CenterA.height);
 Method.Render(fps, 0, s * 1000, function (r) {
 if (r.ratio < 0.8) {
 ctx.save();
 ctx.clearRect(0, 0, Method.width, Method.height);
 ctx.translate(Method.width / 2, Method.height / 2);
 ctx.rotate((12 * 360 * r.ratio) * Math.PI / 180);
 ctx.translate(-(Method.width / 2), -(Method.height / 2));
 ctx.drawImage(imageb, 0, 0, imageb.width, imageb.height, CenterB.x, CenterB.y, CenterB.width, CenterB.height);
 ctx.restore();
 } else {
 ctx.clearRect(0, 0, Method.width, Method.height);
 ctx.drawImage(imageb, 0, 0, imageb.width, imageb.height, CenterB.x, CenterB.y, CenterB.width, CenterB.height);
 }
 }, function () {
 ctx.clearRect(0, 0, Method.width, Method.height);
 ctx.drawImage(imageb, 0, 0, imageb.width, imageb.height, CenterB.x, CenterB.y, CenterB.width, CenterB.height);
 finish();
 });
 };
 Method.Transitions.StarOut = function (imagea, imageb, s, fps, finish) {
 var CenterA = Method.Math.Center(imagea, Method, Method.Math.Scale(imagea, Method));
 var CenterB = Method.Math.Center(imageb, Method, Method.Math.Scale(imageb, Method));
 var max = Math.max(Method.width, Method.height);
 var p = 5;
 var m = 0.5;
 var ctx = Method.getContext('2d');
 ctx.drawImage(imagea, 0, 0, imagea.width, imagea.height, CenterA.x, CenterA.y, CenterA.width, CenterA.height);
 Method.Render(fps, 0, s * 1000, function (r) {
 var r = max * r.ratio;
 ctx.save();
 ctx.translate(Method.width / 2, Method.height / 2);
 ctx.beginPath();
 for (var i = 0; i < p; i++)
 {
 ctx.rotate(Math.PI / p);
 ctx.lineTo(0, -(r * m));
 ctx.rotate(Math.PI / p);
 ctx.lineTo(0, -r);
 }
 ctx.closePath();
 ctx.globalCompositeOperation = 'destination-out';
 ctx.fill();
 ctx.translate(-Method.width / 2, -Method.height / 2);
 ctx.globalCompositeOperation = 'source-over';
 ctx.clip();
 ctx.drawImage(imageb, 0, 0, imageb.width, imageb.height, CenterB.x, CenterB.y, CenterB.width, CenterB.height);
 ctx.restore();
 }, function () {
 ctx.clearRect(0, 0, Method.width, Method.height);
 ctx.drawImage(imageb, 0, 0, imageb.width, imageb.height, CenterB.x, CenterB.y, CenterB.width, CenterB.height);
 finish();
 });
 
 };
 
 Method.Transitions.ToHorizontalCenter = function (imagea, imageb, s, fps, finish) {
 var CenterA = Method.Math.Center(imagea, Method, Method.Math.Scale(imagea, Method));
 var CenterB = Method.Math.Center(imageb, Method, Method.Math.Scale(imageb, Method));
 var ctx = Method.getContext('2d');
 var halfheight = Method.height / 2;
 ctx.drawImage(imagea, 0, 0, imagea.width, imagea.height, CenterA.x, CenterA.y, CenterA.width, CenterA.height);
 Method.Render(fps, 0, s * 1000, function (r) {
 ctx.save();
 ctx.beginPath();
 ctx.rect(0, 0, Method.width, halfheight * r.ratio);
 ctx.rect(0, halfheight + (halfheight * (1 - r.ratio)), Method.width, halfheight * r.ratio);
 ctx.closePath();
 ctx.globalCompositeOperation = 'destination-out';
 ctx.fill();
 ctx.globalCompositeOperation = 'source-over';
 ctx.clip();
 ctx.drawImage(imageb, 0, 0, imageb.width, imageb.height, CenterB.x, CenterB.y, CenterB.width, CenterB.height);
 ctx.restore();
 }, function () {
 ctx.clearRect(0, 0, Method.width, Method.height);
 ctx.drawImage(imageb, 0, 0, imageb.width, imageb.height, CenterB.x, CenterB.y, CenterB.width, CenterB.height);
 finish();
 });
 };
 
 Method.Transitions.ToVerticalCenter = function (imagea, imageb, s, fps, finish) {
 var CenterA = Method.Math.Center(imagea, Method, Method.Math.Scale(imagea, Method));
 var CenterB = Method.Math.Center(imageb, Method, Method.Math.Scale(imageb, Method));
 var ctx = Method.getContext('2d');
 var halfwidth = Method.width / 2;
 ctx.drawImage(imagea, 0, 0, imagea.width, imagea.height, CenterA.x, CenterA.y, CenterA.width, CenterA.height);
 Method.Render(fps, 0, s * 1000, function (r) {
 ctx.save();
 ctx.beginPath();
 ctx.rect(halfwidth + (halfwidth * (1 - r.ratio)), 0, halfwidth * r.ratio, Method.height);
 ctx.rect(0, 0, halfwidth * r.ratio, Method.height);
 ctx.closePath();
 ctx.globalCompositeOperation = 'destination-out';
 ctx.fill();
 ctx.globalCompositeOperation = 'source-over';
 ctx.clip();
 ctx.drawImage(imageb, 0, 0, imageb.width, imageb.height, CenterB.x, CenterB.y, CenterB.width, CenterB.height);
 ctx.restore();
 }, function () {
 ctx.clearRect(0, 0, Method.width, Method.height);
 ctx.drawImage(imageb, 0, 0, imageb.width, imageb.height, CenterB.x, CenterB.y, CenterB.width, CenterB.height);
 finish();
 });
 };
 
 Method.Transitions.TopToBottom = function (imagea, imageb, s, fps, finish) {
 var CenterA = Method.Math.Center(imagea, Method, Method.Math.Scale(imagea, Method));
 var CenterB = Method.Math.Center(imageb, Method, Method.Math.Scale(imageb, Method));
 var ctx = Method.getContext('2d');
 ctx.drawImage(imagea, 0, 0, imagea.width, imagea.height, CenterA.x, CenterA.y, CenterA.width, CenterA.height);
 Method.Render(fps, 0, s * 1000, function (r) {
 ctx.save();
 ctx.beginPath();
 ctx.rect(0, 0, Method.width, Method.height * r.ratio);
 ctx.closePath();
 ctx.globalCompositeOperation = 'destination-out';
 ctx.fill();
 ctx.globalCompositeOperation = 'source-over';
 ctx.clip();
 ctx.drawImage(imageb, 0, 0, imageb.width, imageb.height, CenterB.x, CenterB.y, CenterB.width, CenterB.height);
 ctx.restore();
 }, function () {
 ctx.clearRect(0, 0, Method.width, Method.height);
 ctx.drawImage(imageb, 0, 0, imageb.width, imageb.height, CenterB.x, CenterB.y, CenterB.width, CenterB.height);
 finish();
 });
 };
 
 Method.Transitions.VerticalBlind = function (imagea, imageb, s, fps, finish) {
 var CenterA = Method.Math.Center(imagea, Method, Method.Math.Scale(imagea, Method));
 var CenterB = Method.Math.Center(imageb, Method, Method.Math.Scale(imageb, Method));
 var ctx = Method.getContext('2d');
 var bar = 12;
 var barwidth = Method.width / bar;
 ctx.drawImage(imagea, 0, 0, imagea.width, imagea.height, CenterA.x, CenterA.y, CenterA.width, CenterA.height);
 Method.Render(fps, 0, s * 1000, function (r) {
 ctx.save();
 ctx.beginPath();
 for (var i = 0; i < bar; i++) {
 ctx.rect(barwidth * i, 0, barwidth * r.ratio, Method.height);
 }
 ctx.closePath();
 ctx.globalCompositeOperation = 'destination-out';
 ctx.fill();
 ctx.globalCompositeOperation = 'source-over';
 ctx.clip();
 ctx.drawImage(imageb, 0, 0, imageb.width, imageb.height, CenterB.x, CenterB.y, CenterB.width, CenterB.height);
 ctx.restore();
 }, function () {
 ctx.clearRect(0, 0, Method.width, Method.height);
 ctx.drawImage(imageb, 0, 0, imageb.width, imageb.height, CenterB.x, CenterB.y, CenterB.width, CenterB.height);
 finish();
 });
 };
 Method.Transitions.ZoomIn = function (imagea, imageb, s, fps, finish) {
 var CenterB = Method.Math.Center(imageb, Method, Method.Math.Scale(imageb, Method));
 var ctx = Method.getContext('2d');
 var cx = Method.width / 2;
 var cy = Method.height / 2;
 var maxscale = Method.Math.Scale(imagea, Method);
 Method.Render(fps, 0, s * 1000, function (r) {
 var resize = maxscale + (maxscale * r.ratio);
 ctx.clearRect(0, 0, Method.width, Method.height);
 ctx.save();
 ctx.translate(cx, cy);
 ctx.scale(resize, resize);
 ctx.drawImage(imagea, -imagea.width / 2, -imagea.height / 2);
 ctx.restore();
 }, function () {
 ctx.clearRect(0, 0, Method.width, Method.height);
 ctx.drawImage(imageb, 0, 0, imageb.width, imageb.height, CenterB.x, CenterB.y, CenterB.width, CenterB.height);
 finish();
 });
 };
 Method.Transitions.ZoomOut = function (imagea, imageb, s, fps, finish) {
 var CenterB = Method.Math.Center(imageb, Method, Method.Math.Scale(imageb, Method));
 var ctx = Method.getContext('2d');
 var cx = Method.width / 2;
 var cy = Method.height / 2;
 var maxscale = Method.Math.Scale(imagea, Method);
 Method.Render(fps, 0, s * 1000, function (r) {
 var resize = maxscale + (maxscale * (1 - r.ratio));
 ctx.clearRect(0, 0, Method.width, Method.height);
 ctx.save();
 ctx.translate(cx, cy);
 ctx.scale(resize, resize);
 ctx.drawImage(imagea, -imagea.width / 2, -imagea.height / 2);
 ctx.restore();
 }, function () {
 ctx.clearRect(0, 0, Method.width, Method.height);
 ctx.drawImage(imageb, 0, 0, imageb.width, imageb.height, CenterB.x, CenterB.y, CenterB.width, CenterB.height);
 finish();
 });
 };
 
 return Method;
 }
 
 */