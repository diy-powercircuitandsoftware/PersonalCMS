class Tab {
    constructor(tabarea) {
        this.tabarea = tabarea;
    }
    Add(id, dom) {
        var tabarea = this.tabarea.childNodes;
        var htmllist = [];
        if (dom instanceof HTMLElement) {
            htmllist.push(dom);
        } else if (typeof dom === 'string' || dom instanceof String) {
            [].forEach.call(document.querySelectorAll(dom), function (d) {
                htmllist.push(d);
            });
        }
        for (var i = 0; i < tabarea.length; i++) {
            if (tabarea[i].tabid === id && tabarea[i].tagName === "DIV") {
                for (var j = 0; j < htmllist.length; j++) {
                    tabarea[i].appendChild(htmllist[j]);
                }
                return true;
            }
        }
        var tabdiv = this.tabarea.appendChild(document.createElement("DIV"));
        tabdiv.tabid = id;
        tabdiv.style.display = "none";
        for (var i = 0; i < htmllist.length; i++) {
            tabdiv.appendChild(htmllist[i]);
        }
        return true;
    }

    Show(index) {
        var tabarea = this.tabarea.childNodes;
        for (var i in  tabarea) {
            if (tabarea[i].tabid == index && tabarea[i].tagName === "DIV") {
                tabarea[i].style.display = "block";
            } else if (tabarea[i].tagName === "DIV") {
                tabarea[i].style.display = "none";
            }
        }

    }
}
