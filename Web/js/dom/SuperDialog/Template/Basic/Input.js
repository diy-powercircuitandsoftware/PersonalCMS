class SuperDialog_Template_Input extends SuperDialog {
    DropDown(callback) {
        var dialog = this.Dialog();
        dialog.AddButton(1, "OK");
        dialog.AddButton(0, "Cancel");
        dialog.AddContent('<select style="width:100%;box-sizing: border-box;"></select>');
        dialog.Title("DropDown");
        dialog.DestroyAfterClose();
        dialog.CallBack = (function (v) {
            if (v === "true" || v === "1" || v === 1 || v) {
                 callback(dialog.querySelector('select').value);
            }
        });
        dialog.Add = function (...args) {
            var opt = dialog.querySelector('select').appendChild(document.createElement('option'));
            if (args.length === 1) {

            } else if (args.length === 2) {
                opt.value = args[0];
                opt.innerHTML = args[1];
            }
            return this;
        };
        dialog.CopyOption = function (querystring) {
            var select = dialog.querySelector('select');
            select.innerHTML = "";
            [].forEach.call(document.querySelectorAll(querystring), function (dom) {
                if (dom.tagName.toLowerCase() === "select" || dom.tagName.toLowerCase() === "datalist") {
                    select.innerHTML = select.innerHTML + dom.innerHTML;
                }
            });
              return this;
        };
        return dialog;
    }

    Mutilline(callback) {
        var dialog = this.Dialog();
        dialog.Title("Mutilline");
        dialog.AddButton(1, "OK");
        dialog.AddButton(0, "Cancel");
        dialog.txtarea = dialog.AddContent('<textarea style="width: 98%;height: 100%;resize: none;"></textarea>');
        dialog.DestroyAfterClose();
        dialog.CallBack = (function (v) {
            if (v === "true" || v === "1" || v === 1 || v) {
                callback(this.txtarea.value);
            }
        });
        return dialog;
    }
    Prompt(title, callback) {
        var dialog = this.Dialog();
        dialog.Title(title);
        dialog.AddButton(1, "OK");
        dialog.AddButton(0, "Cancel");
        dialog.txtbox = dialog.AddContent("<input type='text'  style='width:100%;box-sizing: border-box;' />");
        dialog.DestroyAfterClose();
        dialog.CallBack = (function (v) {
            if (v === "true" || v === "1" || v === 1 || v) {
                callback(this.txtbox.value);
            }
        });
        return dialog;
    }
    Quiz(question, callback) {
        var dialog = this.TwoRow(function (v) {
            return callback(v);
        });
        dialog.AddRadioButton = function (name, txt, value) {
            this.AddRow('<input type="radio" name="' + name + '"  style="box-sizing: border-box;" value="' + value + '" />' + txt);
            return this;
        };
        dialog.AddSelectOption = function (name, txt, value) {
            var option = "";
            for (var k in value) {
                var opt = document.createElement("OPTION");
                opt.value = k
                opt.innerHTML = value[k];
                option = option + opt.outerHTML;
            }
            this.AddRow(txt + '<select name="' + name + '">' + option + '</select>');
            return this;
        };
        dialog.Title(question);
        return dialog;
    }
    Rect(callback) {
        var dialog = this.TwoRow(function (v) {
            return callback(v);
        });
        dialog.Title("Rect");
        dialog.AddRow("x:", "<input type='number'  style='width:100%;box-sizing: border-box;' name='x' />");
        dialog.AddRow("y:", "<input type='number'  style='width:100%;box-sizing: border-box;' name='y' />");
        dialog.AddRow("width:", "<input type='number'  style='width:100%;box-sizing: border-box;' name='width' />");
        dialog.AddRow("height:", "<input type='number'  style='width:100%;box-sizing: border-box;' name='height' />");
        return dialog;

    }
    RowCol(callback) {
        var dialog = this.TwoRow(function (v) {
            return callback(v);
        });
        dialog.Title("Row/Column");
        dialog.AddRow("Row:", "<input type='number'  style='width:100%;box-sizing: border-box;' name='row' />");
        dialog.AddRow("Column:", "<input type='number'  style='width:100%;box-sizing: border-box;' name='column' />");
        return dialog;

    }
    Size(callback) {
        var dialog = this.TwoRow(function (v) {
            return callback(v);
        });
        dialog.Title("Size");
        dialog.AddRow("width:", "<input type='number'  style='width:100%;box-sizing: border-box;' name='width' />");
        dialog.AddRow("height:", "<input type='number'  style='width:100%;box-sizing: border-box;' name='height' />");
        return dialog;
    }
    UnLock(callback) {
        var dialog = this.Dialog();
        dialog.Title("Unlock");
        dialog.AddButton(1, "OK");
        dialog.AddButton(0, "Cancel");
        dialog.txtbox = dialog.AddContent("<input type='password'  style='width:100%;box-sizing: border-box;' />");
        dialog.DestroyAfterClose();
        dialog.CallBack = (function (v) {
            if (v === "true" || v === "1" || v === 1 || v) {
                callback(this.txtbox.value);
            }
        });
        return dialog;
    }
}
;

