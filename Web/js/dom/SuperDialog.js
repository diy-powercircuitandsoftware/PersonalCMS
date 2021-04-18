class SuperDialog {
    AbortRetryIgnore(txt, callback) {
        var dialog = document.body.appendChild(document.createElement("DIALOG"));
        dialog.innerHTML = "<form method='dialog'>" + txt + "<div style='text-align: center;'>" +
                "<button data-value='0'>Abort</button>" +
                "<button data-value='1' >Retry</button>" +
                "<button data-value='-1'>Ignore</button></div>" +
                "</form>";
        dialog.addEventListener("click", function (e) {
            if (e.target.getAttribute("data-value") !== null) {
                callback(e.target.getAttribute("data-value"));
            }
        });
        dialog.showModal();
        return dialog;
    }

    Advertisement(txt, time) {
        if (!Number.isInteger(time)) {
            time = 0;
        }
        var dialog = document.body.appendChild(document.createElement("DIALOG"));
        dialog.innerHTML = "<form method='dialog'>" + txt + "<div style='text-align: center;'><button disabled data-bn='close'>Close(" + time + ")</button></div></form>";
        var qs = dialog.querySelector('[data-bn="close"]');
        var t = setInterval(function () {
            qs.innerHTML = "Close(" + time + ")";
            time = time - 1;
            if (time < 0) {
                clearInterval(t);
                qs.removeAttribute("disabled");
            }
        }, 1000);
        dialog.showModal();
        return dialog;
    }
    Alert(txt) {
        var dialog = document.body.appendChild(document.createElement("DIALOG"));
        dialog.innerHTML = "<form method='dialog'>" + txt + "<div style='text-align: right;'><button>OK</button></div></form>";
        dialog.showModal();
        return dialog;
    }
    Canvas(w, h) {
        var dialog = document.body.appendChild(document.createElement("DIALOG"));
        dialog.style.cssText = "padding: 0;"
        dialog.innerHTML = "<form method='dialog'><div style='text-align: right;'><button style='padding: 0;border: none;background: none;'>x</button></div></form>";
        var canvas = dialog.appendChild(document.createElement('canvas'));
        canvas.style.cssText = "border-style: solid;border-width: thin;";
        canvas.width = w;
        canvas.height = h;
        dialog.showModal();
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
                } else {
                    return cboutput;
                }
            } else {
                dialog.Reset();
                return  "password do not match";
            }
        });
        dialog.SetTitle("Change Password");
        dialog.AddRow("Old Password:", "<input type='password'  style='width:100%;box-sizing: border-box;' name='old' />");
        dialog.AddRow("New Password:", "<input type='password'  style='width:100%;box-sizing: border-box;' name='new' />");
        dialog.AddRow("Confirm Password:", "<input type='password'  style='width:100%;box-sizing: border-box;' name='confirm' />");
        return dialog;
    }

    Confirm(txt, callback) {
        var dialog = document.body.appendChild(document.createElement("DIALOG"));
        dialog.innerHTML = "<form method='dialog'>" + txt + "<div style='text-align: right;'><button data-bn='ok'>OK</button><button>Cancel</button></div></form>";
        dialog.querySelector('[data-bn="ok"]').addEventListener("click", function () {
            callback();
        });
        dialog.showModal();
        return dialog;
    }

    Contact(callback) {
        var dialog = this.TwoRow(function (v) {
            return callback(v);
        });
        dialog.SetTitle("Contact");
        dialog.AddRow("Name:", "<input type='text'  style='width:100%;box-sizing: border-box;' name='name' />");
        dialog.AddRow("Email:", "<input type='text'  style='width:100%;box-sizing: border-box;' name='email' />");
        dialog.AddRow("Phone:", "<input type='text'  style='width:100%;box-sizing: border-box;' name='phone' />");
        dialog.AddRow("Subject:", "<input type='text'  style='width:100%;box-sizing: border-box;' name='subject' />");
        dialog.AddRow("<textarea style='min-width:100%;resize:vertical;' name='message' ></textarea>");
        dialog.AddRow("Attachment:", "<input type='file' name='file' />");
        return dialog;
    }

    DropDown(callback) {

        var dialog = this.Confirm('<div data-output="title">DropDown</div> <select style="width:100%;box-sizing: border-box;"></select>', function () {
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
        dialog.SetTitle = function (v) {
            dialog.querySelector('[data-output="title"]').innerHTML = v;
        };
        return dialog;
    }

    Email(callback) {
        var dialog = this.TwoRow(function (v) {
            return callback(v);
        });
        dialog.SetTitle("Email");
        dialog.AddRow("To:", "<input type='text'  style='width:100%;box-sizing: border-box;' name='to' />");
        dialog.AddRow("Cc:", "<input type='text'  style='width:100%;box-sizing: border-box;' name='cc' />");
        dialog.AddRow("Bcc:", "<input type='text'  style='width:100%;box-sizing: border-box;' name='bcc' />");
        dialog.AddRow("Subject:", "<input type='text'  style='width:100%;box-sizing: border-box;' name='subject' />");
        dialog.AddRow("<textarea style='min-width:100%;resize:vertical;' name='message' ></textarea>");
        dialog.AddRow("Attachment:", "<input type='file' name='file' />");
        return dialog;
    }

    Html(html) {
        var dialog = document.body.appendChild(document.createElement("DIALOG"));
        dialog.style.cssText = "padding: 0;"
        dialog.innerHTML = "<form method='dialog'><div style='text-align: right;'><button style='padding: 0;border: none;background: none;'>x</button></div></form><div style='padding: 7px;'>" + html + "</div>";
        dialog.showModal();
        return dialog;
    }

    Import(querystring) {
        var dialog = document.body.appendChild(document.createElement("DIALOG"));
        dialog.appendChild(document.querySelector(querystring));
        dialog.showModal();
        return dialog;
    }
    ImportOkCancel(querystring, callback) {
        var dialog = this.Confirm('<div data-id="ImportOkCancel"></div>', callback);
        dialog.querySelector('[data-id="ImportOkCancel"]').appendChild(document.querySelector(querystring));
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
    //
    License(txt) {
        var dialog = document.body.appendChild(document.createElement("DIALOG"));
dialog.innerHTML=txt;
        dialog.showModal();
        return dialog;
    }
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
        var dialog = document.body.appendChild(document.createElement("DIALOG"));
        dialog.style.cssText = "resize: both;overflow: auto;";
        dialog.innerHTML = "<div data-output='error'></div><div data-output='title' style='font-weight: bold;'></div>" +
                "<table style='wodth:100%;'></table>" +
                "<div style='text-align: right;'><button data-bn='callback'>OK</button><button data-bn='close'>Cancel</button></div>";


        dialog.querySelector("button[data-bn='callback'").addEventListener("click", function () {
            var output = {};
            [].forEach.call(dialog.querySelectorAll("input[name],textarea"), function (dom) {
                output[dom.name] = dom.value;
            });
            [].forEach.call(dialog.querySelectorAll("input[type='file']"), function (dom) {
                output[dom.name] = dom.files;
            });
            var cboutput = callback(output);
            if (cboutput === true) {
                dialog.close();
            } else {
                dialog.querySelector('[data-output="error"]').innerHTML = cboutput;
            }
        });
        dialog.querySelector('[data-bn="close"]').addEventListener("click", function () {
            dialog.close();
        });
        dialog.AddRow = function (...args) {
            var row = dialog.querySelector("table").insertRow(-1);
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
        dialog.Reset = function () {
            [].forEach.call(dialog.querySelectorAll("input,textarea"), function (dom) {
                dom.value = "";
            });
        };
        dialog.SetTitle = function (v) {
            dialog.querySelector('[data-output="title"]').innerHTML = v;
        };
        dialog.showModal();
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

