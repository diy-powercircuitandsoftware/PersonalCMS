class PlayingList {
    constructor(...args) {
        if (args.length === 1 && typeof args[0] === 'string' || args[0] instanceof String) {
            this.list = document.querySelector(args[0]).appendChild(document.createElement("UL"));
        } else if (args.length === 1 && args[0] instanceof HTMLElement) {
            this.list = args[0].appendChild(document.createElement("UL"));
        } else {
            this.list = document.body.appendChild(document.createElement("UL"));
        }
        this.list.style.listStyle = "none";
        this.list.style.padding = "0";
        this.list.style.margin = "0";
        this.list.addEventListener("click", function (e) {
            if (e.target.tagName == "LI") {
                this.ref.Select(e.target.getAttribute("data-id"));
                this.Last = e.target;
            }
        });
          this.list.ref=this;
    }
    AddList(id, name) {
        var li = this.list.appendChild(document.createElement("LI"));
        li.appendChild(document.createTextNode(name));
        li.setAttribute("data-id", id);
         
    }
    Empty() {
        this.list.innerHTML = "";
    }
    Select(v) {
        if (typeof v === "function") {
            this.Select = v;
        }
    }
}







/*
 
 
 Method.Click = function (e) {
 
 Method.IsLast = function () {
 return this.Last == this.children[this.children.length - 1];
 };
 Method.GetNext = function () {
 if (this.Last !== undefined) {
 var nodes = Array.prototype.slice.call(this.children);
 var n = nodes.indexOf(this.Last);
 var next = (n + 1) % this.children.length;
 return this.children[next];
 }
 return null;
 };
 Method.GetRandom = function () {
 if (this.Last !== undefined) {
 var i = Math.floor(Math.random() * (this.children.length));
 return this.children[i];
 }
 return null;
 };
 
 
 return Method;
 }
 
 */