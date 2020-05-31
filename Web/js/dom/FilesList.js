class FilesList {
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
                "<th><input data-dom='FL-SelectAll' type = 'checkbox'/> Select All</th>" +
                "<th>FileName</th>" +
                "<th>Size</th>" +
                "<th>Modified</th>" +
                "<th>Manage</th></tr>";
    }
    AddFile(name, path, icon, size, date) {

    }
    AddDir(name, path, icon, date) {

    }
    Clear() {
        while (this.list.rows.length > 1) {
            this.deleteRow(this.rows.length - 1);
        }
    }
    Multiple(bool) {
        if (bool) {

        } else {

        }
    }
    OpenDir(v) {
        if (typeof v === "function") {
            this.OpenDir = v;
        } else if (typeof v === 'string' || v instanceof String) {
            this.OpenDir(v);
        }
    }
    OpenFile() {

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
 } else if (e.target.getAttribute("class") == "FL-Open" && e.target.parentNode.parentNode.getAttribute("data-filetype") == "DIR") {
 this.ChDir(e.target.parentNode.parentNode.getAttribute("data-path"));
 this.Name = e.target.parentNode.parentNode.getAttribute("data-name");
 } else if (e.target.getAttribute("class") == "FL-Open" && e.target.parentNode.parentNode.getAttribute("data-filetype") == "FILE") {
 this.OpenFile(e.target.parentNode.parentNode.getAttribute("data-path"));
 this.Name = e.target.parentNode.parentNode.getAttribute("data-name");
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
 Method.AddFile = function (name, path, icon, size, date, type) {
 var lastrow = this.insertRow(-1);
 lastrow.setAttribute("data-filetype", type);
 lastrow.setAttribute("data-path", path);
 lastrow.setAttribute("data-name", name);
 lastrow.insertCell(-1).innerHTML = '<input  class="FL-FileSelect" type="checkbox" name=""   />';
 if (icon !== "") {
 lastrow.insertCell(-1).innerHTML = '<a class="FL-Open" href="#"><img style="max-width: 50px;max-height: 50px;" src = "' + icon + '" / >' + name + '</a>';
 } else {
 lastrow.insertCell(-1).innerHTML = '<a class="FL-Open" href="#">' + name + '</a>';
 }
 
 lastrow.insertCell(-1).innerHTML = size;
 lastrow.insertCell(-1).innerHTML = date;
 var Manage = lastrow.insertCell(-1);
 if (this.Editable) {
 Manage.insertAdjacentHTML('beforeend', '<a class="FL-Rename" href="#">Rename</a> &nbsp;');
 Manage.insertAdjacentHTML('beforeend', '<a class="FL-Delete" href="#" >Delete</a> &nbsp;');
 }
 Manage.insertAdjacentHTML('beforeend', '<br>');
 Manage.insertAdjacentHTML('beforeend', '<a class="FL-Open" href="#">Open</a> &nbsp;');
 Manage.insertAdjacentHTML('beforeend', '<a class="FL-Download" href="' + this.DownloadURL + btoa(path) + '">Download</a> &nbsp; ');
 Manage.insertAdjacentHTML('beforeend', '<a Class="FL-Properties" href="#">Properties</a> &nbsp; ');
 };
 
 Method.BeforeDownload = function (cb) {
 
 };
 
 
 
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