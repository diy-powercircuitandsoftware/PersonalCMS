class PointPoint {
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

