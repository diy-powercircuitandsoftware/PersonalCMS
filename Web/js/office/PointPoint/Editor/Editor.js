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
        this.editor.ref = this;
        this.selectitem = null;
        this.AddEditorEvent("mousedown", function (e) {
            this.ref.selectitem = null;
            var current = e.target;
            while (current && (current !== this)) {
                if (current.getAttribute("pointpoint-type") !== null && current.getAttribute("pointpoint-type") !== "slide") {
                    this.ref.selectitem = current;
                    break;
                }
                current = current.parentNode;

            }

            if (this.ref.selectitem !== null) {
                e.selectitem = this.ref.selectitem;
                e.selectitemtype = e.selectitem.getAttribute("pointpoint-type");
            }
            this.ref.MouseDown(e);
        });

        this.AddEditorEvent("mousemove", function (e) {
            if (this.ref.selectitem !== null) {
                var rect = this.getBoundingClientRect();
                e.selectitem = this.ref.selectitem;
                e.selectitemtype = e.selectitem.getAttribute("pointpoint-type");
                e.point_x = (e.clientX - rect.left);
                e.point_y = (e.clientY - rect.top);
            }

            this.ref.MouseMove(e);
        });
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

    AddImage() {

    }
    EXECommand(...args) {
        document.execCommand(...args);
    }
    GetHtmlCode() {
        return  this.editor.innerHTML;
    }
    MouseDown() {

    }
    MouseMove() {

    }
    QueryCommandState(cmd) {
        return document.queryCommandState(cmd);
    }

    Render(dom) {
        this.editor.innerHTML = "";
        this.editor.appendChild(dom);
    }

}



/*
 *  AddTextBox(index, x, y) {
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
 */