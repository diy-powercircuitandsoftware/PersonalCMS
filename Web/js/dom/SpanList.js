class SpanList {
    constructor(...args) {

        if (args.length === 1 && typeof args[0] === 'string' || args[0] instanceof String) {
            this.list = document.querySelector(args[0]).appendChild(document.createElement("DIV"));
        } else if (args.length === 1 && args[0] instanceof HTMLElement) {
            this.list = args[0].appendChild(document.createElement("DIV"));
        } else {
            this.list = document.body.appendChild(document.createElement("DIV"));
        }
        this.list.style.cssText = "border-style: solid;border-width: thin;word-wrap: break-word;";
        this.list.innerHTML = '<div><input data-domspanlist="TxtBox" type="text"  style="border: none;outline: none;width:100%; box-sizing: border-box;" /><ul  data-domspanlist="List" style="cursor:pointer ;padding: 0;margin: 0;list-style: none;"></ul></div>';
        var txtbox = this.list.querySelector('[data-domspanlist="TxtBox"]');
        txtbox.ref = this;
        txtbox.addEventListener("input", function () {
            this.ref.Input(this.value);
        });
        this.list.addEventListener("click", function (e) {
            if (e.target == this) {
                txtbox.focus();
            }
        });

    }
    AddItem(id, value) {
        var span = this.list.insertBefore(document.createElement("SPAN"), this.list.lastElementChild);
        span.appendChild(document.createTextNode(value));
        span.setAttribute("data-id", id);
        span.setAttribute("data-domspanlist", "SpanList");
        span.style.cssText = "display: inline-block;border-style: solid;border-width: thin;margin-left: 3px;margin-top: 3px;";
        var bn = span.appendChild(document.createElement("A"));
        bn.innerHTML = "x";
        bn.href = "#";
        bn.style.cssText = "text-decoration: none;color: red;";
        bn.addEventListener("click", function (e) {
            this.parentNode.parentNode.removeChild(this.parentNode);
        });
    }
    AddList(id, text) {
        var list = this.list.querySelector('[data-domspanlist="List"]');
        var l = list.appendChild(document.createElement("LI"));
        list.style.border = "1px solid";
        l.setAttribute("data-id", id);
        l.innerHTML = text;
        l.ref = this;
        l.addEventListener("click", function () {

            var txtbox = this.ref.list.querySelector('[data-domspanlist="TxtBox"]');
            this.ref.RemoveList();
            txtbox.value = "";
            txtbox.focus();
            this.ref.AddItem(this.getAttribute("data-id"), this.textContent);

        });
    }
    GetItems(){
        var list=[];
        [].forEach.call(this.list.querySelectorAll('[data-domspanlist="SpanList"]'), function (span) {
            list.push(span.getAttribute("data-id"));
        });
        return list;
    }
    Empty() {
        this.RemoveList();
        this.list.querySelector('[data-domspanlist="TxtBox"]').value = "";

        [].forEach.call(this.list.querySelectorAll('[data-domspanlist="SpanList"]'), function (span) {
            span.parentNode.removeChild(span);
        });
    }

    GetList() {
        var arr = [];
        [].forEach.call(this.querySelectorAll('[data-domspanlist="SpanList"]'), function (span) {
            arr.push(span.getAttribute("data-id"));
        });
        return arr;
    }

    Input(v) {
        if (typeof v === "function") {
            this.Input = v;
        } else if (typeof v === 'string' || v instanceof String) {
            this.Input(v);
        }
    }
    RemoveList() {
        var list = this.list.querySelector('[data-domspanlist="List"]');
        list.innerHTML = "";
        list.style.border = "";
    }
}