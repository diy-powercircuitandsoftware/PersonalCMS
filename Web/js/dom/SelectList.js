class SelectList {

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

    }
    AddList(id, name) {
        var li = this.list.appendChild(document.createElement("LI"));
        var checkbox = li.appendChild(document.createElement('input'));
        checkbox.type = "checkbox";
        checkbox.value = id;
        var label = li.appendChild(document.createElement('label'))
        label.style.marginLeft = "7px";
        label.appendChild(document.createTextNode(name));
    }
    GetSelectLists() {
        var dat = [];
        Array.prototype.forEach.call( this.list.querySelectorAll("input[type='checkbox']:checked"), function (node) {
            dat.push(node.value);
        });
        return dat;
    }
    Empty () {
        this.list.innerHTML = "";
    };
}
