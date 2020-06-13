class TableTools {
    constructor() {
        this.table = document.createElement("TABLE");
    }

    AddEventListener(...args) {
        this.table.addEventListener(...args);
    }
    Border(...args) {
        if (args.length === 0) {
            return this.table.border;
        } else {
            this.table.border = args[0];
        }
    }

    CSS(...args) {
        if (args.length === 0) {
            return this.table.style.cssText;
        } else if (args.length === 1) {
            return  this.table.style[args[0]];
        } else if (args.length === 2) {
            this.table.style[args[0]] = args[1];
        }
    }

    CSSText(...args) {
        if (args.length === 0) {
            return this.table.style.cssText;
        } else if (args.length === 1) {
            this.table.style.cssText = args[0];
        }
    }

    DeleteRowAfter(index) {
        var tr = this.table.getElementsByTagName('TR');
        for (var i = tr.length - 1; i > index; i--) {
            tr[i].parentNode.removeChild(tr[i]);
        }
    }

    DeleteRowAt(index) {
        this.table.deleteRow(index);
    }

    Empty() {
        this.table.innerHTML = "";
    }

    EmptyCell(x, y) {
        this.table.rows[y].cells[x].innerHTML = "";
    }

    EmptyRow(index) {
        this.table.rows[index].innerHTML = "";
    }

    GetCell(x, y) {
        return this.table.rows[y].cells[x];
    }

    GetRow() {
        return this.table.rows;
    }

    GetRowCount() {
        return this.table.rows.length;
    }

    Hide() {
        this.table.style.display = "none";
    }

    HideCell(x, y) {
        this.table.rows[y].cells[x].style.display = "none";
    }

    HideRow(index) {
        this.table.rows[index].style.display = "none";
    }

    Html(...args) {
        if (args.length === 0) {
            return this.table.innerHTML;
        } else if (args.length === 1) {
            this.table.innerHTML = args[0];
        }
    }

    HtmlCell(...args) {
        var x = args[0];
        var y = args[1];
        if (args.length === 2) {
            return  this.table.rows[y].cells[x].innerHTML;
        } else if (args.length === 3) {
            var html = args[2];
            this.table.rows[y].cells[x].innerHTML = html;
        }
    }

    InsertCellAtRow(...args) {
        var index = args[0];
        var data = args[1];
        var c = this.table.rows[index].insertCell(-1);
        if (args.length === 2 && data instanceof HTMLElement) {
            return c.appendChild(data);
        } else if (args.length === 2) {
            c.innerHTML = data;
        }
    }

    InsertCellLastRow(...args) {
        var data = args[0];
        var rows = this.table.rows[this.table.rows.length - 1].insertCell(-1);
        if (data instanceof HTMLElement) {
            return   rows.appendChild(data);
        } else if (args.length === 1) {
            rows.innerHTML = data;
        }
    }

    Import(dom) {
        if (dom instanceof HTMLElement && dom.tagName == "TABLE") {
            var attr = dom.attributes;
            for (var i = 0; i < attr.length; i++) {
                var name = attr[i]["name"].toString();
                var value = attr[i]["value"].toString();
                this.table.setAttribute(name, value);
            }
            var oldrow = dom.rows;
            for (var i = 0; i < oldrow.length; i++) {
                this.table.appendChild(oldrow[i]);
            }
            dom.parentNode.replaceChild(this.table, dom);
        }
        return this;
    }

    InsertRow(...args) {
        if (args.length === 0) {
            return this.table.insertRow(-1);
        } else if (args.length === 1 && typeof args[0] === "number") {
            return  this.table.insertRow(args[0]);
        }
    }

    Show() {
        this.table.style.display = "";
    }

    ShowRow(index) {
        this.table.rows[ index].style.display = "";
    }

    ShowCol(x, y) {
        this.table.rows[y].cells[x].style.display = "";
    }

}


