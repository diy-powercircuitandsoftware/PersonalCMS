class  SuperDialog_Template_MessageBox  extends SuperDialog {
    Alert(txt) {
        var dialog = this.Dialog();
        dialog.AddButton("OK", "OK");
        dialog.AddContent(txt);
        dialog.Title("Alert");
        dialog.DestroyAfterClose();
        return dialog;
    }
    AbortRetryIgnore(txt, callback) {
        var dialog = this.Dialog();
        dialog.AddButton(0, "Abort");
        dialog.AddButton(1, "Retry");
        dialog.AddButton(-1, "Ignore");
        dialog.AddContent(txt);
        dialog.DestroyAfterClose();
        dialog.CallBack = callback;
        return dialog;
    }
    Confirm(txt, callback) {
        var dialog = this.Dialog();
        dialog.AddButton(1, "OK");
        dialog.AddButton(0, "Cancel");
        dialog.AddContent(txt);
        dialog.Title("Confirm");
        dialog.DestroyAfterClose();
        dialog.CallBack = (function (v) {
            if (v === "true" || v === "1" || v === 1 || v) {
                callback();
            }
        });
        return dialog;
    }
    Html(html) {
        var dialog = this.Dialog();
        dialog.AddContent(html);
        return dialog;
    }
    SaveBeforeExit(callback) {
        var dialog = this.Dialog();
        dialog.AddButton(-1, "Do not Save");
        dialog.AddButton(0, "Cancel");
        dialog.AddButton(1, "Save");
        dialog.Title("Do You Save Before Exit");
        dialog.AddContent("unsaved data will be lost");
        dialog.DestroyAfterClose();
        dialog.CallBack = callback;
        return dialog;
    }
   
    YesNoCancel(txt, callback) {
        var dialog = this.Dialog();
        dialog.AddButton(1, "Yes");
        dialog.AddButton(-1, "No");
        dialog.AddButton(0, "Cancel");
        dialog.AddContent(txt);
        dialog.DestroyAfterClose();
        dialog.CallBack = callback;
        return dialog;
    }
};
