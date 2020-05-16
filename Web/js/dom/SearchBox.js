class SearchBox {
    constructor(...args) {

        if (args.length === 1 && typeof args[0] === 'string' || args[0] instanceof String) {
            this.searchbox = document.querySelector(args[0]).appendChild(document.createElement("DIV"));
        } else if (args.length === 1 && args[0] instanceof HTMLElement) {
            this.searchbox = args[0].appendChild(document.createElement("DIV"));
        } else {
            this.searchbox = document.body.appendChild(document.createElement("DIV"));
        }


        var htmlcode = '<div>\n\
<input data-domsearchbox="txtbox" style=" width: 100%;box-sizing: border-box;" type="text" name="" value="" />\n\
<div style="position: relative;"><ul  data-domsearchbox="list" style="background-color: white;position: absolute;width:100%;box-sizing: border-box;padding-left: 0;padding-top: 0;list-style: none;margin-top: 0;"></ul></div>\n\
</div>';
        this.searchbox.innerHTML = htmlcode;

    }
    AddItem(id, html) {
        var list = this.searchbox.querySelector('[data-domsearchbox="list"]');
        var li = list.appendChild(document.createElement("LI"));
        li.addEventListener("click", function () {
            this.ref.Calllback(this.getAttribute("data-id"));
             this.parentNode.style.borderStyle = "none";
            this.parentNode.innerHTML="";
            ;
        });
        li.setAttribute("data-id", id);
        li.innerHTML = html;
        li.ref = this;
        list.style.borderStyle = "solid";
        list.style.borderWidth = "thin";
    }
    Calllback(args) {
        if (typeof args === "function") {
            this.Calllback = args;
        }
    }
    ValueChange(args) {
        if (typeof args === "function") {
            var txtbox = this.searchbox.querySelector('[data-domsearchbox="txtbox"]');
            txtbox.addEventListener("input", function () {
                this.uilist.innerHTML = "";
                this.uilist.style.borderStyle = "none";
                this.funccallbank(this.value);
            });
            txtbox.uilist = this.searchbox.querySelector('[data-domsearchbox="list"]');
            txtbox.funccallbank = args;
        }
    }
}