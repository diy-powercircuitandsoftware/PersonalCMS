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
            var syntaxnoarguments = {
                "BeginPath": "beginPath",
                "Clip": "clip",
                "ClosePath": "closePath",
                "Fill": "fill",
                "Restore": "restore",
                "Save": "save"
            };
            var syntaxsetter = {
                "GlobalAlpha": "globalAlpha",
                "GlobalCompositeOperation": "globalCompositeOperation"
            };
            var syntaxpoint = {
                "MoveTo": "moveTo",
                "LineTo": "lineTo",
                "Scale": "scale",
                "Translate": "translate"
            };


            this.Render.SetAnimate(function (v) {

                if (this.transition !== null) {
                    var command = [];
                    if (this.t === 0) {
                        command = this.transition.Start();
                        this.t = this.t + v;
                    } else if (this.t > (this.maxt * 1.01)) {
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

                        if (syntaxnoarguments.hasOwnProperty(cmd.command)) {
                            ctx[syntaxnoarguments[cmd.command]]();
                        } else if (syntaxsetter.hasOwnProperty(cmd.command)) {
                            ctx[syntaxsetter[cmd.command]] = cmd.value;
                        } else if (syntaxpoint.hasOwnProperty(cmd.command)) {
                            ctx[syntaxpoint[cmd.command]](cmd.x, cmd.y);
                        } else if (cmd.command === "Arc") {
                            ctx.arc(cmd.x, cmd.y, cmd.r, cmd.sa, cmd.ea, cmd.acw);
                        } else if (cmd.command === "ClearRect") {
                            ctx.clearRect(cmd.x, cmd.y, cmd.width, cmd.height);
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

                        } else if (cmd.command === "Rect") {
                            if (cmd.fill) {
                                ctx.fillRect(cmd.x, cmd.y, cmd.width, cmd.height);
                            } else {
                                ctx.rect(cmd.x, cmd.y, cmd.width, cmd.height);
                            }
                        } else if (cmd.command === "Rotate") {
                            ctx.rotate(cmd.value);
                        } else if (cmd.command === "QuadraticCurveTo") {
                            ctx.quadraticCurveTo(cmd.cpx, cmd.cpy, cmd.x, cmd.y);
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
class SlideShow_Transition_FillEngine extends SlideShow_TransitionsEngine {

    Shape() {
        return {};
    }

    Start() {
        this.CenterA = this.Center(this.image1size, this.canvassize, this.Scale(this.image1size, this.canvassize));
        this.CenterB = this.Center(this.image2size, this.canvassize, this.Scale(this.image2size, this.canvassize));

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
        });
        var sh = this.Shape(time);
        if (sh instanceof Array) {
            for (var i in sh) {
                stack.push(sh[i]);
            }
        } else if (sh instanceof Object) {
            stack.push(sh);

        }
        stack.push({
            "command": "ClosePath"
        }, {
            "command": "GlobalCompositeOperation",
            "value": "destination-out"
        }, {
            "command": "Fill"
        });

        stack.push({
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

}
;
class SlideShow_Transition_FadeOutFadeIn extends SlideShow_TransitionsEngine {
    Start() {
        this.CenterA = this.Center(this.image1size, this.canvassize, this.Scale(this.image1size, this.canvassize));
        this.CenterB = this.Center(this.image2size, this.canvassize, this.Scale(this.image2size, this.canvassize));
        return[{
                "command": "ClearRect",
                "x": 0,
                "y": 0,
                "width": this.canvassize.width,
                "height": this.canvassize.height,
            }, {
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
            "height": this.canvassize.height
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

class SlideShow_Transition_BottomToTop extends SlideShow_Transition_FillEngine {
    Shape(time) {
        return {
            "command": "Rect",
            "x": 0,
            "y": this.canvassize.height * (1 - time),
            "width": this.canvassize.width,
            "height": this.canvassize.height * time
        };
    }
    ;
}
;
class SlideShow_Transition_CircleOut extends SlideShow_Transition_FillEngine {
    Start() {
        super.Start();
        this.MinCanvasSize = Math.min(this.canvassize.width, this.canvassize.height);
    }
    Shape(time) {
        return {
            "command": "Arc",
            "x": this.canvassize.width / 2,
            "y": this.canvassize.height / 2,
            "r": this.MinCanvasSize * time,
            "sa": 0,
            "ea": 2 * Math.PI,
            "acw": false
        };
    }
    ;
}
;
class SlideShow_Transition_Corner extends SlideShow_Transition_FillEngine {
    Shape(time) {
        return {
            "command": "Rect",
            "x": 0,
            "y": 0,
            "width": Math.round(this.canvassize.width * time),
            "height": Math.round(this.canvassize.height * time)
        };
    }
    ;
}
;

class SlideShow_Transition_PageTurn extends SlideShow_TransitionsEngine {
    Start() {
        this.CenterA = this.Center(this.image1size, this.canvassize, this.Scale(this.image1size, this.canvassize));
        this.CenterB = this.Center(this.image2size, this.canvassize, this.Scale(this.image2size, this.canvassize));
        this.P = (Math.sqrt(Math.pow(this.canvassize.width, 2) + Math.pow(this.canvassize.height, 2))) * 1.5;
        return[{
                "command": "ClearRect",
                "x": 0,
                "y": 0,
                "width": this.canvassize.width,
                "height": this.canvassize.height
            }, {
                "command": "DrawImage",
                "image": 1,
                "src": this.Rect(0, 0, this.image1size.width, this.image1size.height),
                "dest": this.CenterA
            }];
    }
    Running(time) {
        var stack = [];
        var x = (this.P * time) + 0.1;
        var y = (this.P * time) + 0.1;

        stack.push({
            "command": "Save"
        }, {
            "command": "Rect",
            "x": 0,
            "y": 0,
            "width": this.canvassize.width,
            "height": this.canvassize.height,
            "fill": true
        }, {
            "command": "DrawImage",
            "image": 2,
            "src": this.Rect(0, 0, this.image2size.width, this.image2size.height),
            "dest": this.CenterB
        }, {
            "command": "BeginPath"
        }, {
            "command": "MoveTo",
            "x": y * y / 2 / x + x / 2,
            "y": 0
        }, {
            "command": "LineTo",
            "x": this.canvassize.width * 2,
            "y": 0
        }, {
            "command": "LineTo",
            "x": 0,
            "y": this.canvassize.height * 2
        }, {
            "command": "LineTo",
            "x": 0,
            "y": x * x / 2 / y + y / 2
        }, {
            "command": "ClosePath"
        }, {
            "command": "GlobalCompositeOperation",
            "value": "destination-out"
        }, {
            "command": "Fill"
        }
        , {
            "command": "GlobalCompositeOperation",
            "value": "source-over"
        }, {
            "command": "Clip"
        }, {
            "command": "DrawImage",
            "image": 1,
            "src": this.Rect(0, 0, this.image1size.width, this.image1size.height),
            "dest": this.CenterA
        }, {
            "command": "Translate",
            "x": x,
            "y": y
        }, {
            "command": "Rotate",
            "value": Math.atan2(y, x) * 2
        }, {
            "command": "Scale",
            "x": -1,
            "y": 1
        }, {
            "command": "DrawImage",
            "image": 1,
            "src": this.Rect(0, 0, this.image1size.width, this.image1size.height),
            "dest": this.CenterA
        }, {
            "command": "Translate",
            "x": x,
            "y": y
        }, {
            "command": "Restore"
        });
        return stack;
    }

}
;


class SlideShow_Transition_RightToLeft extends SlideShow_Transition_FillEngine {
    Shape(time) {
        return {

            "command": "Rect",
            "x": Math.round(this.canvassize.width * (1 - time)),
            "y": 0,
            "width": this.canvassize.width * time,
            "height": this.canvassize.height
        };
    }
    ;
}
;


class SlideShow_Transition_FromVerticalCenter extends SlideShow_Transition_FillEngine {
    Start() {
        super.Start();
        this.halfwidth = this.canvassize.width / 2;
    }
    Shape(time) {
        return [{

                "command": "Rect",
                "x": this.halfwidth,
                "y": 0,
                "width": this.halfwidth * time,
                "height": this.canvassize.height
            }, {

                "command": "Rect",
                "x": this.halfwidth * (1 - time),
                "y": 0,
                "width": this.halfwidth * time,
                "height": this.canvassize.height
            }];
    }
    ;
}
;
class SlideShow_Transition_StarOut extends SlideShow_Transition_FillEngine {
    Start() {
        this.P = 5;
        this.M = 0.5;
        this.MaxCanvasSize = Math.max(this.canvassize.width, this.canvassize.height);
        super.Start();
    }
    Shape(time) {
        var stack = [];
        var r = this.MaxCanvasSize * time;
        stack.push({
            "command": "Translate",
            "x": this.canvassize.width / 2,
            "y": this.canvassize.height / 2
        });
        for (var i = 0; i < this.P; i++)
        {

            stack.push({
                "command": "Rotate",
                "value": Math.PI / this.P
            });
            stack.push({
                "command": "LineTo",
                "x": 0,
                "y": -(r * this.M)
            });
            stack.push({
                "command": "Rotate",
                "value": Math.PI / this.P
            });
            stack.push({
                "command": "LineTo",
                "x": 0,
                "y": -r
            });

        }
        stack.push({
            "command": "Translate",
            "x": -this.canvassize.width / 2,
            "y": -this.canvassize.height / 2
        });
        return stack;
    }
}
;


class SlideShow_Transition_ToHorizontalCenter extends SlideShow_Transition_FillEngine {
    Start() {
        super.Start();
        this.halfheight = this.canvassize.height / 2;
    }
    Shape(time) {
        return [{

                "command": "Rect",
                "x": 0,
                "y": 0,
                "width": this.canvassize.width,
                "height": this.halfheight * time
            }, {

                "command": "Rect",
                "x": 0,
                "y": this.halfheight + (this.halfheight * (1 - time)),
                "width": this.canvassize.width,
                "height": this.halfheight * time
            }];
    }
}
;
class SlideShow_Transition_FromHorizontalCenter extends SlideShow_Transition_FillEngine {
    Start() {
        super.Start();
        this.halfheight = this.canvassize.height / 2;
    }

    Shape(time) {

        return [{

                "command": "Rect",
                "x": 0,
                "y": this.halfheight,
                "width": this.canvassize.width,
                "height": this.halfheight * time
            }, {

                "command": "Rect",
                "x": 0,
                "y": this.halfheight * (1 - time),
                "width": this.canvassize.width,
                "height": this.halfheight * time
            }];
    }
}
;
class SlideShow_Transition_TopToBottom extends SlideShow_Transition_FillEngine {

    Shape(time) {

        return [{
                "command": "Rect",
                "x": 0,
                "y": 0,
                "width": this.canvassize.width,
                "height": this.canvassize.height * time
            }];
    }
}
;
class SlideShow_Transition_LeftToRight extends SlideShow_Transition_FillEngine {

    Shape(time) {

        return [{
                "command": "Rect",
                "x": 0,
                "y": 0,
                "width": this.canvassize.width * time,
                "height": this.canvassize.height
            }];
    }
}
;
class SlideShow_Transition_ToVerticalCenter extends SlideShow_Transition_FillEngine {
    Start() {
        super.Start();
        this.halfwidth = this.canvassize.width / 2;
    }

    Shape(time) {

        return [{
                "command": "Rect",
                "x": this.halfwidth + (this.halfwidth * (1 - time)),
                "y": 0,
                "width": this.halfwidth * time,
                "height": this.canvassize.height
            }, {
                "command": "Rect",
                "x": 0,
                "y": 0,
                "width": this.halfwidth * time,
                "height": this.canvassize.height
            }];
    }
}
;
class SlideShow_Transition_VerticalBlind extends SlideShow_Transition_FillEngine {
    Start() {
        super.Start();
        this.bar = 12;
        this.barwidth = this.canvassize.width / this.bar;
    }
    Shape(time) {
        var stack = [];
        for (var i = 0; i < this.bar; i++) {
            stack.push({
                "command": "Rect",
                "x": this.barwidth * i,
                "y": 0,
                "width": this.barwidth * time,
                "height": this.canvassize.height
            });
        }
        return stack;

    }
}
;
class SlideShow_Transition_HorizontalBlind extends SlideShow_Transition_FillEngine {
    Start() {
        super.Start();
        this.bar = 12;
        this.barheight = this.canvassize.height / this.bar;
    }
    Shape(time) {
        var stack = [];
        for (var i = 0; i < this.bar; i++) {
            stack.push({
                "command": "Rect",
                "x": 0,
                "y": this.barheight * i,
                "width": this.canvassize.width,
                "height": this.barheight * time
            });
        }
        return stack;

    }
}
;
class SlideShow_Transition_HeartOut extends SlideShow_Transition_FillEngine {
    Start() {
        this.DoubleMaxCanvasSize = Math.max(this.canvassize.width, this.canvassize.height) * 2;
        super.Start();
    }
    Shape(time) {
        var stack = [];
        var d = this.DoubleMaxCanvasSize * time; //The Size of the hearting
        var k = (this.canvassize.width / 2) - (d / 2); // The Position of the heart
        var kd4 = k + d / 4;
        var kd2 = k + d / 2;
        var kd34 = k + d * 3 / 4;
        var kd = k + d;
        stack.push({
            "command": "MoveTo",
            "x": k,
            "y": kd4
        }, {
            "command": "QuadraticCurveTo",
            "cpx": k,
            "cpy": k,
            "x": kd4,
            "y": k
        }, {
            "command": "QuadraticCurveTo",
            "cpx": kd2,
            "cpy": k,
            "x": kd2,
            "y": kd4
        }, {
            "command": "QuadraticCurveTo",
            "cpx": kd2,
            "cpy": k,
            "x": kd34,
            "y": k
        }, {
            "command": "QuadraticCurveTo",
            "cpx": kd,
            "cpy": k,
            "x": kd,
            "y": kd4
        }, {
            "command": "QuadraticCurveTo",
            "cpx": kd,
            "cpy": kd2,
            "x": kd34,
            "y": kd34
        }, {
            "command": "LineTo",
            "x": kd2,
            "y": kd
        }, {
            "command": "LineTo",
            "x": kd4,
            "y": kd34
        }, {
            "command": "QuadraticCurveTo",
            "cpx": k,
            "cpy": kd2,
            "x": k,
            "y": kd4
        });


        return stack;


    }
}
;

class SlideShow_Transition_RectWipe extends SlideShow_Transition_FillEngine {
    Start() {
        super.Start();
        this.CountOFTiles = 12;
        this.Tiles = Math.max(this.canvassize.width, this.canvassize.height) / this.CountOFTiles;
        this.Starty = 0;
        this.Startx = 0;

        this.Index = 0;
        this.MaxIndex = this.CountOFTiles * this.CountOFTiles;
    }
    Shape(time) {

        var progress = this.Index / this.MaxIndex;
        var out = [];
        while (progress < time) {

            if (this.Startx > this.canvassize.width) {
                this.Starty = this.Starty + this.Tiles;
                this.Startx = 0;
            } else {
                this.Startx = this.Startx + this.Tiles;
            }
            out.push({
                "command": "Rect",
                "x": this.Startx,
                "y": this.Starty,
                "width": this.Tiles,
                "height": this.Tiles
            });
            this.Index++;
            progress = this.Index / this.MaxIndex;
        }

        return out;

    }
}
;

class SlideShow_Transition_Mosaic extends SlideShow_Transition_FillEngine {
    Start() {
        super.Start();
        this.CountOFTiles = 12;
        this.Tiles = Math.max(this.canvassize.width, this.canvassize.height) / this.CountOFTiles;
        this.Mosaic = [];
        for (var i = 0; i <= this.CountOFTiles; i++) {
            for (var j = 0; j <= this.CountOFTiles; j++) {
                this.Mosaic.push({
                    "x": j * this.Tiles,
                    "y": i * this.Tiles
                });
            }
        }

        this.Starty = 0;
        this.Startx = 0;

        this.Index = 0;
        this.MaxIndex = this.Mosaic.length;

    }
    Shape(time) {

        var progress = this.Index / this.MaxIndex;
        var out = [];
        while (progress < time) {
            var mosaic = this.Mosaic.splice(this.Mosaic.length * Math.random() | 0, 1)[0];
            if (mosaic !== undefined) {
                out.push({
                    "command": "Rect",
                    "x": mosaic.x,
                    "y": mosaic.y,
                    "width": this.Tiles,
                    "height": this.Tiles
                });
            }
            this.Index++;
            progress = this.Index / this.MaxIndex;
        }

        return out;

    }
}
;


//
/*    
 
 
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