class SuperDialog_Template_Personal extends SuperDialog {
    ChangePassword(callback) {
        var dialog = this.TwoRow(function (v) {
            if (v["new"] === v["confirm"]) {
                var cboutput = callback({
                    "old": v["old"],
                    "new": v["new"]
                });
                if (cboutput === true) {
                    return true;
                } else if (cboutput !== undefined) {
                    dialog.Error(cboutput);
                    return false;
                }
                return true;
            } else {

                dialog.Reset();
                dialog.Error("password do not match");
                return false;
            }
        });
        dialog.Title("Change Password");
        dialog.AddRow("Old Password:", "<input type='password'  style='width:100%;box-sizing: border-box;' name='old' />");
        dialog.AddRow("New Password:", "<input type='password'  style='width:100%;box-sizing: border-box;' name='new' />");
        dialog.AddRow("Confirm Password:", "<input type='password'  style='width:100%;box-sizing: border-box;' name='confirm' />");
        dialog.DestroyAfterClose();
        return dialog;
    }

    Contact(callback) {
        var dialog = this.TwoRow(function (v) {
            return callback(v);
        });
        dialog.Title("Contact");
        dialog.AddRow("Name:", "<input type='text'  style='width:100%;box-sizing: border-box;' name='name' />");
        dialog.AddRow("Email:", "<input type='text'  style='width:100%;box-sizing: border-box;' name='email' />");
        dialog.AddRow("Phone:", "<input type='text'  style='width:100%;box-sizing: border-box;' name='phone' />");
        dialog.AddRow("Subject:", "<input type='text'  style='width:100%;box-sizing: border-box;' name='subject' />");
        dialog.AddRow("<textarea style='min-width:100%;resize:vertical;' name='message' ></textarea>");
        dialog.AddRow("Attachment:", "<input type='file' name='file' />");
        return dialog;
    }

    Email(callback) {
        var dialog = this.TwoRow(function (v) {
            return callback(v);
        });
        dialog.Title("Email");
        dialog.AddRow("To:", "<input type='text'  style='width:100%;box-sizing: border-box;' name='to' />");
        dialog.AddRow("Cc:", "<input type='text'  style='width:100%;box-sizing: border-box;' name='cc' />");
        dialog.AddRow("Bcc:", "<input type='text'  style='width:100%;box-sizing: border-box;' name='bcc' />");
        dialog.AddRow("Subject:", "<input type='text'  style='width:100%;box-sizing: border-box;' name='subject' />");
        dialog.AddRow("<textarea style='min-width:100%;resize:vertical;' name='message' ></textarea>");
        dialog.AddRow("Attachment:", "<input type='file' name='file' />");
        return dialog;
    }
    Login(callback) {
        var dialog = this.TwoRow(function (v) {
            return callback(v);
        });
        dialog.Title("Login");
        dialog.AddRow("Username", '<input type="text"  style="width:99%;box-sizing: border-box;" name="username" />');
        dialog.AddRow("Password", '<input type="password"  style="width:99%;box-sizing: border-box;" name="password" />');
        dialog.AddRow("Remember Me ", '<input type="checkbox"  style="width:99%;box-sizing: border-box;" value="true" name="remember" />');
        return dialog;
    }

    Personal(callback) {
        var dialog = this.TwoRow(function (v) {
            return callback(v);
        });
        dialog.Title("Personal");
        dialog.AddRow("Name:", "<input type='text'  style='width:100%;box-sizing: border-box;' name='name' />");
        dialog.AddRow("LastName:", "<input type='text'  style='width:100%;box-sizing: border-box;' name='lastname' />");
        dialog.AddRow("MiddleName:", "<input type='text'  style='width:100%;box-sizing: border-box;' name='middlename' />");
        dialog.AddRow("ID-Passport:", "<input type='text'  style='width:100%;box-sizing: border-box;' name='id' />");
        dialog.AddRow("Sex:", "<select  style='width:100%;box-sizing: border-box;' >  <option value='M' >Male</option>  <option value='F'>Female</option><option value='L'>LGBT</option></select>");
        dialog.AddRow("Phone", "<input type= 'text'  style= 'width:100%;box-sizing: border-box; ' name= 'phone' />");
        dialog.AddRow("Email", "<input type= 'text'  style= 'width:100%;box-sizing: border-box; ' name= 'email' />");
        dialog.AddRow("Fax", "<input type= 'text'  style= 'width:100%;box-sizing: border-box; ' name= 'fax' />");
        dialog.AddRow("Address", "<textarea style='min-width: 100%; resize: vertical;' name='Address'></textarea>");
        dialog.AddRow("City", "<input type= 'text '  style= 'width:100%;box-sizing: border-box; ' name= 'city' />");
        dialog.AddRow("Zip Code", "<input type= 'text '  style= 'width:100%;box-sizing: border-box; ' name= 'zip' />");
        dialog.AddRow("Country", "<input type= 'text '  style= 'width:100%;box-sizing: border-box; ' name= 'country' />");
        return dialog;
    }
    Register(callback) {
        var dialog = this.TwoRow(function (v) {
            return callback(v);
        });
        dialog.Title("Register");
        dialog.AddRow("Email:", "<input type='text'  style='width:100%;box-sizing: border-box;' name='email' />");
        dialog.AddRow("Password:", "<input type='password'  style='width:100%;box-sizing: border-box;' name='password' />");
        dialog.AddRow("Phone:", "<input type='text'  style='width:100%;box-sizing: border-box;' name='phone' />");
        return dialog;
    }
}
;