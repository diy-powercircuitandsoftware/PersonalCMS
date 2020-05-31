class SpanList{
     constructor(...args) {
        if (args.length === 1 && typeof args[0] === 'string' || args[0] instanceof String) {
            this.list = document.querySelector(args[0]).appendChild(document.createElement("DIV"));
        } else if (args.length === 1 && args[0] instanceof HTMLElement) {
            this.list = args[0].appendChild(document.createElement("DIV"));
        } else {
            this.list = document.body.appendChild(document.createElement("DIV"));
        }
    }
    
}




/*
function SpanList() {
    var Method = document.createElement("");
    Method.UISearch = Method.appendChild(document.createElement("DIV"));
    Method.TextBox = Method.UISearch.appendChild(document.createElement("INPUT"));
    Method.LockPosition = Method.UISearch.appendChild(document.createElement("DIV"));
    Method.ULList = Method.LockPosition.appendChild(document.createElement("UL"));
    Method.TextBox.type = "text";
    Method.TextBox.style.cssText = "outline: none;width:100%;";
    Method.LockPosition.style.cssText = "position: relative;";
    Method.ULList.style.cssText = "background-color:white;width:100%;border-style: solid;border-width: thin;position: absolute;list-style: none";
    Method.style.cssText = "word-wrap: break-word;overflow-wrap: break-word;border-style: solid;border-width: thin;width: 99%;height: 100%;background-color: white;";

    Method.AddList = function (id, name) {
        this.LockPosition.style.zIndex = this.style.zIndex;
        var li = this.ULList.appendChild(document.createElement("LI"));
        li.addEventListener("click", function () {
            this.parentNode.parentNode.parentNode.parentNode.AddSelectList(this.getAttribute("data-id"), this.textContent);
            this.parentNode.parentNode.parentNode.parentNode.TextBox.value = "";
            this.parentNode.parentNode.parentNode.parentNode.TextBox.focus();
            this.parentNode.innerHTML = "";
        });
        li.addEventListener("mouseover", function () {
            this.style.cursor = "pointer";
        });
        li.addEventListener("mouseout", function () {
            this.style.cursor = "";
        });
        li.setAttribute("data-id", id);
        li.innerHTML = name;

    };
    Method.AddSelectList = function (id, name) {
        var selectlist = this.insertBefore(document.createElement("SPAN"), this.UISearch);
        selectlist.style.cssText = "display: inline-block;margin-left: 3px;border-style: solid;border-width: thin;";
        selectlist.setAttribute("data-id", id);
        selectlist.appendChild(document.createTextNode(name));
        var bnremove = selectlist.appendChild(document.createElement("SPAN"));
        bnremove.innerHTML = "X";
        bnremove.style.cssText = "color:red;cursor:pointer;margin-left: 3px;border-style: solid;border-width: thin;";
        bnremove.addEventListener("click", function () {
            this.parentNode.parentNode.removeChild(this.parentNode);
        });
    };
    Method.Clear = function () {
        this.ULList.innerHTML = "";
    };
    Method.Empty = function () {
        [].forEach.call(this.querySelectorAll("SPAN[data-id]"), function (span) {
            span.parentNode.removeChild(span);
        });
    };
    Method.GetList = function () {
        var List = this.querySelectorAll("SPAN[data-id]");
        var arr = [];
        [].forEach.call(List, function (span) {
            arr.push(span.getAttribute("data-id"));
        });
        return arr;
    };
    Method.Enter = function (v) {
        
    };
    Method.Input = function (v) {
        
    };


    Method.TextBox.addEventListener("keyup", function (event) {
        var k = event.which || event.keyCode;
        if (k == 8) {
            var span = this.parentNode.parentNode.querySelectorAll("SPAN[data-id]");
            if (span.length > 0 && this.value == "") {
                var lastspan = span[span.length - 1];
                lastspan.parentNode.removeChild(lastspan);
            }
        } else if (k == 13) {
            this.parentNode.parentNode.Enter(this.value);
        } else {
            this.parentNode.parentNode.ULList.innerHTML = "";
            this.parentNode.parentNode.Input(this.value);
        }
    });
    Method.addEventListener("click", function (e) {
        if (e.target == this) {
            Method.TextBox.focus();
        }
    });
    return Method;
}*/