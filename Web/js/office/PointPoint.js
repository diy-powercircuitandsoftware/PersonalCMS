class PointPoint_Editor {
    //https://www.codeproject.com/Articles/609052/Simple-HTML5-SVG-Move-and-Resize-Tool

    constructor(...args) {
        if (args.length === 1 && typeof args[0] === 'string' || args[0] instanceof String) {
            this.editor = document.querySelector(args[0]).appendChild(document.createElement("DIV"));
        } else if (args.length === 1 && args[0] instanceof HTMLElement) {
            this.editor = args[0].appendChild(document.createElement("DIV"));
        } else {
            this.editor = document.body.appendChild(document.createElement("DIV"));
        }

        this.slides = [];
        this.mode = null;
        this.modelist = ["edit"];

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
    ChangeMode(m) {
        this.mode = m;
    }
    ClearCanvas() {
        this.editor.innerHTML = "";
    }
    AddTextBox(index, txt, x, y) {
        this.slides[index].AddText(txt, x, y);
    }
    AddImage() {

    }
    AddSlideEvent(s) {
        var ref = this;
        s.AddEvent("click", function () {

        });
        s.AddEvent("dblclick", function (e) {

            if (ref.mode == "edit") {
                if (e.target.tagName == "text") {
                    ref.SvgEditText(e.target);
                }
            }
        });

    }
    GetSlides() {
        return this.slides;
    }
    InsertSlide(...args) {
        if (args.length === 1 && (args[0] === null || args[0] instanceof PointPoint_Slide)) {
            var s = args[0];
            this.AddSlideEvent(s);
            this.slides.push(s);
        }
    }
    ReplaceSlideAt(index, slide) {
        if (slide === null || slide instanceof PointPoint_Slide) {
            var s = slide;
            this.AddSlideEvent(s);
            this.slides[index] = s;
        }
    }
    SlidesCount() {
        return this.slides.length;
    }
    SlideExists(index) {
        return !(this.slides[index] === undefined || this.slides[index] === null);
    }
    Render(index) {
        if (this.SlideExists(index)) {
            this.editor.innerHTML = "";
            this.editor.appendChild(this.slides[index].GetSVG());
            return true;
        }
        return false;
    }
    SvgEditText() {

    }

}
class PointPoint_SvgTextConverter {

    ToHtml(txt) {

        let div = document.createElement("DIV");

        div.appendChild(txt.cloneNode(true));

        let txttag = div.getElementsByTagName("tspan");
        for (let i = 0; i < txttag.length; i++) {
            let spanreplace = document.createElement("span");
            spanreplace.innerHTML = txttag[i].textContent;
            txttag[i].parentNode.replaceChild(spanreplace, txttag[i]);
        }


        txttag = div.getElementsByTagName("text");
        for (let i = 0; i < txttag.length; i++) {
            let divreplace = document.createElement("div");
            divreplace.innerHTML = txttag[i].textContent;
            txttag[i].parentNode.replaceChild(divreplace, txttag[i]);
        }



        return div.innerHTML;
    }
    ToSvg(html) {
        let svg = document.createElementNS("http://www.w3.org/2000/svg", "svg");
        let div = document.createElement("DIV");
        div.innerHTML=html;
let  txttag = div.getElementsByTagName("div");


    }

}
class PointPoint_Player {

}

class PointPoint_Animation {

}

class PointPoint_Slide {
    constructor(...args) {
        this.slidearea = document.createElementNS("http://www.w3.org/2000/svg", "svg");
        this.fontsize = 18;
    }
    AddEvent(...args) {
        this.slidearea.addEventListener(...args);
    }

    AddText(input, x, y) {
        //tspan 
        //input is string ?
        //input is texttag?
        var txt = document.createElementNS("http://www.w3.org/2000/svg", "text");
        txt.setAttributeNS(null, "x", x);
        txt.setAttributeNS(null, "y", y);
        txt.setAttributeNS(null, "font-size", this.fontsize);
        txt.appendChild(document.createTextNode(input));
        this.slidearea.appendChild(txt);
    }
    GetAllElementsTag() {
        var out = [];
        var t = this.slidearea.getElementsByTagName("text");
        for (var i = 0; i < t.length; i++) {
            out.push({"type": "text"});
        }
        return out;
    }
    GetSVG() {
        return  this.slidearea;
    }
    Index(...args) {
        if (args.length === 0) {
            return this.slidearea.getAttribute("index");
        } else if (args.length === 1) {
            return this.slidearea.setAttribute("index", args[0]);
        }
    }
    Size(...args) {
        if (args.length === 0) {
            return this.slidearea.getAttribute("viewBox");
        } else if (args.length === 1) {
            var w = parseInt(args[0].width);
            var h = parseInt(args[0].height);
            this.slidearea.setAttribute("viewBox", "0 0" + " " + w + " " + h);
        } else if (args.length === 2) {
            var w = parseInt(args[0]);
            var h = parseInt(args[1]);
            this.slidearea.setAttribute("viewBox", "0 0" + " " + w + " " + h);
        }
    }
    XMLString(...args) {
        if (args.length === 0) {
            var xml = new XMLSerializer();
            return  xml.serializeToString(this.slidearea);
        } else if (args.length === 1) {
            var parser = new DOMParser();
            var doc = parser.parseFromString(args[0], "image/svg+xml");
            this.slidearea = doc.documentElement;

        }

    }

}



