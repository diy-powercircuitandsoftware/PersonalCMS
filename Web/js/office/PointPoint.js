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
        this.editor.style.position = "relative";
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

    AddTextBox(index, x, y) {
        var txtbox = this.editor.appendChild(document.createElement("DIV"));
        txtbox.innerHTML = "Edit This Text";
        txtbox.contentEditable = "true";
        txtbox.style.position = "absolute";
        txtbox.style.top = y;
        txtbox.style.left = x;
        txtbox.ref = this.slides[index].AddText(txtbox.innerHTML, x, y);
        txtbox.addEventListener("keydown", function () {
            this.normalize();

            this.ref.textContent = "";

        });
    }
    AddImage() {

    }
    EXECommand(...args) {
        document.execCommand(...args);
    }
    EXECommandState(cmd) {
        /*   var CommandList = ["bold", "copy", "cut", "decreaseFontSize",
         "insertHorizontalRule", "increaseFontSize", "indent", "italic",
         "justifyLeft", "justifyCenter", "justifyRight", "justifyFull",
         "insertOrderedList", "outdent", "insertParagraph", "paste",
         "redo", "removeFormat", "unlink", "strikeThrough", "subscript", "superscript", "underline", "undo", "insertUnorderedList"];
         if (CommandList.indexOf(cmd) >= 0) {
         return this.editor.contentWindow.document.queryCommandState(cmd);
         }*/
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
        if (this.SlideExists(index) && index >= 0) {
            var slides = this.slides[index].GetSlideData();
            var txtele = slides.getElementsByTagName("text");

            for (var i = 0; i < txtele.length; i++) {
                var txtbox = this.editor.appendChild(document.createElement("DIV"));
                txtbox.innerHTML = txtele[i].textContent;
                txtbox.contentEditable = "true";
                txtbox.style.position = "absolute";
                txtbox.style.top = txtele[i].getAttribute("y");
                txtbox.style.left = txtele[i].getAttribute("x");
                txtbox.ref = txtele[i];
                txtbox.addEventListener("keydown", function () {
                    this.normalize();
                    this.ref.textContent = "";
                    var tree = document.createTreeWalker(this, NodeFilter.SHOW_TEXT);
                    while (tree.nextNode()) {
                        var newnode = document.createElement("text");
                        var text = tree.currentNode;
                        //parrentnode to css
                        this.ref.appendChild(newnode);
                        newnode.textContent = text.nodeValue;
                        if (text.parentNode.tagName.toLowerCase() === "b") {
                            newnode.setAttribute("bold", "true");
                        }
                        if (text.parentNode.tagName.toLowerCase() === "i") {
                            newnode.setAttribute("italic", "true");
                        }
                        if (text.parentNode.tagName.toLowerCase() === "u") {
                            newnode.setAttribute("underline", "true");
                        }

                    }
                    console.log(this.ref);
                });
            }

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



