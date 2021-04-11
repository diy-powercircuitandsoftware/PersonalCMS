class  SQLite_Table_Editor {
    SQLiteTypes = ["BIGINT", "BLOB", "BOOLEAN", "CHAR", "DATE", "DATETIME", "DECIMAL", "DOUBLE", "INTEGER", "INT", "NONE", "NUMERIC", "REAL", "STRING", "TEXT", "TIME", "VARCHAR"];
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
        this.list.innerHTML = "<tr>" +
                "<th><input data-domfileslist='SelectAll' type = 'checkbox'/> Select All</th>" +
                "<th>Column</th>" +
                "<th>Field</th>" +
                "<th>Type</th>" +
                " <th>Primary Key</th>" +
                " <th>Auto Increment</th>" +
                " <th>Not NULL</th>" +
                " <th>Default Value</th>" +
                "<th>Edit</th></tr>";
        this.list.addEventListener("click", function (e) {
            if(e.target.getAttribute("data-editid")){
                this.OnEdit(e.target.getAttribute("data-editid"));
            }
        });
    }
    AddForEdit(Column, Field, Type, Primary, Autoincrement, NULL, Default) {
        var row = this.list.insertRow(-1);
        row.insertCell(-1).innerHTML = '<input data-domtype="select" type="checkbox" value="' + Column + '" />';
        row.insertCell(-1).innerHTML = Column;
        row.insertCell(-1).innerHTML = Field;
        row.insertCell(-1).innerHTML = Type;
        row.insertCell(-1).innerHTML = Primary;
        row.insertCell(-1).innerHTML = Autoincrement;
        row.insertCell(-1).innerHTML = NULL;
        row.insertCell(-1).innerHTML = Default;
        row.insertCell(-1).innerHTML = "<button data-editid='" + Column + "'>Edit</button>";
        row.setAttribute("data-mode", "edit");
    }
    AddNew( ) {
        var row = this.list.insertRow(-1);
        var typeselect = document.createElement("SELECT");
        typeselect.style.cssText = "width: 100%;box-sizing: border-box;";

        for (var i in this.SQLiteTypes) {
            var opt = typeselect.appendChild(document.createElement("OPTION"));
            opt.value = this.SQLiteTypes[i];
            opt.innerHTML = this.SQLiteTypes[i];
        }
        row.insertCell(-1).innerHTML = '-';
        row.insertCell(-1).innerHTML = "-";
        row.insertCell(-1).innerHTML = '<input type="text" name="" data-type="Name" />';
        row.insertCell(-1).innerHTML = '<input type="checkbox" data-type="PrimaryKey"  />';
        row.insertCell(-1).innerHTML = '<input type="checkbox" data-type="AutoIncrement"  />';
        row.insertCell(-1).innerHTML = '<input type="checkbox" data-type="NotNULL"  />';
        row.insertCell(-1).innerHTML = '<input type="text" data-type="DefaultValue"  />';
        row.insertCell(-1).innerHTML = "-";
        var cell3 = row.insertCell(3);
        cell3.appendChild(typeselect);
        typeselect.setAttribute("data-type", "Type");
        cell3.insertAdjacentHTML("beforeend", '<input data-type="TypeLength" min=0 style="display:block;width: 100%;box-sizing: border-box;" type="number" name="" value="" />');
        row.setAttribute("data-mode", "new");
    }
    GetAddNewToJSON() {
        var out = [];
        [].forEach.call(this.list.querySelectorAll("[data-mode='new']"), function (datanew) {
            var arrvalue = {};
            [].forEach.call(datanew.querySelectorAll("[data-type]"), function (datatype) {
                if (datatype.type == "checkbox") {
                    arrvalue[datatype.getAttribute("data-type")] = datatype.checked;
                } else {
                    arrvalue[datatype.getAttribute("data-type")] = datatype.value;
                }
            });
            arrvalue["Name"] = arrvalue["Name"].trim();
            if (arrvalue["Name"].length > 0) {
                out.push(arrvalue);
            }
        });
        return out;
    }
    GetCreateTableString(name) {
        var json = this.GetAddNewToJSON();
        var arrmix = [];
        var out = "";
        out = out + "CREATE TABLE " + name + " (";
        for (var k in json) {
            if (json[k].PrimaryKey && !json[k].AutoIncrement) {
                arrmix.push(json[k].Name + " " + json[k].Type + " PRIMARY KEY");
            } else if (json[k].PrimaryKey && !json[k].AutoIncrement) {
                arrmix.push(json[k].Name + " " + json[k].Type + " PRIMARY KEY AUTOINCREMENT");
            } else {
                arrmix.push(json[k].Name + " " + json[k].Type);
            }

        }
        out = out + arrmix.join(",");
        out = out + ");";
        return out;
    }
    OnEdit(cb){
        this.list.OnEdit=cb;
    }

}


