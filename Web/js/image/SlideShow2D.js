class SlideShow2D {
    constructor(dom) {
        this.canvas = null;
        if (dom instanceof HTMLElement && dom.tagName.toUpperCase() == "CANVAS") {
            this.canvas = dom;
        } else if (dom instanceof HTMLElement) {
            this.canvas = dom.appendChild(document.createElement("CANVAS"));
        }
        this.canvas.style.border = "thin solid";
        this.Config = {
            "AnimateTime": 1000,
            "HoldTime": 1000,
            "Index": 1
        };
        this.ImageList = new SlideShow2D_ImageList();
        this.FPSTimer = new SlideShow2D_FPSTimer();
        this.Render = new SlideShow2D_RenderEngine(this.canvas);
        this.TransitionList = [];
        this.FPSTimer.Reference = this;
        this.FPSTimer.Tick = function (t) {
            this.Reference.Render.Rendering(t);
        };
        this.Render.Reference = this;
        this.Render.TransitionEnd = function () {
            var prev= this.Reference.Config.Index;
            this.Reference.Config.Index = (this.Reference.Config.Index + 1) % this.Reference.ImageList.Count();
             var next= this.Reference.Config.Index;
            this.Reference.Render.SetImageB(this.Reference.ImageList.GetImage(this.Reference.Config.Index));
           
            this.SetTransition(new this.Reference.TransitionList[0](
                this.Size(), this.Reference.ImageList.GetImageSize(prev), this.Reference.ImageList.GetImageSize(next)
            ));
        };
    }
    AddImage(path) {
        this.ImageList.AddImage(path);
    }
    AddTransition(te) {
        if (new te() instanceof SlideShow2D_Transition) {
            this.TransitionList.push(te);
        }
    }
    Clear() {
        return this.Render.ImageList.Clear();
    }
    GetImageCount() {
        return this.Render.ImageList.Count();
    }
    Load(callback) {
        this.ImageList.Load = callback;
    }
    Size(w, h) {
        this.Render.Size(w, h);
    }
    Start() {
        this.Render.animate_finishtime = this.Config.AnimateTime;
        this.Render.hold_finishtime = this.Config.HoldTime;
        this.Render.SetImageA(this.ImageList.GetImage(0));
        this.Render.SetImageB(this.ImageList.GetImage(1));
        this.Render.SetTransition(new this.TransitionList[0](
                this.Render.Size(), this.ImageList.GetImageSize(0), this.ImageList.GetImageSize(1)
                ));//TEST
        this.FPSTimer.Start();
    }
}

class SlideShow2D_FPSTimer {
    constructor(fps = 60) {
        this.requestID = 0;
        this.fps = fps;
        this.animate = function () {};
    }
    Tick(time) {

    }

    Start() {
        cancelAnimationFrame(this.requestID);
        let then = performance.now();
        const interval = 1000 / this.fps;
        const tolerance = 0.1;
        const animateLoop = (now) => {
            const delta = now - then;
            if (delta >= interval - tolerance) {
                then = now - (delta % interval);
                this.Tick(delta);
            }
            this.requestID = requestAnimationFrame(animateLoop);
        };
        this.requestID = requestAnimationFrame(animateLoop);
    }
    Stop() {
        cancelAnimationFrame(this.requestID);
    }
}

class SlideShow2D_ImageList {
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
                    ref.Load(ref.ImageList.length);
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
        return this.ImageList.length;
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
    Load() {

    }
}

class SlideShow2D_RenderEngine {
    constructor(canvas) {
        this.canvas = canvas;
        this.animate_accumulatetime = 0;
        this.animate_finishtime = 0;
        this.hold_accumulatetime = 0;
        this.hold_finishtime = 0;
        this.effects = null;
        this.transition = null;
        this.hold = true;
        this.image_a = null;
        this.image_b = null;

    }

    DrawCenter(image) {
        var ctx = this.canvas.getContext('2d');
        var ratio = Math.min(ctx.canvas.width / image.width, ctx.canvas.height / image.height);
        var w = image.width * ratio;
        var h = image.height * ratio;
        var x = ctx.canvas.width / 2 - w / 2;
        var y = ctx.canvas.height / 2 - h / 2;
        ctx.clearRect(0, 0, ctx.canvas.width, ctx.canvas.height);
        ctx.drawImage(image, 0, 0, image.width, image.height,
                x, y, w, h);
    }
    Rendering(tick) {
        var ctx = this.canvas.getContext('2d');
        if (this.hold) {
            if (this.hold_accumulatetime == 0) {
                this.DrawCenter(this.image_a);
            }

            //draw effect
            this.hold_accumulatetime = this.hold_accumulatetime + tick;
            if (this.hold_accumulatetime > this.hold_finishtime) {

                this.hold = !this.hold;
                this.hold_accumulatetime = 0;
            }
        } else if (!this.hold) {
            if (this.transition != null) {
                var command = this.transition.Running(this.animate_accumulatetime / this.animate_finishtime);
                for (var i = 0; i < command.length; i++) {
                    var funcname=command[i].command;
                    if (command[i].extends) {
                        if (funcname=="DrawCenter"){
                            if (command[i].address==1){
                                this.DrawCenter(this.image_a);
                            } else  if (command[i].address==2){
                                this.DrawCenter(this.image_b);
                            }
                        }
                    } else if (command[i].args !== undefined) {
                        var args=command[i].args ;
                        ctx[funcname](...args) ;
                    } else if (command[i].value !== undefined) {
                        ctx[funcname] = command[i].value;
                        
                    }
                }
            }
            this.animate_accumulatetime = this.animate_accumulatetime + tick;
            if (this.animate_accumulatetime > this.animate_finishtime) {
                if (this.image_b != null) {
                    this.image_a = this.image_b;                   
                }
                this.hold = !this.hold;
                this.animate_accumulatetime = 0;
                this.TransitionEnd();
            }
        }

    }
    SetImageA(img) {
        this.image_a = img;
    }
    SetImageB(img) {
        this.image_b = img;
    }
    SetTransition(transition) {
        this.transition = transition;
    }
    Size(...args) {
        if (args.length == 0) {
            return {
                "width": this.canvas.width,
                "height": this.canvas.height
            };
        } else if (args.length == 2) {
            this.canvas.width = args[0];
            this.canvas.height = args[1];
        }
    }
    TransitionEnd() {

    }
}

class SlideShow2D_Transition {

    constructor(canvassize, image1size, image2size) {
        this.canvassize = canvassize;
        this.image1size = image1size;
        this.image2size = image2size;
    }

    Running(time) {
        var stack = [];
        return stack;
    }

    /* Center(rect, ref, ratio = 1) {
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
     scale(src, dest) {
     return    Math.min(dest.width / src.width, dest.height / src.height);
     }*/
}
/*class SlideShow2D_Fill_Transition extends SlideShow2D_Transition {
 
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
 }*/

//Transition//
class SlideShow2D_Transition_FadeOutFadeIn extends SlideShow2D_Transition {

    Running(time) {
        var stack = [];
        if (time == 0) {
            return[{
                    "command": "clearRect",
                    "args": [0, 0, this.canvassize.width, this.canvassize.height]
                }, {
                    "command": "globalAlpha",
                    "value": 1
                }, {
                    "command": "DrawCenter",
                    "address": 1,
                    "extends": true
                }];
        }

        if (time < 0.5) {
            stack.push({
                "command": "clearRect",
                "args": [0, 0, this.canvassize.width, this.canvassize.height]
            });
            stack.push({
                "command": "globalAlpha",
                "value": 1 - (2 * time)
            }, {
                "command": "DrawCenter",
                "address": 1,
                "extends": true
            });

        } else if (time > 0.5  ) {
            stack.push({
                "command": "clearRect",
                "args": [0, 0, this.canvassize.width, this.canvassize.height]
            });
            stack.push({
                "command": "globalAlpha",
                "value": (2 * time ) - 1
            }, {
                "command": "DrawCenter",
                "address": 2,
                "extends": true
            });
        } 
        if (time >= 0.9) {
            return[{
                    "command": "globalAlpha",
                    "value": 1
                }];
        }
        return stack;
    }

}
;