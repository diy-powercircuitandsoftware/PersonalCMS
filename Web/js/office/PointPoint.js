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
        this.converter = new PointPoint_HtmlConverter();
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
        var txtbox = this.editor.appendChild(document.createElement("DIV"));
        txtbox.innerHTML = "Edit This Text";
        txtbox.contentEditable = "true";
        txtbox.style.position = "absolute";
        txtbox.style.top = y;
        txtbox.style.left = x;
        txtbox.ref = this.slides[index].AddText(txtbox.innerHTML, x, y);
        txtbox.fnref = this;

        txtbox.addEventListener("input", function () {
            var node = (this.fnref.converter.Html2TextObject(this));
            this.ref.innerHTML = node.innerHTML;

        });
        txtbox.addEventListener("mousedown", function (e) {
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
                this.ref.setAttribute("x", newoffset.x + "%");
                this.ref.setAttribute("y", newoffset.y + "%");

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
                this.ref.setAttribute("x", newoffset.x + "%");
                this.ref.setAttribute("y", newoffset.y + "%");
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
                if (slidesroot[i].tagName == "text") {
                    var txtele = slidesroot[i];
                    var editor = this.editor.appendChild(this.converter.TextObject2Html(txtele));
                    editor.ref = txtele;
                    editor.fnref = this;
                    editor.contentEditable = "true";
                    editor.style.position = "absolute";
                    editor.addEventListener("input", function () {
                        var node = (this.fnref.converter.Html2TextObject(this));
                        this.ref.innerHTML = node.innerHTML;

                    });
                    editor.addEventListener("mousedown", function (e) {
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
                    editor.addEventListener("mousemove", function (e) {
                        if (this.moving) {
                            e.preventDefault();
                            this.style.left = (e.clientX + this.movingoffset.x) + 'px';
                            this.style.top = (e.clientY + this.movingoffset.y) + 'px';

                        }
                    });
                    editor.addEventListener("mouseup", function () {
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
                            this.ref.setAttribute("x", newoffset.x + "%");
                            this.ref.setAttribute("y", newoffset.y + "%");
                            this.moving = false;
                        }

                    });
                    editor.addEventListener("mouseout", function () {
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
                            this.ref.setAttribute("x", newoffset.x + "%");
                            this.ref.setAttribute("y", newoffset.y + "%");
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
class PointPoint_HtmlConverter {
    Html2TextObject(htmldom) {

        var newnode = document.createElement("text");
        let tree = document.createTreeWalker(htmldom, NodeFilter.SHOW_TEXT);

        [].forEach.call(htmldom.querySelectorAll("ul,ol"), function (list) {
            list.listref = document.createElement("list");
        });

        while (tree.nextNode()) {

            var textnode = document.createElement("text");
            var text = tree.currentNode;
            var parrentnode = text.parentNode;
            var inlist = false;

            textnode.textContent = text.nodeValue;


            while (parrentnode !== htmldom) {
                var tagname = parrentnode.tagName.toLowerCase();
                switch (tagname) {
                    case "b":
                        textnode.setAttribute("bold", "true");
                        break;
                    case "i":
                        textnode.setAttribute("italic", "true");
                        break;
                    case "u":
                        textnode.setAttribute("underline", "true");
                        break;
                    case "ol":
                        inlist = parrentnode.listref;
                        inlist.setAttribute("type", "ol");
                        break;
                    case "ul":
                        inlist = parrentnode.listref;
                        inlist.setAttribute("type", "ul");
                        break;
                }
                parrentnode = parrentnode.parentNode;
            }
            textnode.setAttribute("color", window.getComputedStyle(text.parentNode).getPropertyValue("color"));

            if (!inlist) {
                newnode.appendChild(textnode);
            } else if (inlist.parentNode == null) {
                inlist.appendChild(textnode)
                newnode.appendChild(inlist);
            } else if (inlist.parentNode !== null) {
                inlist.appendChild(textnode)
            }
        }
        return newnode;
    }
    TextObject2Html(txtele) {
        var txtbox = document.createElement("DIV");
        var noderef = [];
        if (txtele.children.length == 0) {
            var nodee = document.createElement("DIV");
            txtbox.appendChild(nodee);
            nodee.textContent = txtele.textContent;
            nodee.attr = txtele.attributes;
            noderef.push(nodee);


        } else {
            let tree = document.createTreeWalker(txtele, NodeFilter.SHOW_ELEMENT, function (node) {
                if (node.tagName == "text") {
                    var parrentnode = node.parentNode;
                    while (parrentnode !== txtele) {
                        var tagname = parrentnode.tagName.toLowerCase();
                        if (tagname == "list") {
                            return  NodeFilter.FILTER_REJECT;
                        }
                        parrentnode = parrentnode.parentNode;
                    }
                    return    NodeFilter.FILTER_ACCEPT;
                }
                return    NodeFilter.FILTER_ACCEPT;

            });
            while (tree.nextNode()) {

                if (tree.currentNode.tagName == "text") {
                    var nodee = document.createElement("DIV");
                    txtbox.appendChild(nodee);
                    nodee.textContent = tree.currentNode.textContent;
                    nodee.attr = tree.currentNode.attributes;
                    noderef.push(nodee);

                } else if (tree.currentNode.tagName == "list") {
                    var list = null;
                    if (tree.currentNode.getAttribute("type") == "ol") {
                        list = document.createElement("ol");
                    } else {
                        list = document.createElement("ul");
                    }

                    var child = tree.currentNode.childNodes;

                    for (var i = 0; i < child.length; i++) {
                        var nodee = list.appendChild(document.createElement("li")).appendChild(document.createElement("DIV"))
                        nodee.textContent = child[i].textContent;
                        nodee.attr = child[i].attributes;
                        noderef.push(nodee);

                    }
                    list.attr = tree.currentNode.attributes;
                    noderef.push(list);

                    txtbox.appendChild(list);
                }

            }
        }
        for (var i = 0; i < noderef.length; i++) {

            if (noderef[i].attr["color"]) {
                noderef[i].style.color = noderef[i].attr.getNamedItem("color").value;
            }
            if (noderef[i].attr["bold"]) {

                noderef[i].style.fontWeight = "bold";
            }
            if (noderef[i].attr["italic"]) {

                noderef[i].style.fontStyle = "italic";
            }
            if (noderef[i].attr["underline"]) {

                noderef[i].style.textDecoration = "underline";
            }


        }
        txtbox.style.top = txtele.getAttribute("y");
        txtbox.style.left = txtele.getAttribute("x");
        return txtbox;
    }
}


class PointPoint_Player {
    constructor(...args) {
        if (args.length === 1 && typeof args[0] === 'string' || args[0] instanceof String) {
            this.canvas = document.querySelector(args[0]).appendChild(document.createElement("canvas"));
        } else if (args.length === 1 && args[0] instanceof HTMLElement) {
            this.canvas = args[0].appendChild(document.createElement("canvas"));
        } else {
            this.canvas = document.body.appendChild(document.createElement("canvas"));
        }
        this.slides = [];
        this.slidesindex = 0;
        this.slidesitemindex = 0;
        var render = new PointPoint_Player_RenderEngine();
        render.ref = this;
        render.SetAnimate(function (v) {
            if (this.ref.slidesindex < this.ref.slides.length) {
                var ctx = this.ref.canvas.getContext('2d');
                var slideobj = this.ref.slides[this.ref.slidesindex];
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
                                        if (textnode[itn].getAttribute("color") !== null) {
                                            ctx.fillStyle = (textnode[itn].getAttribute("color"));

                                        } else if (cn[i].getAttribute("color") !== null) {
                                            ctx.fillStyle = (cn[i].getAttribute("color"));

                                        }






                                        if (textnode[itn].getAttribute("fontsize") !== null) {
                                            //  y = y + parseInt(textnode[itn].getAttribute("fontsize"));
                                        } else if (cn[i].getAttribute("fontsize") !== null) {
                                            // y = y + parseInt(cn[i].getAttribute("fontsize"));
                                        } else {
                                            var metrics = ctx.measureText(textnode[itn].textContent);
                                            var actualHeight = metrics.actualBoundingBoxAscent + metrics.actualBoundingBoxDescent;
                                            y = y + (actualHeight * 1.2);
                                        }
                                        ctx.fillText(textnode[itn].textContent, x, y);

                                    } else if (textnode[itn].tagName == "list") {
                                        var listnode = textnode[itn].childNodes;
                                        for (var iln = 0; iln < listnode.length; iln++) {
                                            if (textnode[itn].getAttribute("color") !== null) {
                                                ctx.fillStyle = (textnode[itn].getAttribute("color"));

                                            } else if (cn[i].getAttribute("color") !== null) {
                                                ctx.fillStyle = (cn[i].getAttribute("color"));

                                            }

                                            if (listnode[iln].getAttribute("fontsize") !== null) {
                                                // y = y + parseInt(listnode[iln].getAttribute("fontsize"));
                                            } else if (cn[i].getAttribute("fontsize") !== null) {
                                                //  y = y + parseInt(cn[i].getAttribute("fontsize"));
                                            } else {
                                                var metrics = ctx.measureText(listnode[iln].textContent);
                                                var actualHeight = metrics.actualBoundingBoxAscent + metrics.actualBoundingBoxDescent;
                                                y = y + (actualHeight * 1.2);
                                            }
                                            ctx.fillText(listnode[iln].textContent, x, y);



                                        }

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
        render.Start();
    }
    AddPlayerEvent(...args) {
        this.canvas.addEventListener(...args);
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
        this.slidesitemindex = this.slidesitemindex + 1;
    }
    NextSlide() {
        this.slidesitemindex = 0;
        this.slidesindex = this.slidesindex + 1;
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
        var xmlString = "<root></root>";
        var parser = new DOMParser();
        this.slidedata = parser.parseFromString(xmlString, "text/xml").documentElement;

    }

    AddText(input, x, y) {
        var txt = document.createElement("text");
        txt.setAttribute("x", x);
        txt.setAttribute("y", y);
        txt.appendChild(document.createTextNode(input));
        this.slidedata.appendChild(txt);
        return txt;

    }
    GetSlideData() {
        return this.slidedata;
    }
    Index(...args) {
        if (args.length === 0) {
            return this.slidedata.getAttribute("index");
        } else if (args.length === 1) {
            return this.slidedata.setAttribute("index", args[0]);
        }
    }

    Size(...args) {
        if (args.length === 0) {
            return {
                "width": this.slidedata.getAttribute("width"),
                "height": this.slidedata.getAttribute("height")
            };
        } else if (args.length === 1) {
            this.slidedata.setAttribute("width", parseInt(args[0].width));
            this.slidedata.setAttribute("height", parseInt(args[0].height));
        } else if (args.length === 2) {
            this.slidedata.setAttribute("width", parseInt(args[0]));
            this.slidedata.setAttribute("height", parseInt(args[1]));
        }
    }
    XMLString(...args) {
        if (args.length === 0) {
            var xml = new XMLSerializer();
            return  xml.serializeToString(this.slidedata);
        } else if (args.length === 1) {
            var parser = new DOMParser();
            var doc = parser.parseFromString(args[0], "text/xml");
            this.slidedata = doc.documentElement;

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


