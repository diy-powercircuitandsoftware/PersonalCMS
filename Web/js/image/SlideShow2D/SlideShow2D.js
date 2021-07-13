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
        this.Render = null;
        this.TransitionList = [];
        this.FPSTimer.Reference = this;
        this.FPSTimer.Tick = function (t) {
            this.Reference.Render.Rendering(t);
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
    AnimateTime(...args) {
        if (args.length == 0) {
            return this.Config.AnimateTime;
        }
        this.Config.AnimateTime = args[0];
        this.Render.animate_finishtime = this.Config.AnimateTime;
    }
    Clear() {
        this.Stop();
        this.Load(0);
        this.ImageIndexChange(0);
        this.ImageList.Clear();
    }
    GetImageCount() {
        return this.ImageList.Count();
    }
    HoldTime(...args) {
        if (args.length == 0) {
            return this.Config.HoldTime;
        }
        this.Config.HoldTime = args[0];
        this.Render.hold_finishtime = this.Config.HoldTime;
    }
    ImageIndexChange(v) {

    }
    Load(callback) {
        if (typeof callback === 'function') {
            this.ImageList.Load = callback;
        } else if (Number.isInteger(callback)) {
            this.ImageList.Load(callback);
        }
    }
    Size(w, h) {
        this.canvas.width = w;
        this.canvas.height = h;
    }
    Start() {
        this.Render = new SlideShow2D_RenderEngine(this.canvas);
        this.Render.Reference = this;
        this.Render.TransitionEnd = function () {
            var rndnum = Math.floor(Math.random() * Math.floor(this.Reference.TransitionList.length));
            var prev = this.Reference.Config.Index;
            this.Reference.Config.Index = (this.Reference.Config.Index + 1) % this.Reference.ImageList.Count();
            var next = this.Reference.Config.Index;
            this.Reference.Render.SetImageB(this.Reference.ImageList.GetImage(this.Reference.Config.Index));
            var rendersize = this.Size();
            var imageasize = this.Reference.ImageList.GetImageSize(prev);
            var imagebsize = this.Reference.ImageList.GetImageSize(next);
            var transition = new this.Reference.TransitionList[rndnum]( );
            transition.CanvasSize(rendersize.width, rendersize.height);
            transition.ImageASize(imageasize.width, imageasize.height);
            transition.ImageBSize(imagebsize.width, imagebsize.height);
            this.SetTransition(transition);
            this.Reference.ImageIndexChange(this.Reference.Config.Index);
        };
        if (this.ImageList.Count() == 0) {
            return false;
        } else if (this.ImageList.Count() % 2 != 0) {
            this.ImageList.AddImage(this.ImageList.GetImage(0));
        }
        this.Render.animate_finishtime = this.Config.AnimateTime;
        this.Render.hold_finishtime = this.Config.HoldTime;
        this.Render.SetImageA(this.ImageList.GetImage(0));
        this.Render.SetImageB(this.ImageList.GetImage(1));
        var rendersize = this.Render.Size();
        var imageasize = this.ImageList.GetImageSize(0);
        var imagebsize = this.ImageList.GetImageSize(1);
        if (this.TransitionList.length > 0) {
            var transition = new this.TransitionList[0]( );
            transition.CanvasSize(rendersize.width, rendersize.height);
            transition.ImageASize(imageasize.width, imageasize.height);
            transition.ImageBSize(imagebsize.width, imagebsize.height);
            this.Render.SetTransition(transition);
        }
        this.FPSTimer.Start();
    }
    Stop() {
        this.FPSTimer.Stop();
        if (this.Render !== null) {
            this.Render.Clear();
        }
    }
    ToggleFPSPlayer() {
        if (this.Render === null) {
            this.Start();
            return !this.FPSTimer.pause;
        }
        this.FPSTimer.pause = !this.FPSTimer.pause;
        return  !this.FPSTimer.pause;
    }
}

class SlideShow2D_FPSTimer {
    constructor(fps = 60) {
        this.requestID = 0;
        this.fps = fps;
        this.animate = function () {};
        this.pause = true;
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
                if (!this.pause) {
                    this.Tick(delta);
                }
            }
            this.requestID = requestAnimationFrame(animateLoop);
        };
        this.pause = false;
        this.requestID = requestAnimationFrame(animateLoop);
    }
    Stop() {
        this.pause = true;
        cancelAnimationFrame(this.requestID);
    }
}

class SlideShow2D_ImageList {
    constructor() {
        this.ImageList = [];
    }

    AddImage(path) {
        if (typeof path === 'string' || path instanceof String) {
            var img = new Image();
            img.ref = this;
            img.src = path;
            img.onload = function () {
                this.ref.ImageList.push(this);
                this.ref.Load(this.ref.ImageList.length);
            };
        } else if (path instanceof HTMLImageElement) {
            this.ImageList.push(path);
            this.Load(this.ImageList.length);
        }


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

    Clear() {
        var ctx = this.canvas.getContext('2d');
        ctx.clearRect(0, 0, ctx.canvas.width, ctx.canvas.height);
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
                var progress = this.animate_accumulatetime / this.animate_finishtime;
                var command = this.transition.Update(progress, tick);
                for (var i = 0; i < command.length; i++) {
                    var funcname = command[i].command;
                    if (command[i].extends) {
                        if (funcname == "DrawCenter") {
                            if (command[i].address == 1) {
                                this.DrawCenter(this.image_a);
                            } else if (command[i].address == 2) {
                                this.DrawCenter(this.image_b);
                            }
                        } else if (funcname == "Polygons") {
                            var end = 360 * Math.PI / 180;
                            var x = command[i].args[0];
                            var y = command[i].args[1];
                            var size = command[i].args[2];
                            var dot = command[i].args[3];
                            var step_size = end / dot;
                            var angle = 0;
                            var first = true;
                            while (angle <= end) {
                                let px = (Math.sin(angle) * size) + x,
                                        py = (-Math.cos(angle) * size) + y;
                                if (first) {
                                    ctx.moveTo(px, py);
                                    first = false;
                                } else {
                                    ctx.lineTo(px, py);
                                }
                                angle = angle + step_size;
                            }


                        }


                    } else if (command[i].args !== undefined) {
                        var args = command[i].args;
                        ctx[funcname](...args);
                    } else if (command[i].value !== undefined) {
                        ctx[funcname] = command[i].value;
                    } else if (typeof ctx[funcname] === 'function') {
                        ctx[funcname]();
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

    CanvasSize(w, h) {
        this.canvassize = {
            "width": w,
            "height": h
        };
    }
    ImageASize(w, h) {
        this.imageasize = {
            "width": w,
            "height": h
        };
    }
    ImageBSize(w, h) {
        this.imagebsize = {
            "width": w,
            "height": h
        };
    }

    Update(time, tick) {
        var stack = [];
        return stack;
    }

}
class SlideShow2D_Fill_Transition extends SlideShow2D_Transition {

    Initialization() {

    }
    Template(time, tick) {
        return {};
    }

    Update(time, tick) {

        var stack = [];
        if (time == 0) {
            this.ImageA = 1;
            this.ImageB = 2;
            this.ReDrawingImageA = true;
            this.Initialization();
        }

        if (this.ReDrawingImageA) {
            stack.push({
                "command": "clearRect",
                "args": [0, 0, this.canvassize.width, this.canvassize.height]
            }, {
                "command": "DrawCenter",
                "address": this.ImageA,
                "extends": true
            });
        }

        stack.push({
            "command": "save"
        }, {
            "command": "beginPath"
        });

        var sh = this.Template(time, tick);
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
            "command": "DrawCenter",
            "address": this.ImageB,
            "extends": true
        }, {
            "command": "restore"
        });
        return stack;
    }
}
;
class SlideShow2D_Fill_XY_Equation_Transition extends SlideShow2D_Fill_Transition {
    Template(time, tick) {
        var stack = [];
        var radian = 0;
        var radian_add = Math.PI / 180;
        stack.push({
            "command": "translate",
            "args": [this.canvassize.width / 2, this.canvassize.height / 2]
        }, {
            "command": "moveTo",
            "args": [this.MultiplyX(time, tick) * this.GetX(radian, time, tick), this.MultiplyY(time, tick) * this.GetY(radian, time, tick)]
        })

        while (radian <= (Math.PI * 2)) {
            radian += radian_add;
            stack.push({
                "command": "lineTo",
                "args": [this.MultiplyX(time, tick) * this.GetX(radian, time, tick), this.MultiplyY(time, tick) * this.GetY(radian, time, tick)]
            });
        }
        stack.push({
            "command": "translate",
            "args": [-(this.canvassize.width / 2), -(this.canvassize.height / 2)]
        });
        return stack;
    }
    GetX(radian, time, tick) {

    }
    GetY(radian, time, tick) {

    }
    MultiplyX(time, tick) {
        return 1;
    }
    MultiplyY(time, tick) {
        return 1;
    }

}
;
