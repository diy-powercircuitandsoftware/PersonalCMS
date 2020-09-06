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
          this.editor.style.position="relative";
        this.slides = [];
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

    AddTextBox(index,   x, y) {
        var txtbox = this.editor.appendChild(document.createElement("DIV"));
        txtbox.innerHTML = "Edit This Text";
        txtbox.contentEditable = "true";
        txtbox.style.position="absolute";
        txtbox.style.top=y;
          txtbox.style.left=x;
        //  this.slides[index].AddText(txt, x, y);
    }
    AddImage() {

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
    SlidesCount() {
        return this.slides.length;
    }
    SlideExists(index) {
        return !(this.slides[index] === undefined || this.slides[index] === null);
    }
    Render(index) {
        this.editor.innerHTML = "";
        if (this.SlideExists(index)&&index>=0) {
            var s = this.slides[index].GetSlideData();
            console.log(s);
            //  this.editor.appendChild(this.slides[index].GetSlideData());
            return true;
        }
        return false;
    }

}

class PointPoint_Player {

}

class PointPoint_Animation {

}

class PointPoint_Slide {
    constructor( ) {
        var xmlString = "<root></root>";
        var parser = new DOMParser();
        this.slidedata = parser.parseFromString(xmlString, "text/xml").documentElement;

    }

    AddText(input, x, y) {


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



