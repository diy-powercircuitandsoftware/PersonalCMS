class PointPoint_Editor {
    constructor(...args) {
        if (args.length === 1 && typeof args[0] === 'string' || args[0] instanceof String) {
            this.editor = document.querySelector(args[0]).appendChild(document.createElement("DIV"));
        } else if (args.length === 1 && args[0] instanceof HTMLElement) {
            this.editor = args[0].appendChild(document.createElement("DIV"));
        } else {
            this.editor = document.body.appendChild(document.createElement("DIV"));
        }
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
    AddTextBox(slideindex, txt) {

    }
    GetSlides() {
        return this.slides;
    }
    InsertSlide(...args) {
        if (args.length === 1 && (args[0] === null || args[0] instanceof PointPoint_Slide)) {
            this.slides.push(args[0]);
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
        }
    }

}
class PointPoint_Player {

}

class PointPoint_Animation {

}

class PointPoint_Slide {
    constructor(...args) {
        this.slidearea = document.createElementNS("http://www.w3.org/2000/svg", "svg");
    }
    AddText(txt) {

    }
    GetSVG() {
        return  this.slidearea;

    }
    Size(...args) {
        if (args.length === 0) {
            return {
                "width": this.slidearea.width,
                "height": this.slidearea.height
            };
        } else if (args.length === 2) {
            this.slidearea.width = args[0];
            this.slidearea.height = args[1];
        }
    }
    XMLString(...args) {
        if (args.length === 0) {
            var xml = new XMLSerializer();
            return  xml.serializeToString(this.slidearea);
        } else if (args.length === 0) {
            var parser = new DOMParser();
            this.slidearea = parser.parseFromString(args[0], "svg");
        }

    }

}



