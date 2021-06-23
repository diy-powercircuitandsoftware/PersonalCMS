class SuperDialog {
    Dialog() {
        var dialog = document.body.appendChild(document.createElement("DIALOG"));
        var title = dialog.appendChild(document.createElement("form"));
        var content = dialog.appendChild(document.createElement("div"));
        var button = dialog.appendChild(document.createElement("div"));
        dialog.CallBack = function () {};
        dialog.style.cssText = "padding: 0;";
        button.style.cssText = "text-align:center;";
        dialog.AddButton = function (v, txt) {
            var bn = button.appendChild(document.createElement("button"));
            bn.ref = this;
            bn.v = v;
            bn.innerHTML = txt;
            bn.addEventListener("click", function (e) {
                var cbrs = this.ref.CallBack(this.v);
                if (cbrs === true || cbrs === undefined) {
                    dialog.close();
                }
            });
            return bn;
        };
        dialog.AddContent = function (c) {
            if (typeof c === "string") {
                content.insertAdjacentHTML('beforeend', c);
                return content.lastChild;
            } else if (c instanceof HTMLElement) {
                return  content.appendChild(c);
            }
        };
        dialog.Title = function (t, closeable) {
            if (closeable || (closeable === undefined)) {
                title.style.cssText = "display:flex;border-style: solid;border-width: thin;";
                title.setAttribute("method", "dialog");
                title.innerHTML = '<span style="font-weight: bold;flex-grow: 1;">' + t + '</span><button>X</button>';
            } else if (!closeable) {
                title.innerHTML = '<span style="font-weight: bold;">' + t + '</span>';
            }
            return this;
        };

        dialog.DestroyAfterClose = function () {
            this.addEventListener('close', function () {
                this.parentNode.removeChild(this);
            });
        };

        dialog.showModal();
        return dialog;
    }

    Import(querystring) {
        var dialog = this.Dialog();
        var q = document.querySelector(querystring);
        q.style.display = "";
        dialog.AddContent(q);
        return dialog;
    }
    ImportOkCancel(querystring, callback) {
        var dialog = this.Import(querystring);
        dialog.AddButton(1, "OK");
        dialog.AddButton(0, "Cancel");
        dialog.CallBack = (function (v) {
            if (v === "true" || v === "1" || v === 1 || v) {
                callback();
            }
        });
        return dialog;
    }

    TwoRow(callback) {

        var dialog = this.Dialog();
        dialog.error = dialog.AddContent(document.createElement('div'));
        dialog.table = dialog.AddContent(document.createElement('table'));
        dialog.CallBack = (function (v) {
            if (v === "true" || v === "1" || v === 1 || v) {
                var output = {};
                [].forEach.call(dialog.querySelectorAll("input[name],textarea"), function (dom) {
                    if (dom.checked) {
                        output[dom.name] = dom.value;
                    } else if (["radio", "checkbox"].indexOf(dom.type) == -1) {
                        output[dom.name] = dom.value;
                    }
                });
                [].forEach.call(dialog.querySelectorAll("input[type='file']"), function (dom) {
                    output[dom.name] = dom.files;
                });
                if (arguments.length > 0) {
                    return callback(output);
                }

            }
        });
        if (arguments.length > 0) {
            dialog.AddButton(1, "OK");
            dialog.AddButton(0, "Cancel");
        }
        dialog.AddRow = function (...args) {
            var row = this.table.insertRow(-1);
            if (args.length == 1) {
                var cell = row.insertCell(-1);
                cell.colSpan = 2;
                cell.innerHTML = args[0];
            } else if (args.length == 2) {
                row.insertCell(-1).innerHTML = args[0];
                row.insertCell(-1).innerHTML = args[1];
            }
            return this;
        };
        dialog.Error = function (txt) {
            this.error.innerHTML = txt;
        };
        dialog.Reset = function () {
            [].forEach.call(this.table.querySelectorAll("input,textarea"), function (dom) {
                dom.value = "";
            });
        };
        dialog.DestroyAfterClose();
        return dialog;

    }

}

 