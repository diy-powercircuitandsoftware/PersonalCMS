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
            } else if (c instanceof HTMLElement) {
                return  content.appendChild(c);
            }
        };
        dialog.Title = function (t) {
            title.style.cssText = "display:flex;border-style: solid;border-width: thin;";
            title.setAttribute("method", "dialog");
            title.innerHTML = '<span   style="font-weight: bold;flex-grow: 1;">' + t + '</span><button>X</button>';

        };

        dialog.DestroyAfterClose = function () {
            this.addEventListener('close', function () {
                this.parentNode.removeChild(this);
            });
        };

        dialog.showModal();
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
    Alert(txt) {
        var dialog = this.Dialog();
        dialog.AddButton("OK", "OK");
        dialog.AddContent(txt);
        dialog.DestroyAfterClose();
        return dialog;
    }
    Canvas(w, h) {
        var dialog = this.Dialog();
        dialog.Title("Canvas");
        var canvas = dialog.AddContent(document.createElement('canvas'));
        canvas.style.cssText = "border-style: solid;border-width: thin;";
        canvas.width = w;
        canvas.height = h;
        dialog.DestroyAfterClose();
        return dialog;
    }
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

    Confirm(txt, callback) {
        var dialog = this.Dialog();
        dialog.AddButton(1, "OK");
        dialog.AddButton(0, "Cancel");
        dialog.AddContent(txt);
        dialog.DestroyAfterClose();
        dialog.CallBack = (function (v) {
            if (v === "true" || v === "1" || v === 1 || v) {
                callback();
            }
        });
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

    DropDown(callback) {
        var dialog = this.Confirm('<select style="width:100%;box-sizing: border-box;"></select>', function () {
            callback(dialog.querySelector('select').value);
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
        };
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

    Html(html) {
        var dialog = this.Dialog();
        dialog.AddContent(html);
        return dialog;
    }

    Import(querystring) {
        var dialog = this.Dialog();
        dialog.AddContent(document.querySelector(querystring));
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

    Load(...args) {
        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function () {
            if (this.readyState === 4 && this.status === 200) {
                this.sd.Html(xhttp.responseText);

            }
        };
        xhttp.sd = this;
        ;
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
        return  xhttp.sd;
    }
   
    License(txt) {
        var dialog = this.Dialog();
        dialog.AddContent(txt);
        dialog.AddButton(1, "Accept");
        return dialog;
    }
     //
    Loading(...args) {
        var sd = new Dialog();

        sd.Resize(false);
        sd.pgdom = sd.Content('<progress   max="1"  style="width: 100%;"></progress><div></div>');
        sd.DestroyAfterClose();
        sd.Show();
        if (args.length === 1 && typeof args[0] === "function") {
            sd.Button({"Cancel": function () {
                    if (args[0]()) {
                        sd.Close();
                    }
                }});
        }
        sd.Val = function (v) {
            this.pgdom[0].value = v;
        };
        sd.Log = function (v) {
            this.pgdom[1].innerHTML = v;
        };
        return sd;
    }
    Login(callback) {
        var sd = this.TableLayout(callback);

        sd.Resize(false);
        sd.AddNewRowElement("Username", '<input type="text"  style="width:100%;box-sizing: border-box;" value="" />');
        sd.AddNewRowElement("Password", '<input type="password"  style="width:100%;box-sizing: border-box;" value="" />');
        sd.AddNewRowElement("Remember Me ", '<input type="checkbox"  style="width:100%;box-sizing: border-box;" value="true" />');
        return sd;
    }
    MediaPlayer(...args) {
        var src = args[0];
        var sd = new Dialog();
        var image = ["gif", "png", "jpg", "jpeg", "webp"];
        var video = ["mp4", "webm"];
        var audio = ["mp3", "ogg"];

        sd.ButtonAlign("center");
        sd.TextAlign("center");
        sd.DestroyAfterClose();
        sd.Show();
        if (Array.isArray(src)) {
            sd.Content(' <div style="background-color: black;min-width: 300px;min-height: 300px;width: 100%;height: 100%;"></div>');
            sd.i = 0;
            sd.Button({"&lt;": function () {
                    sd.i = (sd.i + 1) % src.length;
                    var ext = (src[sd.i].split('.').pop()).toLowerCase();
                    if (image.indexOf(ext) >= 0) {
                        sd.Content(' <div style="background-color: black;width: 100%;height: 100%;"><img style="max-width: 100%;max-height: 100%;" src="' + src[ sd.i] + '"/></div>');
                    } else if (video.indexOf(ext) >= 0) {
                        sd.Content(' <div style="background-color: black;width: 100%;height: 100%;"><video  style="background-color: black;width: 100%;height: 100%;" controls="controls" autoplay="autoplay" src="' + src[ sd.i] + '"></video></div>');
                    } else if (audio.indexOf(ext) >= 0) {
                        sd.Content(' <div style="background-color: black;width: 100%;height: 100%;"><audio style="background-color: black;width: 100%;" controls="controls" autoplay="autoplay" src="' + src[ sd.i] + '"></audio></div>');
                    }

                }, "&gt;": function () {
                    sd.i = (src.length + sd.i - 1) % src.length;
                    var ext = (src[sd.i].split('.').pop()).toLowerCase();
                    if (image.indexOf(ext) >= 0) {
                        sd.Content(' <div style="background-color: black;width: 100%;height: 100%;"><img style="max-width: 100%;max-height: 100%;" src="' + src[ sd.i] + '"/></div>');
                    } else if (video.indexOf(ext) >= 0) {
                        sd.Content(' <div style="background-color: black;width: 100%;height: 100%;"><video  style="background-color: black;width: 100%;height: 100%;" controls="controls" autoplay="autoplay" src="' + src[ sd.i] + '"></video></div>');
                    } else if (audio.indexOf(ext) >= 0) {
                        sd.Content(' <div style="background-color: black;width: 100%;height: 100%;"><audio style="background-color: black;width: 100%;" controls="controls" autoplay="autoplay" src="' + src[ sd.i] + '"></audio></div>');
                    }
                }});

        } else if (args.length == 2) {
            var ext = args[1];
            if (image.indexOf(ext) >= 0) {
                sd.Content(' <div style="background-color: black;width: 100%;height: 100%;"><img style="max-width: 100%;max-height: 100%;" src="' + src + '"/></div>');
            } else if (video.indexOf(ext) >= 0) {
                sd.Content(' <div style="background-color: black;width: 100%;height: 100%;"><video  style="background-color: black;width: 100%;height: 100%;" controls="controls" autoplay="autoplay" src="' + src + '"></video></div>');
            } else if (audio.indexOf(ext) >= 0) {
                sd.Content(' <div style="background-color: black;width: 100%;height: 100%;"><audio style="background-color: black;width: 100%;" controls="controls" autoplay="autoplay" src="' + src + '"></audio></div>');
            }
        } else {
            var ext = (src.split('.').pop()).toLowerCase();
            if (image.indexOf(ext) >= 0) {
                sd.Content(' <div style="background-color: black;width: 100%;height: 100%;"><img style="max-width: 100%;max-height: 100%;" src="' + src + '"/></div>');
            } else if (video.indexOf(ext) >= 0) {
                sd.Content(' <div style="background-color: black;width: 100%;height: 100%;"><video  style="background-color: black;width: 100%;height: 100%;" controls="controls" autoplay="autoplay" src="' + src + '"></video></div>');
            } else if (audio.indexOf(ext) >= 0) {
                sd.Content(' <div style="background-color: black;width: 100%;height: 100%;"><audio style="background-color: black;width: 100%;" controls="controls" autoplay="autoplay" src="' + src + '"></audio></div>');
            }
        }

    }
    Mutilline(...args) {
        var sd = new Dialog();
        sd.ta = document.createElement("textarea");
        sd.ta.style.cssText = " width: 100%;height: 100%;resize: none;";

        sd.Content(args[0]);
        sd.Append(sd.ta);
        sd.DestroyAfterClose();
        sd.Show();
        sd.Button({"OK": function () {
                if (typeof args[1] === "function") {
                    if (args[1](sd.ta.value)) {
                        sd.Close();
                    }
                }
            }, "Cancel": function () {
                sd.Close();
            }});
        return sd;
    }
    Payment(callback) {
        var sd = this.TableLayout(callback);

        sd.AddNewRowElement("CARD NUMBER", '<input type="text" placeholder="0000 0000 0000 0000" style="width:100%;box-sizing: border-box;" value="" />');
        sd.AddNewRowElement("CARD HOLDER", '<input type="text" placeholder="name" style="width:100%;box-sizing: border-box;" value="" />');
        sd.AddNewRowElement("EXPIRES", '<input type="text" placeholder="MM/YY" style="width:100%;box-sizing: border-box;" value="" />');
        sd.AddNewRowElement("CVV", '<input type="password"  style="width:100%;box-sizing: border-box;" value="" />');
        return sd;
    }
    Personal(callback) {
        var sd = this.TableLayout(callback);

        sd.AddNewRowElement("Name", '<input type="text"  style="width:100%;box-sizing: border-box;" value="" />');
        sd.AddNewCellElement("LastName", '<input type="text"  style="width:100%;box-sizing: border-box;" value="" />');
        sd.AddNewCellElement("MiddleName", '<input type="text"  style="width:100%;box-sizing: border-box;" value="" />');
        sd.AddNewRowElement("ID-Passport", '<input type="text"  style="width:100%;box-sizing: border-box;" value="" />');
        sd.AddNewCellElement("Sex", '<select  style="width:100%;box-sizing: border-box;" >  <option value="M" >Male</option>  <option value="F" >Female</option></select>');
        sd.AddNewCellElement("Marital", '<select  style="width:100%;box-sizing: border-box;" ><option value="S" >Single</option><option value="M" >Married</option><option value="D" >Divorced</option></select>');
        sd.AddNewRowElement("Phone", '<input type="text"  style="width:100%;box-sizing: border-box;" value="" />');
        sd.AddNewCellElement("Email", '<input type="text"  style="width:100%;box-sizing: border-box;" value="" />');
        sd.AddNewCellElement("Fax", '<input type="text"  style="width:100%;box-sizing: border-box;" value="" />');
        sd.AddNewRowElement('Address:<textarea style="min-width: 100%; resize: vertical;" name="Address"></textarea>');
        sd.AddNewRowElement("City", '<input type="text"  style="width:100%;box-sizing: border-box;" value="" />');
        sd.AddNewCellElement("Zip Code", '<input type="text"  style="width:100%;box-sizing: border-box;" value="" />');
        sd.AddNewCellElement("Country", '<input type="text"  style="width:100%;box-sizing: border-box;" value="" />');
        sd.Normalize();
        return sd;
    }
    PleaseWait() {
        var sd = new Dialog();

        sd.Resize(false);
        sd.Content("<div style='cursor:wait;'>Please Wait</div>");
        sd.TextAlign("center");
        sd.DestroyAfterClose();
        sd.Show();
        return sd;
    }

    Prompt(...args) {
        var sd = new Dialog();
        sd.ta = document.createElement("input");
        sd.ta.style.cssText = " width: 100%;";

        sd.Content(args[0]);
        sd.Append(sd.ta);
        sd.DestroyAfterClose();
        sd.Resize(false);
        sd.Show();
        sd.Button({"OK": function () {
                if (typeof args[1] === "function") {
                    if (args[1](sd.ta.value)) {
                        sd.Close();
                    }
                }
            }, "Cancel": function () {
                sd.Close();
            }});
        return sd;
    }

    Rect(callback) {
        var sd = this.TableLayout(callback);

        sd.Resize(false);
        sd.AddNewRowElement("x", '<input type="number"  style="width:100%;box-sizing: border-box;" value="" />');
        sd.AddNewRowElement("y", '<input type="number"  style="width:100%;box-sizing: border-box;" value="" />');
        sd.AddNewRowElement("width ", '<input type="number"  style="width:100%;box-sizing: border-box;" value="" />');
        sd.AddNewRowElement("height ", '<input type="number"  style="width:100%;box-sizing: border-box;" value="" />');
        return sd;
    }
    Register(callback) {
        var sd = this.TableLayout(callback);

        sd.AddNewRowElement("Email", '<input type="text"  style="width:100%;box-sizing: border-box;" value="" />');
        sd.AddNewRowElement("Password", '<input type="password"  style="width:100%;box-sizing: border-box;" value="" />');
        sd.AddNewRowElement("Phone", '<input type="text"  style="width:100%;box-sizing: border-box;" value="" />');
        return sd;
    }
    RowCol(callback) {
        var sd = this.TableLayout(callback);

        sd.Resize(false);
        sd.AddNewRowElement("Row", '<input type="number" min="0" style="width:100%;box-sizing: border-box;" value="" />');
        sd.AddNewRowElement("Column", '<input type="number" min="0"  style="width:100%;box-sizing: border-box;" value="" />');
        return sd;
    }
    Quiz(question, callback) {
        var sd = this.TableLayout(callback);

        sd.Resize(false);
        sd.AddNewRowElement(question);
        sd.AddRadioButton = function (id, name, value) {
            sd.AddNewRowElement(id, name, '<input type="radio"  style="width:100%;box-sizing: border-box;" value="' + value + '" />');
            return this;
        };
        sd.AddSelectOption = function (id, name, value) {
            var s = document.createElement("SELECT");
            s.style.width = "100%";
            sd.AddNewRowElement(id, name, s);
            for (var k in value) {
                var opt = s.appendChild(document.createElement("OPTION"));
                opt.value = k
                opt.innerHTML = value[k];
            }
            return this;
        };
        return sd;
    }
    SaveBeforeExit(callback) {
        var sd = new Dialog();

        sd.Resize(false);
        sd.Content("Do You Save Before Exit");
        sd.TextAlign("center");
        sd.ButtonAlign("center");
        sd.DestroyAfterClose();
        sd.Show();
        sd.Button({"Save": function () {
                if (typeof callback === "function" && callback(1)) {
                    sd.Close();
                }
            }, "Do not Save": function () {
                if (typeof callback === "function" && callback(0)) {
                    sd.Close();
                }
            }, "Cancel": function () {
                sd.Close();
            }}
        );
        return sd;
    }

    Size(callback) {
        var sd = this.TableLayout(callback);

        sd.Resize(false);
        sd.AddNewRowElement("width ", '<input type="number"  style="width:100%;box-sizing: border-box;" value="" />');
        sd.AddNewRowElement("height ", '<input type="number"  style="width:100%;box-sizing: border-box;" value="" />');
        return sd;
    }
//
    TwoRow(callback) {

        var dialog = this.Dialog();
        dialog.error = dialog.AddContent(document.createElement('div'));
        dialog.table = dialog.AddContent(document.createElement('table'));
        dialog.CallBack = (function (v) {
            if (v === "true" || v === "1" || v === 1 || v) {
                var output = {};
                [].forEach.call(dialog.querySelectorAll("input[name],textarea"), function (dom) {
                    output[dom.name] = dom.value;
                });
                [].forEach.call(dialog.querySelectorAll("input[type='file']"), function (dom) {
                    output[dom.name] = dom.files;
                });
                return callback(output);
            }
        });
        dialog.AddButton(1, "OK");
        dialog.AddButton(0, "Cancel");
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
//


    TextArea(...args) {
        var sd = new Dialog();
        sd.ta = document.createElement("textarea");
        sd.ta.style.cssText = " width: 100%;";

        sd.Content(args[0]);
        sd.Append(sd.ta);
        sd.DestroyAfterClose();
        sd.Resize(false);
        sd.Show();
        sd.Button({"OK": function () {
                if (typeof args[1] === "function") {
                    if (args[1](sd.ta.value)) {
                        sd.Close();
                    }
                }
            }, "Cancel": function () {
                sd.Close();
            }});
        return sd;
    }
    UnLock(callback) {
        var sd = new Dialog();

        sd.Resize(false);
        var pw = sd.Content('<input  style="width:100%;box-sizing: border-box;" type="password" name="" value="" />');
        sd.DestroyAfterClose();
        sd.Show();
        sd.Button({"OK": function () {
                if (typeof callback === "function") {
                    if (callback(pw[0].value)) {
                        sd.Close();
                    }
                } else {
                    sd.Close();
                }
            }, "Cancel": function () {
                sd.Close();
            }});
        return sd;
    }
    YesNoCancel(callback) {
        var sd = new Dialog();

        sd.Resize(false);
        sd.Content("Do You Save Before Exit");
        sd.TextAlign("center");
        sd.ButtonAlign("center");
        sd.DestroyAfterClose();
        sd.Show();
        sd.Button({"Yes": function () {
                if (typeof callback === "function" && callback("Y")) {
                    sd.Close();
                }
            }, "No": function () {
                if (typeof callback === "function" && callback("N")) {
                    sd.Close();
                }
            }, "Cancel": function () {
                if (typeof callback === "function" && callback("C")) {
                    sd.Close();
                }
            }}
        );
        return sd;
    }

}

