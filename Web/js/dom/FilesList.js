class FilesList {
    constructor(...args) {
        this.iconext = [];
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
                "<th>FileName</th>" +
                "<th>Size</th>" +
                "<th>Modified</th>" +
                "<th>Manage</th></tr>";
        this.list.addEventListener("click", function (e) {
            var selcetdom = e.target.getAttribute("data-domfileslist");
            if (selcetdom == "SelectAll") {
                var sett = e.target.checked && this.fn.multiple;
                [].forEach.call(this.querySelectorAll('[data-domfileslist="Select"]'), function (chk) {
                    chk.checked = sett;
                });
            } else if (selcetdom == "Select" && !this.fn.multiple) {
                [].forEach.call(this.querySelectorAll('[data-domfileslist="Select"]'), function (chk) {
                    chk.checked = false;
                });
                e.target.checked = true;
            } else if (selcetdom == "Open") {
                var parrent = e.target.parentNode.parentNode;
                if (parrent.getAttribute("data-type") == "DIR") {
                    this.fn.OpenDir(parrent.getAttribute("data-path"));
                } else if (parrent.getAttribute("data-type") == "File") {
                    this.fn.OpenFile(parrent.getAttribute("data-path"));
                }
            }else if (selcetdom == "Open") {
                
            }
        });
        this.list.fn = this;
    }
    AddFile(name, path, size, date) {
        var icon = "";
        var lastrow = this.list.insertRow(-1);
        lastrow.setAttribute("data-path", path);
        lastrow.setAttribute("data-type", "File");


        lastrow.innerHTML = this.StringFormat('<td><input data-domfileslist="Select" type="checkbox" /></td><td>%s %s</td><td>%s</td><td>%s</td><td><a data-domfileslist="Open" href="#">Open</a>&nbsp;<a data-domfileslist="Rename" href="#">Rename</a>&nbsp;<a data-domfileslist="Delete" href="#" >Delete</a>&nbsp;<a data-domfileslist="Properties" href="#">Properties</a>&nbsp;<a data-domfileslist="Download" href="">Download</a></td>', icon, name, size, date);

    }
    AddDir(name, path, date) {
        var icon = '<img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAABwklEQVQ4T6VTPWtUURA9Z+59b4O7GgJBoyCYRgQr+6ASVNAi/0SFgK2lva0/wkawsZU0pkglmBgFRd24fmTX1XX3vZmRe3cFYzQsZB6PW9yZM+fMmUscMjht/dbjrUZ3rnseFTAajjwH/CPX19bPlTPlDZISJBAOgWTYlGLufsxqu2bqFwQsTQ2qhnTWVr3j9tqjJ0JckSgQCRAS6TM4PCW7YSALUGkAhlyoqtBaYWYddh5crwI0hhhB2avIMw+AM018b5xAT05BvUzcYObol2e+cuf+1Z+NaI1QFKCMue8Nh5rDzTIYKJmFa422LA75/t7yt0asW0WZAAI4IZG7TxiACQD5jiFAa0M1HKHNkyO+vntpt8nBbCyKPAP85YsnCX9SihHmCh1W2OHxii/vLHWa2puPMQHIPoB9imKR5WhV4ZPM1Xy+uvShOfy8kAE4BUAIyR94XeMLZ5Ubty++af5onw4hghTw9xD+s2GecpBcUPTZUj67efnV0d7bRQlJf3Lh4OUcW512RDEIM8aNW8svWrubZ4lx9/H/7/ZpoOnSk61wKEvn09WVh/PdzRWfVE31ODxzgBZH+lPlH/TgDg3wCxuW05idQ2qnAAAAAElFTkSuQmCC"/>';
        var lastrow = this.list.insertRow(-1);
        lastrow.setAttribute("data-path", path);
        lastrow.setAttribute("data-type", "DIR");
        lastrow.innerHTML = this.StringFormat('<td><input data-domfileslist="Select" type="checkbox" /></td><td>%s %s</td><td>-</td><td>%s</td><td><a data-domfileslist="Open" href="#">Open</a>&nbsp;<a data-domfileslist="Rename" href="#">Rename</a>&nbsp;<a data-domfileslist="Delete" href="#" >Delete</a>&nbsp;<a data-domfileslist="Properties" href="#">Properties</a>&nbsp;<a data-domfileslist="Download" href="">Download</a></td>', icon, name, date);
    }
    AddIconFiles(ext, path) {
        this.iconext[ext] = path;
    }
    AddPreviewImage(path) {
        this.preview = path;
    }
    Clear() {
        while (this.list.rows.length > 1) {
            this.list.deleteRow(this.list.rows.length - 1);
        }
    }
    ClearSelectList() {
        [].forEach.call(this.list.querySelectorAll('[data-domfileslist="Select"]'), function (chk) {
            chk.checked = false;
        });
    }
    Multiple(bool) {
        this.multiple = bool;
    }
    OpenDir(v) {
        if (typeof v === "function") {
            this.OpenDir = v;
        } else if (typeof v === 'string' || v instanceof String) {
            this.OpenDir(v);
        }
    }
    OpenFile(v) {
        if (typeof v === "function") {
            this.OpenFile = v;
        } else if (typeof v === 'string' || v instanceof String) {
            this.OpenFile(v);
        }
    }
    Rename(v) {
        if (typeof v === "function") {
            this.Rename = v;
        } else if (typeof v === 'string' || v instanceof String) {
            this.Rename(v);
        }
    }
    StringFormat(...args) {
        var str = args[0];
        for (var i = 1; i < args.length; i++) {
            str = str.replace("%s", args[i]);
        }
        return str;
    }

}




/*
 
 function FilesList(Editable) {
 
 Method.addEventListener("click", function (e) {
 if (e.target.getAttribute("class") == "FL-SelectAll" && this.Mutilselect) {
 var fs = this.getElementsByClassName("FL-FileSelect");
 for (var i = 0; i < fs.length; i++) {
 fs[i].checked = e.target.checked;
 }
 } else if (e 
 } else if (e.target.getAttribute("class") == "FL-Rename") {
 this.RenameFile(e.target.parentNode.parentNode.getAttribute("data-path"));
 this.Name = e.target.parentNode.parentNode.getAttribute("data-name");
 } else if (e.target.getAttribute("class") == "FL-Delete") {
 this.Delete(e.target.parentNode.parentNode.getAttribute("data-path"));
 this.Name = e.target.parentNode.parentNode.getAttribute("data-name");
 } else if (e.target.getAttribute("class") == "FL-Properties") {
 this.PropertiesFile(e.target.parentNode.parentNode.getAttribute("data-path"));
 this.Name = e.target.parentNode.parentNode.getAttribute("data-name");
 } else if (e.target.getAttribute("class") == "FL-FileSelect" && !this.Mutilselect) {
 var pointer = e.target;
 var FileSelect = this.getElementsByClassName("FL-FileSelect");
 for (var i = 0; i < FileSelect.length; i++) {
 if (FileSelect[i] !== pointer) {
 FileSelect[i].checked = false;
 }
 }
 } else if (e.target.getAttribute("class") == "FL-Download") {
 if (this.BeforeDownload(e.target.parentNode.parentNode.getAttribute("data-path")) == false) {
 e.preventDefault();
 return false;
 }
 
 }
 }); 
 
 Method.ClearSelectList = function () {
 var FileSelect = this.getElementsByClassName("FL-FileSelect");
 for (var i = 0; i < FileSelect.length; i++) {
 FileSelect[i].checked = false;
 }
 this.getElementsByClassName("FL-SelectAll")[0].checked = false;
 };
 Method.Delete = function (cb) {
 
 };
 Method.GetSelectFiles = function (index) {
 var files = [];
 var FileSelect = this.getElementsByClassName("FL-FileSelect");
 for (var i = 0; i < FileSelect.length; i++) {
 var name = FileSelect[i].parentNode.parentNode.getAttribute("data-name");
 if (FileSelect[i].checked == true && name != "." && name !== "..") {
 files.push(FileSelect[i].parentNode.parentNode.getAttribute("data-path"));
 }
 }
 if (arguments.length == 0) {
 return files;
 }
 if (index < files.length) {
 return files[index];
 }
 return "";
 };
 
 Method.OpenFile = function (cb) {
 
 };
 Method.RenameFile = function (cb) {
 
 };
 Method.PropertiesFile = function (cb) {
 
 };
 
 
 return Method;
 
 }
 
 
 
 
 
 
 */