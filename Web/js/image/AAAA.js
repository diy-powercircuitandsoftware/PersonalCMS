class SlideShow2D {
    constructor(...args) {
       

        this.Running = false;
        this.Render = new SlideShow_RenderEngine();
        this.Render.ImageList = new SlideShow_ImageList();
        this.Render.ImageList.ref = this;
        this.Render.ImageList.index = 0;
        this.Render.hold_t = 0;
        this.Render.hold_maxt = 1 * 1000;
        this.Render.transition_t = 0;
        this.Render.transition_maxt = 1 * 1000;
        this.Render.transitionslist = [];
        this.Render.transition = null;
        this.Render.canvas = this.canvas;

        this.Render.ImageList.OnImageListChange = function (v) {
            this.ref.OnImageListChange(v);
        };
        this.Render.ImageList.OnSelectedImage = function (v) {
            this.ref.OnSelectedImage(v);
        };
    }
    
    OnImageListChange() {

    }
    OnSelectedImage() {

    }
    SetHoldTime(v) {
        if (Number.isInteger(v)) {
            this.Render.hold_maxt = v;
        }
    }
    SetTransitionTime(v) {
        if (Number.isInteger(v)) {
            this.Render.transition_maxt = v;
        }
    }

  
    Start() {

        if (!this.Running) {
            this.Running = true;
            var syntaxnoarguments = {
                "beginPath": "beginPath",
                "clip": "clip",
                "closePath": "closePath",
                "fill": "fill",
                "restore": "restore",
                "save": "save"
            };
            var syntaxsetter = {
                "globalAlpha": "globalAlpha",
                "globalCompositeOperation": "globalCompositeOperation"
            };
            var syntaxpoint = {
                "moveTo": "moveTo",
                "lineTo": "lineTo",
                "scale": "scale",
                "translate": "translate"
            };
            var syntaxrect = {
                "Rect": "rect",
                "fillRect": "fillRect",
                "clearRect": "clearRect"

            };


            this.Render.SetAnimate(function (v) {
                var ctx = this.canvas.getContext('2d');
                if (this.transition !== null) {
                    var command = [];
                    if (this.transition_t === 0) {
                        command = this.transition.Start();
                        this.transition_t = this.transition_t + v;
                    } else if (this.transition_t > (this.transition_maxt * 1.01)) {
                        command = this.transition.Finish();
                        this.transition = null;
                        this.transition_t = 0;
                        this.ImageList.index++;
                    } else {
                        command = this.transition.Running(this.transition_t / this.transition_maxt);
                        this.transition_t = this.transition_t + v;
                    }


                    for (var i in command) {
                        var cmd = command[i];

                        if (syntaxnoarguments.hasOwnProperty(cmd.command)) {
                            ctx[syntaxnoarguments[cmd.command]]();
                        } else if (syntaxsetter.hasOwnProperty(cmd.command)) {
                            ctx[syntaxsetter[cmd.command]] = cmd.value;
                        } else if (syntaxpoint.hasOwnProperty(cmd.command)) {
                            ctx[syntaxpoint[cmd.command]](cmd.x, cmd.y);
                        } else if (syntaxrect.hasOwnProperty(cmd.command)) {
                            ctx[syntaxrect[cmd.command]](cmd.x, cmd.y, cmd.width, cmd.height);
                        } else if (cmd.command === "Arc") {
                            ctx.arc(cmd.x, cmd.y, cmd.r, cmd.sa, cmd.ea, cmd.acw);
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

                        } else if (cmd.command === "Rotate") {
                            ctx.rotate(cmd.value);
                        } else if (cmd.command === "QuadraticCurveTo") {
                            ctx.quadraticCurveTo(cmd.cpx, cmd.cpy, cmd.x, cmd.y);
                        }

                    }

                } else {

                    if (this.hold_t === 0) {
                        this.rendered_imagea = false;
                    }
                    if (this.hold_t > (this.hold_maxt * 1.01)) {
                        var current = this.ImageList.index;
                        if (current < this.ImageList.Count() - 1) {
                            var rndnum = Math.floor(Math.random() * Math.floor(this.transitionslist.length));
                            var img1 = this.ImageList.GetImageSize(current);
                            var img2 = this.ImageList.GetImageSize(current + 1);
                            this.transition = new this.transitionslist[rndnum](this.canvas, img1, img2);
                        } else if (current === this.ImageList.Count() - 1) {
                            this.ImageList.index = 0;
                        }
                        this.hold_t = 0;
                    } else {
                        image = this.ImageList.GetImage(this.ImageList.index);
                        if (image !== null && !this.rendered_imagea) {
                            var ratio = Math.min(ctx.canvas.width / image.width, ctx.canvas.height / image.height);
                            var w = image.width * ratio;
                            var h = image.height * ratio;
                            var x = ctx.canvas.width / 2 - w / 2;
                            var y = ctx.canvas.height / 2 - h / 2;
                            ctx.clearRect(0, 0, ctx.canvas.width, ctx.canvas.height);
                            ctx.drawImage(image, 0, 0, image.width, image.height, x, y, w, h);
                            this.rendered_imagea = true;
                        }
                        this.hold_t = this.hold_t + v;
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
 

class SlideShow2D_Transition_fillEngine extends SlideShow2D_TransitionsEngine {

    Shape() {
        return {};
    }

    Start() {
        this.CenterA = this.Center(this.image1size, this.canvassize, this.scale(this.image1size, this.canvassize));
        this.CenterB = this.Center(this.image2size, this.canvassize, this.scale(this.image2size, this.canvassize));

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
            "command": "save"
        }, {
            "command": "beginPath"
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
            "command": "closePath"
        }, {
            "command": "globalCompositeOperation",
            "value": "destination-out"
        }, {
            "command": "fill"
        });

        stack.push({
            "command": "globalCompositeOperation",
            "value": "source-over"
        }, {
            "command": "clip"
        }, {
            "command": "DrawImage",
            "image": 2,
            "src": this.Rect(0, 0, this.image2size.width, this.image2size.height),
            "dest": this.CenterB
        }, {
            "command": "restore"
        });
        return stack;
    }

}




class SlideShow2D_Transition_BottomToTop extends SlideShow2D_Transition_fillEngine {
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
class SlideShow2D_Transition_CircleOut extends SlideShow2D_Transition_fillEngine {
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
class SlideShow2D_Transition_Corner extends SlideShow2D_Transition_fillEngine {
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

class SlideShow2D_Transition_PageTurn extends SlideShow2D_TransitionsEngine {
    Start() {
        this.CenterA = this.Center(this.image1size, this.canvassize, this.scale(this.image1size, this.canvassize));
        this.CenterB = this.Center(this.image2size, this.canvassize, this.scale(this.image2size, this.canvassize));
        this.P = (Math.sqrt(Math.pow(this.canvassize.width, 2) + Math.pow(this.canvassize.height, 2))) * 1.5;
        return[{
                "command": "clearRect",
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
            "command": "save"
        }, {
            "command": "fillRect",
            "x": 0,
            "y": 0,
            "width": this.canvassize.width,
            "height": this.canvassize.height

        }, {
            "command": "DrawImage",
            "image": 2,
            "src": this.Rect(0, 0, this.image2size.width, this.image2size.height),
            "dest": this.CenterB
        }, {
            "command": "beginPath"
        }, {
            "command": "moveTo",
            "x": y * y / 2 / x + x / 2,
            "y": 0
        }, {
            "command": "lineTo",
            "x": this.canvassize.width * 2,
            "y": 0
        }, {
            "command": "lineTo",
            "x": 0,
            "y": this.canvassize.height * 2
        }, {
            "command": "lineTo",
            "x": 0,
            "y": x * x / 2 / y + y / 2
        }, {
            "command": "closePath"
        }, {
            "command": "globalCompositeOperation",
            "value": "destination-out"
        }, {
            "command": "fill"
        }
        , {
            "command": "globalCompositeOperation",
            "value": "source-over"
        }, {
            "command": "clip"
        }, {
            "command": "DrawImage",
            "image": 1,
            "src": this.Rect(0, 0, this.image1size.width, this.image1size.height),
            "dest": this.CenterA
        }, {
            "command": "translate",
            "x": x,
            "y": y
        }, {
            "command": "Rotate",
            "value": Math.atan2(y, x) * 2
        }, {
            "command": "scale",
            "x": -1,
            "y": 1
        }, {
            "command": "DrawImage",
            "image": 1,
            "src": this.Rect(0, 0, this.image1size.width, this.image1size.height),
            "dest": this.CenterA
        }, {
            "command": "translate",
            "x": x,
            "y": y
        }, {
            "command": "restore"
        });
        return stack;
    }

}
;


class SlideShow2D_Transition_RightToLeft extends SlideShow2D_Transition_fillEngine {
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


class SlideShow2D_Transition_FromVerticalCenter extends SlideShow2D_Transition_fillEngine {
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
class SlideShow2D_Transition_StarOut extends SlideShow2D_Transition_fillEngine {
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
            "command": "translate",
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
                "command": "lineTo",
                "x": 0,
                "y": -(r * this.M)
            });
            stack.push({
                "command": "Rotate",
                "value": Math.PI / this.P
            });
            stack.push({
                "command": "lineTo",
                "x": 0,
                "y": -r
            });

        }
        stack.push({
            "command": "translate",
            "x": -this.canvassize.width / 2,
            "y": -this.canvassize.height / 2
        });
        return stack;
    }
}
;


class SlideShow2D_Transition_ToHorizontalCenter extends SlideShow2D_Transition_fillEngine {
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
class SlideShow2D_Transition_FromHorizontalCenter extends SlideShow2D_Transition_fillEngine {
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
class SlideShow2D_Transition_TopToBottom extends SlideShow2D_Transition_fillEngine {

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
class SlideShow2D_Transition_LeftToRight extends SlideShow2D_Transition_fillEngine {

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
class SlideShow2D_Transition_ToVerticalCenter extends SlideShow2D_Transition_fillEngine {
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
class SlideShow2D_Transition_VerticalBlind extends SlideShow2D_Transition_fillEngine {
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
class SlideShow2D_Transition_HorizontalBlind extends SlideShow2D_Transition_fillEngine {
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
class SlideShow2D_Transition_HeartOut extends SlideShow2D_Transition_fillEngine {
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
            "command": "moveTo",
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
            "command": "lineTo",
            "x": kd2,
            "y": kd
        }, {
            "command": "lineTo",
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

class SlideShow2D_Transition_RectWipe extends SlideShow2D_Transition_fillEngine {
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
        while (progress < time) {// time calibration 

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

class SlideShow2D_Transition_Mosaic extends SlideShow2D_Transition_fillEngine {
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

        this.Index = 0;
        this.MaxIndex = this.Mosaic.length;

    }
    Shape(time) {

        var progress = this.Index / this.MaxIndex;
        var out = [];
        while (progress < time) {// time calibration 
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


class SlideShow2D_Transition_SpinRight extends SlideShow2D_TransitionsEngine {
    Start() {
        this.CenterA = this.Center(this.image1size, this.canvassize, this.scale(this.image1size, this.canvassize));
        this.CenterB = this.Center(this.image2size, this.canvassize, this.scale(this.image2size, this.canvassize));
        this.CX = this.canvassize.width / 2;
        this.CY = this.canvassize.height / 2;
    }

    Running(time) {
        var stack = [];
        stack.push({
            "command": "clearRect",
            "x": 0,
            "y": 0,
            "width": this.canvassize.width,
            "height": this.canvassize.height

        });
        if (time < 0.4) {
            stack.push({
                "command": "DrawImage",
                "image": 1,
                "src": this.Rect(0, 0, this.image1size.width, this.image1size.height),
                "dest": this.CenterA
            });
        } else if (time > 0.4 && time < 0.6) {

            stack.push({
                "command": "save",

            }, {
                "command": "translate",
                "x": this.CX,
                "y": this.CY
            }, {
                "command": "Rotate",
                "value": (12 * 360 * time) * Math.PI / 180

            }, {
                "command": "translate",
                "x": -this.CX,
                "y": -this.CY
            }, {
                "command": "DrawImage",
                "image": 2,
                "src": this.Rect(0, 0, this.image2size.width, this.image2size.height),
                "dest": this.CenterB
            }, {
                "command": "restore"

            });
        } else if (time > 0.6) {

            stack.push({
                "command": "DrawImage",
                "image": 2,
                "src": this.Rect(0, 0, this.image2size.width, this.image2size.height),
                "dest": this.CenterB
            });
        }

        return stack;
    }

}
;



class SlideShow2D_Transition_ZoomInOut extends SlideShow2D_TransitionsEngine {
    Start() {
        this.CenterA = this.Center(this.image1size, this.canvassize, this.scale(this.image1size, this.canvassize));
        this.CenterB = this.Center(this.image2size, this.canvassize, this.scale(this.image2size, this.canvassize));
        this.CX = this.canvassize.width / 2;
        this.CY = this.canvassize.height / 2;
    }

    Running(time) {
        var stack = [];
        var progress = 0;
        var inout = "";
        stack.push({
            "command": "save"
        }, {
            "command": "clearRect",
            "x": 0,
            "y": 0,
            "width": this.canvassize.width,
            "height": this.canvassize.height

        });


        if (time < 0.2) {
            progress = (time - 0) / 0.2;
            inout = "i";
        } else if (time > 0.2 && time < 0.4) {
            progress = (time - 0.2) / 0.2;
            inout = "o";
        } else if (time > 0.4 && time < 0.6) {
            inout = "";
        } else if (time > 0.6 && time < 0.8) {
            progress = (time - 0.6) / 0.2;
            inout = "i";
        } else if (time > 0.8) {
            progress = (time - 0.8) / 0.2;
            inout = "o";
        }
        stack.push({
            "command": "translate",
            "x": this.CX,
            "y": this.CY
        });

        var size = 3 * progress;
        var invsize = 3 * (1 - progress);
        if (inout === "i" && size > 1) {
            stack.push({
                "command": "scale",
                "x": size,
                "y": size
            });
        } else if (inout === "o" && invsize > 1) {

            stack.push({
                "command": "scale",
                "x": invsize,
                "y": invsize
            });
        }
        stack.push({
            "command": "translate",
            "x": -this.CX,
            "y": -this.CY
        });
        if (time < 0.5) {
            stack.push({
                "command": "DrawImage",
                "image": 1,
                "src": this.Rect(0, 0, this.image1size.width, this.image1size.height),
                "dest": this.CenterA
            });
        } else if (time > 0.5) {
            stack.push({
                "command": "DrawImage",
                "image": 2,
                "src": this.Rect(0, 0, this.image2size.width, this.image2size.height),
                "dest": this.CenterB
            });
        }


        stack.push({
            "command": "restore"
        });


        return stack;
    }

}
;
