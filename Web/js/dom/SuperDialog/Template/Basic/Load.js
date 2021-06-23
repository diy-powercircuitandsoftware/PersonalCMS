class SuperDialog_Template_Load extends SuperDialog {
    Load(...args) {
        var dialog = this.Dialog();


        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function () {
            if (this.readyState === 4 && this.status === 200) {

                dialog.AddContent(xhttp.responseText);
            }
        };
        xhttp.sd = this;

        if (args.length === 1) {
            xhttp.open("GET", args[0], true);
            xhttp.send();
        } else if (args.length === 2) {
            var json = args[1];
            xhttp.open("GET", args[0] + '?' +
                    Object.keys(json).map(function (key) {
                return (key) + '=' +
                        (json[key]);
            }).join('&'), true);
            xhttp.send( );
        }
        return dialog;
    }
    Loading() {
        var dialog = this.Dialog();
        dialog.ref = this;
        dialog.pg = dialog.AddContent(document.createElement("progress"));
        dialog.divlog = dialog.AddContent(document.createElement("div"));
        dialog.pg.setAttribute("max", 1);
        dialog.pg.setAttribute("value", 0);
        dialog.Title("Loading", false);
        dialog.AddButton(0, "Cancel");
        dialog.DestroyAfterClose();
        dialog.Cancel = function () {

        };
        dialog.CallBack = function (v) {
            if (v == "0") {
                var ref = this;
                var subdialog = this.Dialog();
                subdialog.AddButton(1, "OK");
                subdialog.AddButton(0, "Cancel");
                subdialog.AddContent(txt);
                subdialog.Title("Confirm");
                subdialog.DestroyAfterClose();
                subdialog.CallBack = (function (v) {
                    if (v === "true" || v === "1" || v === 1 || v) {
                        ref.Cancel();
                        ref.close();
                    }
                });

            }
            return false;
        };
        dialog.Val = function (v) {
            this.pg.value = v;
            return this;
        };
        dialog.Log = function (v) {
            this.divlog.innerHTML = v;
            return this;
        };
        return dialog;
    }

    PleaseWait() {
        var dialog = this.Dialog();
        dialog.AddContent("<div style='cursor:wait;'>Please Wait</div>");
        dialog.DestroyAfterClose();
        return dialog;
    }
}
;