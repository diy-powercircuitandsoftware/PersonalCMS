class SuperDialog_Template_Business extends SuperDialog {
    Advertisement(txt, time) {
        if (!Number.isInteger(time)) {
            time = 0;
        }
        var dialog = this.Dialog();
        var qs = dialog.AddButton("Close", "Close");
        qs.setAttribute("disabled", "disabled");
        dialog.AddContent(txt);
        dialog.DestroyAfterClose();
        var t = setInterval(function () {
            qs.innerHTML = "Close(" + time + ")";
            time = time - 1;
            if (time < 0) {
                clearInterval(t);
                qs.removeAttribute("disabled");
            }
        }, 1000);

        return dialog;
    }
    License(txt) {
        var dialog = this.Dialog();
        dialog.AddContent(txt);
        dialog.AddButton(1, "Accept");
        return dialog;
    }
    Payment(callback) {
        var dialog = this.TwoRow(function (v) {
            return callback(v);
        });
        dialog.Title("Payment");
        dialog.AddRow("CARD NUMBER", '<input placeholder="0000 0000 0000 0000" type="text"  style="width:99%;box-sizing: border-box;" name="number" />');
        dialog.AddRow("CARD HOLDER", '<input placeholder="name" type="text"  style="width:99%;box-sizing: border-box;" name="name" />');
        dialog.AddRow("EXPIRES", '<input placeholder="MM/YY" type="text"  style="width:99%;box-sizing: border-box;" name="exp" />');
        dialog.AddRow("CVV", '<input type="text"  style="width:99%;box-sizing: border-box;" value="true" name="cvv" />');
        return dialog;

    }
}
;


