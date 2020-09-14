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
            var slidesroot = slides.children;
            for (var i = 0; i < slidesroot.length; i++) {
                if (slidesroot[i].tagName == "text") {
                    var txtele = slidesroot[i];
                    var editor = this.editor.appendChild(this.TextObject2Html(txtele));
                    editor.ref = txtele;
                    editor.fnref = this;
                    editor.addEventListener("input", function () {
                        var node = (this.fnref.Html2TextObject(this));
                        for (i = 0; i < this.ref.attributes.length; i++)
                        {
                            var a = this.ref.attributes[i];
                            node.setAttribute(a.name, a.value);
                        }

                        this.ref.parentNode.replaceChild(node, this.ref);
                        this.ref = node;

                    });


                }
            }

            return true;
        }
        return false;
    }
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
        if (txtele.children.length == 0) {
            var nodee = document.createElement("DIV");
            txtbox.appendChild(nodee);
            nodee.textContent = txtele.textContent;

            if (txtele.getAttribute("bold") == "true") {
                nodee.style.fontWeight = "bold";
            }
            if (txtele.getAttribute("italic") == "true") {
                nodee.style.fontStyle = "italic";
            }
            if (txtele.getAttribute("underline") == "true") {
                nodee.style.textDecoration = "underline";
            }
            if (txtele.getAttribute("color") !== null) {
                nodee.style.color = txtele.getAttribute("color");
            }

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
                //if list for loop 
                //if parrent list stop
                
                if (tree.currentNode.tagName == "text") {
                    var nodee = document.createElement("DIV");
                    txtbox.appendChild(nodee);
                    nodee.textContent = tree.currentNode.textContent;
                    if (tree.currentNode.getAttribute("bold") == "true") {
                        nodee.style.fontWeight = "bold";
                    }
                    if (tree.currentNode.getAttribute("italic") == "true") {
                        nodee.style.fontStyle = "italic";
                    }
                    if (tree.currentNode.getAttribute("underline") == "true") {
                        nodee.style.textDecoration = "underline";
                    }
                    if (tree.currentNode.getAttribute("color") !== null) {
                        nodee.style.color = tree.currentNode.getAttribute("color");
                    }
                } else if (tree.currentNode.tagName == "list") {
                    var nodee =null;
                    if (tree.currentNode.getAttribute("type")=="ol"){
                            nodee=  document.createElement("ol");
                    }else{
                        nodee=  document.createElement("ul");
                    }
                   
                    var child =tree.currentNode.childNodes;
                    
                    for (var i = 0; i < child.length; i++) {
                       nodee.appendChild(document.createElement("li")).innerHTML=child[i].textContent;
                    }
 
                    txtbox.appendChild(nodee);
                }

            }
        }
        txtbox.class = "TextBoxEditable";
        txtbox.contentEditable = "true";
        txtbox.style.position = "absolute";
        txtbox.style.top = txtele.getAttribute("y");
        txtbox.style.left = txtele.getAttribute("x");
        return txtbox;
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



