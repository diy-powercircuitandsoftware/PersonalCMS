class  SQLite_Data_Editor {
    constructor(...args) {
        if (args.length === 1 && typeof args[0] === 'string' || args[0] instanceof String) {
            this.list = document.querySelector(args[0]).appendChild(document.createElement("TABLE"));
        } else if (args.length === 1 && args[0] instanceof HTMLElement) {
            this.list = args[0].appendChild(document.createElement("TABLE"));
        } else {
            this.list = document.body.appendChild(document.createElement("TABLE"));
        }
        this.list.border = "1";
        this.list.style.cssText = "width: 100%;text-align: center;";
        this.list.addEventListener("click", function (e) {
            if (e.target.getAttribute("data-editid")) {
                this.OnEdit(e.target.getAttribute("data-editid"));
            }
        });
        this.list.THead = this.list.createTHead().insertRow(-1);

    }
    AddColumn(...args) {
        if (args.length == 1 && Array.isArray(args[0])) {
            for (var i in args[0]) {
                this.list.THead.insertCell(-1).innerHTML = args[0][i];
            }
        } else {
            for (var i in args) {
                this.list.THead.insertCell(-1).innerHTML = args[i];
            }
        }

    }
    AddRow(...args) {
         var row= this.list.insertRow(-1);
        if (args.length == 1 && Array.isArray(args[0])) {
           
            for (var i in args[0]) {
               row.insertCell(-1).innerHTML = args[0][i];
            }
        } else {
            for (var i in args) {
              row.insertCell(-1).innerHTML = args[i];
            }
        }

    }
}

