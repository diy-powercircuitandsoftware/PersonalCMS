class WYSIWYG {
    constructor(...args) {
        if (args.length === 1 && typeof args[0] === 'string' || args[0] instanceof String) {
            this.editor = document.querySelector(args[0]).appendChild(document.createElement("iframe"));
        } else if (args.length === 1 && args[0] instanceof HTMLElement) {
            this.editor = args[0].appendChild(document.createElement("iframe"));
        } else {
            this.editor = document.body.appendChild(document.createElement("iframe"));
        }
       
    }
    BackgroundColor() {

    }
    DesignMode(bool) {
        var doc = this.editor.contentWindow.document;
        doc.open();
        doc.close();

        if (bool) {
            this.editor.contentDocument.designMode = "on";
        } else {
            this.editor.contentDocument.designMode = "off";
        }
         doc.head.innerHTML='<meta charset="utf-8"/>';
    }
    Html(...args) {
        if (args.length === 0) {
            return  this.editor.contentWindow.document.body.innerHTML;
        } else {
            this.editor.contentWindow.document.body.innerHTML = args[0];
        }
    }
    Size(w, h) {
        this.editor.style.width = w;
        this.editor.style.height = h;
    }

}



/*
 
 
 
 
 function WYSIWYG() {
 var Method = document.createElement("");
 Method.EditorCSS = function (cssstr) {
 this.style.cssText = cssstr;
 };
 
 Method.BackgroundColor = function (args) {
 var body = this.contentWindow.document.body;
 if (body.childElementCount == 1) {
 if (arguments.length == 0) {
 
 } else {
 if (body.firstElementChild.tagName == "DIV") {
 body.firstElementChild.style.backgroundColor = args;
 } else {
 var innerhtml = body.innerHTML;
 body.innerHTML = "";
 var nd = body.appendChild(this.contentWindow.document.createElement("DIV"));
 nd.innerHTML = innerhtml;
 nd.style.backgroundColor = args;
 }
 }
 } else {
 if (arguments.length == 0) {
 
 } else {
 var innerhtml = body.innerHTML;
 body.innerHTML = "";
 var nd = body.appendChild(this.contentWindow.document.createElement("DIV"));
 nd.innerHTML = innerhtml;
 nd.style.backgroundColor = args;
 }
 }
 
 };
 
 
 Method.EXECommand = function (cmd) {
 var CommandList = ["bold", "copy", "cut", "decreaseFontSize",
 "insertHorizontalRule", "increaseFontSize", "indent", "italic",
 "justifyLeft", "justifyCenter", "justifyRight", "justifyFull",
 "insertOrderedList", "outdent", "insertParagraph", "paste",
 "redo", "removeFormat", "unlink", "strikeThrough", "subscript", "superscript", "underline", "undo", "insertUnorderedList"];
 if (CommandList.indexOf(cmd) >= 0) {
 this.contentWindow.document.execCommand(cmd, false, false);
 }
 
 };
 Method.EXECommandState = function (cmd) {
 var CommandList = ["bold", "copy", "cut", "decreaseFontSize",
 "insertHorizontalRule", "increaseFontSize", "indent", "italic",
 "justifyLeft", "justifyCenter", "justifyRight", "justifyFull",
 "insertOrderedList", "outdent", "insertParagraph", "paste",
 "redo", "removeFormat", "unlink", "strikeThrough", "subscript", "superscript", "underline", "undo", "insertUnorderedList"];
 if (CommandList.indexOf(cmd) >= 0) {
 return this.contentWindow.document.queryCommandState(cmd);
 }
 
 };
 Method.GetTextCount = function ( ) {
 return  this.contentWindow.document.body.textContent.length;
 };
 Method.GetValue = function (cmd) {
 if (["backColor", "foreColor"].indexOf(cmd) >= 0) {
 var col = this.contentWindow.document.queryCommandValue(cmd);
 return   Method.RGB2Hex(col);
 } else if (cmd == "hiliteColor") {
 var sel = this.contentWindow.document.getSelection();
 if (sel.rangeCount > 0) {
 var dom = sel.getRangeAt(0).startContainer.parentNode;
 if (dom.style !== undefined) {
 return   Method.RGB2Hex(dom.style.backgroundColor);
 }
 }
 } else if (["fontName", "fontSize"].indexOf(cmd) >= 0) {
 return this.contentWindow.document.queryCommandValue(cmd);
 }
 };
 
 Method.GetSelectTable = function () {
 var selection = this.contentWindow.document.getSelection();
 if (selection.rangeCount > 0) {
 var pn = selection.getRangeAt(0).startContainer.parentNode;
 while (pn !== null && pn.tagName !== "BODY") {
 if (pn.tagName == "TABLE") {
 return pn;
 }
 
 pn = pn.parentNode;
 }
 }
 return null;
 };
 Method.GetSelectTableCell = function () {
 var selection = this.contentWindow.document.getSelection();
 if (selection.rangeCount > 0) {
 var pn = selection.getRangeAt(0).startContainer.parentNode;
 while (pn !== null && pn.tagName !== "BODY") {
 if (pn.tagName == "TH" || pn.tagName == "TD") {
 return pn;
 }
 pn = pn.parentNode;
 }
 }
 return null;
 };
 Method.GetSelectTableRow = function () {
 var selection = this.contentWindow.document.getSelection();
 if (selection.rangeCount > 0) {
 var pn = selection.getRangeAt(0).startContainer.parentNode;
 while (pn !== null && pn.tagName !== "BODY") {
 if (pn.tagName == "TR") {
 return pn;
 }
 pn = pn.parentNode;
 }
 }
 return null;
 };
 Method.Html = function (htmlcode) {
 if (arguments.length == 0) {
 return  this.contentWindow.document.body.innerHTML;
 } else {
 this.contentWindow.document.body.innerHTML = htmlcode;
 }
 };
 Method.InsertCommand = function (cmd, value) {
 var CommandList = ["insertHTML", "insertImage", "insertText"];
 if (CommandList.indexOf(cmd) >= 0) {
 this.contentWindow.document.execCommand(cmd, false, value);
 } else if (cmd == "createlink") {
 var sel = this.contentWindow.document.getSelection().toString();
 if (sel.length > 0) {
 this.contentWindow.document.execCommand("createlink", false, value);
 } else {
 var a = this.contentWindow.document.createElement("A");
 var sel = this.contentWindow.document.getSelection();
 if (sel.getRangeAt && sel.rangeCount) {
 var range = sel.getRangeAt(0);
 range.collapse(false);
 range.insertNode(a);
 } else {
 this.contentWindow.document.body.appendChild(a);
 }
 a.innerHTML = value;
 a.href = value;
 }
 }
 };
 Method.InsertDomAtSelection = function (dom) {
 var sel = this.contentWindow.document.getSelection();
 if (sel.getRangeAt && sel.rangeCount) {
 var range = sel.getRangeAt(0);
 range.collapse(false);
 range.insertNode(dom);
 } else {
 this.contentWindow.document.body.appendChild(dom);
 }
 return dom;
 }
 Method.KeyUp = function (callback) {
 if (typeof callback === "function") {
 this.contentWindow.document.onkeyup = callback;
 }
 };
 Method.Load = function (callback) {
 this.onload = function () {
 callback();
 };
 };
 Method.MouseDown = function (callback) {
 if (typeof callback === "function") {
 this.contentWindow.document.onmousedown = callback;
 }
 };
 Method.RGB2Hex = function (rgb) {
 rgb = rgb.match(/^rgba?[\s+]?\([\s+]?(\d+)[\s+]?,[\s+]?(\d+)[\s+]?,[\s+]?(\d+)[\s+]?/i);
 return (rgb && rgb.length === 4) ? "#" +
 ("0" + parseInt(rgb[1], 10).toString(16)).slice(-2) +
 ("0" + parseInt(rgb[2], 10).toString(16)).slice(-2) +
 ("0" + parseInt(rgb[3], 10).toString(16)).slice(-2) : '';
 };
 Method.SetValue = function (cmd, value) {
 var CommandList = ["backColor", "foreColor", "fontName", "fontSize", "hiliteColor"];
 if (CommandList.indexOf(cmd) >= 0) {
 this.contentWindow.document.execCommand(cmd, false, value);
 }
 };
 
 Method.TableCommand = function (cmd, args1, args2) {
 if (cmd == "BorderStyle") {
 var selection = this.GetSelectTable();
 if (selection !== null) {
 if (args1 === undefined) {
 } else {
 selection.style.borderStyle = args1;
 if (args1 == "none") {
 selection.style.borderStyle = "solid";
 }
 
 [].forEach.call(selection.querySelectorAll("TD,TH"), function (tdth) {
 tdth.style.borderStyle = args1;
 });
 }
 }
 return false;
 } else if (cmd == "DeleteColumn") {
 var selection = this.GetSelectTableCell();
 if (selection !== null) {
 var ci = selection.cellIndex;
 [].forEach.call(selection.parentNode.parentNode.querySelectorAll("TR"), function (tr) {
 tr.deleteCell(ci);
 });
 }
 } else if (cmd == "DeleteRow") {
 var selection = this.GetSelectTableRow();
 if (selection !== null) {
 this.GetSelectTable().deleteRow(selection.rowIndex);
 }
 } else if (cmd == "InsertColumn") {
 var pointer = this;
 var selection = this.GetSelectTableCell();
 if (selection !== null) {
 var ci = selection.cellIndex;
 [].forEach.call(selection.parentNode.parentNode.querySelectorAll("TR"), function (tr) {
 var cell = tr.insertCell(ci + 1);
 var tempdiv = cell.appendChild(pointer.contentWindow.document.createElement("DIV"));
 tempdiv.style.minWidth = "20px";
 tempdiv.style.minHeight = "10px";
 tempdiv.style.resize = "both";
 cell.style.cssText = selection.style.cssText;
 });
 }
 } else if (cmd == "InsertTable") {
 var row = args1;
 var col = args2;
 var table = this.contentWindow.document.createElement("table");
 table.border = 1;
 for (var r = 1; r <= row; r++) {
 var irow = table.insertRow(-1);
 for (var c = 1; c <= col; c++) {
 var cell = irow.insertCell(-1);
 var tempdiv = cell.appendChild(this.contentWindow.document.createElement("DIV"));
 tempdiv.style.minWidth = "20px";
 tempdiv.style.minHeight = "10px";
 tempdiv.style.resize = "both";
 }
 }
 return this.InsertDomAtSelection(table);
 } else if (cmd == "InsertRow") {
 var selection = this.GetSelectTableRow();
 if (selection !== null) {
 var rindex = selection.rowIndex;
 var countc = selection.cells.length;
 var nr = selection.parentNode.insertRow(rindex + 1);
 for (var i = 0; i < countc; i++) {
 var cell = nr.insertCell(-1);
 var tempdiv = cell.appendChild(this.contentWindow.document.createElement("DIV"));
 tempdiv.style.minWidth = "20px";
 tempdiv.style.minHeight = "10px";
 tempdiv.style.resize = "both";
 cell.style.cssText = selection.cells[0].style.cssText;
 }
 }
 }
 
 };
 
 
 
 Method.test = function () {
 var selection = this.contentWindow.document.getSelection();
 
 if (selection.rangeCount > 0) {
 var x = selection.getRangeAt(0);
 alert(x.rowIndex);
 }
 
 
 };
 
 return Method;
 }
 */