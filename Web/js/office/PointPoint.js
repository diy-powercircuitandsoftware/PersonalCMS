class PointPoint_Animation {
    GetName() {

    }
    Render() {

    }
}
class PointPoint_Editor {
    constructor(...args) {
        if (args.length === 1 && typeof args[0] === 'string' || args[0] instanceof String) {
            this.editor = document.querySelector(args[0]).appendChild(document.createElement("DIV"));
        } else if (args.length === 1 && args[0] instanceof HTMLElement) {
            this.editor = args[0].appendChild(document.createElement("DIV"));
        } else {
            this.editor = document.body.appendChild(document.createElement("DIV"));
        }
        this.editor.style.position = "relative";
        this.slides = [];
        this.mode = null;
        this.selectitem = null;

    }
    CanvasSize(...args) {
        if (args.length === 0) {
            return {
                "width": this.editor.style.width,
                "height": this.editor.style.height
            };
        } else if (args.length === 2) {
            this.editor.style.width = args[0];
            this.editor.style.height = args[1];
        }
    }
    AddEditorEvent(...args) {
        this.editor.addEventListener(...args);
    }

    AddTextBox(index, x, y) {
        var ref = this.slides[index].AddText("Edit This Text", x, y);
        var txtbox = this.editor.appendChild(ref.cloneNode(true));
        txtbox.ref = ref;
        txtbox.contentEditable = "true";
        txtbox.fnref = this;
        txtbox.addEventListener("input", function () {
            this.ref.innerHTML = this.innerHTML;
        });
        txtbox.addEventListener("mousedown", function (e) {
            this.fnref.selectitem = this;
            if (this.fnref.mode == "delete") {
                this.ref.parentNode.removeChild(this.ref);
                this.parentNode.removeChild(this);
            } else if (this.fnref.mode == "move") {
                this.moving = true;
                this.movingoffset = {
                    "x": this.offsetLeft - e.clientX,
                    "y": this.offsetTop - e.clientY
                };
            }
        });
        txtbox.addEventListener("mousemove", function (e) {
            if (this.moving) {
                e.preventDefault();
                this.style.left = (e.clientX + this.movingoffset.x) + 'px';
                this.style.top = (e.clientY + this.movingoffset.y) + 'px';
            }
        });
        txtbox.addEventListener("mouseup", function () {
            if (this.moving) {
                var parentoffset = {
                    "w": parseInt(this.parentNode.style.width),
                    "h": parseInt(this.parentNode.style.height)
                }
                var currentoffset = {
                    "x": parseInt(this.style.left),
                    "y": parseInt(this.style.top)
                };
                var newoffset = {
                    "x": (currentoffset.x / parentoffset.w) * 100,
                    "y": (currentoffset.y / parentoffset.h) * 100
                }
                this.ref.style.left = newoffset.x + "%";
                this.ref.style.top = newoffset.y + "%";
                this.moving = false;
            }

        });
        txtbox.addEventListener("mouseout", function () {
            if (this.moving) {
                var parentoffset = {
                    "w": parseInt(this.parentNode.style.width),
                    "h": parseInt(this.parentNode.style.height)
                }
                var currentoffset = {
                    "x": parseInt(this.style.left),
                    "y": parseInt(this.style.top)
                };
                var newoffset = {
                    "x": (currentoffset.x / parentoffset.w) * 100,
                    "y": (currentoffset.y / parentoffset.h) * 100
                }
                this.ref.style.left = newoffset.x + "%";
                this.ref.style.top = newoffset.y + "%";
                this.moving = false;
            }
        });

    }
    AddImage() {

    }
    EXECommand(...args) {
        document.execCommand(...args);
    }
    QueryCommandState(cmd) {
        return document.queryCommandState(cmd);
    }
    GetSlides() {
        return this.slides;
    }
    InsertSlide(...args) {
        if (args.length === 1 && (args[0] === null || args[0] instanceof PointPoint_Slide)) {
            this.slides.push(args[0]);
        }
    }
    ReplaceSlideAt(index, slide) {
        if (slide === null || slide instanceof PointPoint_Slide) {
            this.slides[index] = slide;
        }
    }
    Size(index, w, h) {
        this.slides[index].Size(w, h);
    }
    SlidesCount() {
        return this.slides.length;
    }
    SlideExists(index) {
        return !(this.slides[index] === undefined || this.slides[index] === null);
    }
    Render(index) {
        this.editor.innerHTML = "";
        if (this.SlideExists(index) && index >= 0) {
            var slides = this.slides[index].GetSlideData();
            var slidesroot = slides.children;
            for (var i = 0; i < slidesroot.length; i++) {
                //  console.log(slidesroot[i]) ;
                var objmove = null;
                if (slidesroot[i].getAttribute("data-item") == "text") {

                    var txtele = slidesroot[i];
                    var editor = this.editor.appendChild(txtele.cloneNode(true));
                    editor.ref = txtele;
                    editor.fnref = this;
                    editor.contentEditable = "true";

                    editor.addEventListener("input", function () {

                        this.ref.innerHTML = this.innerHTML;

                    });
                    objmove = editor;

                }
                if (objmove !== null) {
                    objmove.addEventListener("mousedown", function (e) {
                        this.fnref.selectitem = this.ref;
                        if (this.fnref.mode == "delete") {
                            this.ref.parentNode.removeChild(this.ref);
                            this.parentNode.removeChild(this);
                        } else if (this.fnref.mode == "move") {
                            this.moving = true;
                            this.movingoffset = {
                                "x": this.offsetLeft - e.clientX,
                                "y": this.offsetTop - e.clientY
                            };
                        }

                    });
                    objmove.addEventListener("mousemove", function (e) {
                        if (this.moving) {
                            e.preventDefault();
                            this.style.left = (e.clientX + this.movingoffset.x) + 'px';
                            this.style.top = (e.clientY + this.movingoffset.y) + 'px';

                        }
                    });
                    objmove.addEventListener("mouseup", function () {
                        if (this.moving) {
                            var parentoffset = {
                                "w": parseInt(this.parentNode.style.width),
                                "h": parseInt(this.parentNode.style.height)
                            }
                            var currentoffset = {
                                "x": parseInt(this.style.left),
                                "y": parseInt(this.style.top)
                            };
                            var newoffset = {
                                "x": (currentoffset.x / parentoffset.w) * 100,
                                "y": (currentoffset.y / parentoffset.h) * 100
                            }
                            this.ref.style.left = newoffset.x + "%";
                            this.ref.style.top = newoffset.y + "%";
                            this.moving = false;
                        }

                    });
                    objmove.addEventListener("mouseout", function () {
                        if (this.moving) {
                            var parentoffset = {
                                "w": parseInt(this.parentNode.style.width),
                                "h": parseInt(this.parentNode.style.height)
                            }
                            var currentoffset = {
                                "x": parseInt(this.style.left),
                                "y": parseInt(this.style.top)
                            };
                            var newoffset = {
                                "x": (currentoffset.x / parentoffset.w) * 100,
                                "y": (currentoffset.y / parentoffset.h) * 100
                            }
                            this.ref.style.left = newoffset.x + "%";
                            this.ref.style.top = newoffset.y + "%";
                            this.moving = false;
                        }
                    });
                }
            }

            return true;
        }
        return false;
    }

}

class PointPoint_Player {
    constructor(...args) {
        if (args.length === 1 && typeof args[0] === 'string' || args[0] instanceof String) {
            this.div = document.querySelector(args[0]).appendChild(document.createElement("div"));
        } else if (args.length === 1 && args[0] instanceof HTMLElement) {
            this.div = args[0].appendChild(document.createElement("div"));
        } else {
            this.div = document.body.appendChild(document.createElement("div"));
        }
        this.domlist = this.div.appendChild(document.createElement("div"));
        this.canvas = this.div.appendChild(document.createElement("canvas"));

        this.slides = [];
        this.slidesindex = -1;
        this.slidesitemindex = 0;
        this.domlist.style.position = "relative";
        /* var render = new PointPoint_Player_RenderEngine();
         render.ref = this;
         render.SetAnimate(function (v) {
         if (this.ref.slidesindex < this.ref.slides.length) {
         var ctx = this.ref.canvas.getContext('2d');
         
         if (slideobj !== null) {
         var root = slideobj.GetSlideData();
         var rootwidth = root.getAttribute("width");
         var rootheight = root.getAttribute("height");
         if (this.ref.canvas.width == rootwidth && this.ref.canvas.height == rootheight) {
         ctx.clearRect(0, 0, rootwidth, rootheight);
         var cn = root.childNodes;
         
         for (var i = 0; i < Math.min(cn.length, this.ref.slidesitemindex); i++) {
         
         
         if (cn[i].tagName == "text") {
         
         var x = (parseInt(rootwidth) / 100) * (parseFloat(cn[i].getAttribute("x")));
         var y = (parseInt(rootheight) / 100) * (parseFloat(cn[i].getAttribute("y")));
         //animation
         var textnode = cn[i].childNodes;
         //x=0;
         for (var itn = 0; itn < textnode.length; itn++) {
         
         
         if (textnode[itn].tagName == "text") {
         
         }
         //                                 
         }
         // ctx.font = '48px serif';
         
         //     ctx.fillStyle = cn[i].getAttribute("color");
         //console.log(cn[i]);
         
         }
         }
         
         
         } else {
         this.ref.canvas.width = root.getAttribute("width");
         this.ref.canvas.height = root.getAttribute("height");
         }
         
         }
         }
         
         });
         render.Start();*/
    }
    AddPlayerEvent(...args) {
        this.div.addEventListener(...args);
    }
    AddSlide(s) {
        if (s === null || s instanceof PointPoint_Slide) {
            this.slides.push(s);
        }
    }
    IsNull() {
        return  this.slides[this.slidesindex] === null;
    }
    NextItem() {
        if (this.slidesindex >= 0) {
            var sd = this.slides[this.slidesindex].GetSlideData();
            this.domlist.style.width = sd.style.width;
            this.domlist.style.height = sd.style.height;
            var cn = Array.from(sd.childNodes);
            if (this.slidesitemindex < cn.length) {
                this.domlist.appendChild(cn[ this.slidesitemindex].cloneNode(true));
                this.slidesitemindex++;
                return true;
            }
        }
        return false;      
    }
    NextSlide() {
        this.domlist.innerHTML = "";
        if (this.slidesindex < this.slides.length - 1) {
            this.slidesindex++;
            this.slidesitemindex = 0;
            return true;
        }
        return false;
    }
    ReplaceSlideAt(index, slide) {
        if (slide === null || slide instanceof PointPoint_Slide) {
            this.slides[index] = slide;
        }
    }
}

class PointPoint_Player_RenderEngine {
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


class PointPoint_Slide {
    constructor( ) {

        this.slidedata = document.createElement("DIV");
        this.slidedata.style.position = "relative";
    }

    AddText(input, x, y) {
        var txt = document.createElement("DIV");
        txt.style.position = "absolute";
        txt.style.left = x;
        txt.style.top = y;
        txt.appendChild(document.createTextNode(input));
        txt.setAttribute("data-item", "text");
        this.slidedata.appendChild(txt);
        return txt;

    }
    GetSlideData() {
        return this.slidedata;
    }
    Index(...args) {
        if (args.length === 0) {
            return this.slidedata.getAttribute("data-index");
        } else if (args.length === 1) {
            return this.slidedata.setAttribute("data-index", args[0]);
        }
    }
    Serialize(...args) {
        if (args.length === 0) {
            return  {
                "Html": this.slidedata.innerHTML,
                "Width": this.slidedata.style.width,
                "Height": this.slidedata.style.height,
                "Index": this.slidedata.getAttribute("data-index")
            }
        } else if (args.length === 1) {
            this.slidedata.innerHTML = args[0]["Html"];
            this.slidedata.style.width = args[0]["Width"];
            this.slidedata.style.height = args[0]["Height"];
            this.slidedata.setAttribute("data-index", args[0]["Index"]);
        }
    }
    Size(...args) {
        if (args.length === 0) {
            return {
                "width": this.slidedata.style.width,
                "height": this.slidedata.style.height
            };
        } else if (args.length === 1) {
            this.slidedata.style.width = args[0].width;
            this.slidedata.style.height = args[0].height;
        } else if (args.length === 2) {
            this.slidedata.style.width = args[0];
            this.slidedata.style.height = args[1];
        }
    }

}


class PointPoint_LeftToRight_Animation extends PointPoint_Animation {
    GetName() {
        return "LeftToRight";
    }
    Render() {

    }
}


