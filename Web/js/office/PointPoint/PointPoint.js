class PointPoint {
    constructor( ) {
        this.Slides = [];

    }

    AddSlide(PointPointSlide) {
        this.Slides.push(PointPointSlide);
    }

    Count() {
        return this.Slides.length;
    }

    Get(index) {
        return(this.Slides[index]);
    }
    ReplaceHtml(index, code) {
        if (this.Slides[index] == undefined) {
            this.Slides[index] = new PointPoint_Slide(index);
        }
        this.Slides[index].SetHtml(code);
    }
    ReplaceSlide(index, slide) {
        if (this.Slides[index] == undefined) {
            this.Slides[index] = new PointPoint_Slide(index);
        }
        this.Slides[index].Replace(slide);
    }
    Serialize( ) {
        var html = [];
        for (var i in this.Slides) {
            html.push(this.Slides[i].ToHtml());
        }
        return html;
    }

}


class PointPoint_Slide {
    constructor(...args) {
        this.slideframe = document.createElement("DIV");
        this.slideframe.style.position = "relative";
        this.slideframe.setAttribute("pointpoint-name", "untitle");
        this.slideframe.setAttribute("pointpoint-type", "slide");
        this.slideframe.style.width = "800px";
        this.slideframe.style.height = "600px";
        if (args.length === 1) {
            this.slideframe.setAttribute("pointpoint-index", args[0]);
        }

    }
    AddImage(path) {

    }
    AddText(input, x, y) {
        var txt = document.createElement("DIV");
        txt.setAttribute("pointpoint-type", "text");
        txt.setAttribute("pointpoint-animate", "");
        txt.setAttribute("pointpoint-animate-time", "");
        txt.setAttribute("pointpoint-animate-audio", "");
        txt.setAttribute("pointpoint-animate-audio-type", "");
        txt.style.position = "absolute";
        txt.style.left = x;
        txt.style.top = y;
        txt.appendChild(document.createTextNode(input));
        this.slideframe.appendChild(txt);
        return txt;
    }

    CSS(...args) {
        if (args.length === 0) {
            return this.slideframe.style.cssText;
        } else if (args.length === 1) {
            return this.slideframe.style[args[0]];
        } else if (args.length === 2) {
            return this.slideframe.style[args[0]] = args[1];
        }
    }

    GetSlide() {
        return this.slideframe;
    }

    Index(...args) {
        if (args.length === 0) {
            return this.slideframe.getAttribute("data-index");
        } else if (args.length === 1) {
            return this.slideframe.setAttribute("data-index", args[0]);
        }
    }
    Replace(dom) {
        if (dom.tagName == "DIV" && dom.getAttribute("pointpoint-type") == "slide") {
            [...dom.attributes].forEach(attr => {
                this.slideframe.setAttribute(attr.nodeName, attr.nodeValue)
            })
            this.slideframe.innerHTML = dom.innerHTML;
        }

    }
    SetHtml(html) {
        this.slideframe.innerHTML = html;
    }
    ToHtml() {
        [].forEach.call(this.slideframe.querySelectorAll("[pointpoint-type]"), function (dom) {
            dom.removeAttribute("contenteditable");
            var l = dom.style.left;
            var t = dom.style.top;
            if (l.indexOf("px") > -1) {
                var pw = parseInt(dom.parentNode.style.width);
                dom.style.left = ((parseInt(l) / pw)*100) + "%";

            }
            if (t.indexOf("px") > -1) {
                var ph = parseInt(dom.parentNode.style.height);
                dom.style.top = ((parseInt(t) / ph)*100) + "%";
            }
        });
        return this.slideframe.outerHTML;
    }
}

