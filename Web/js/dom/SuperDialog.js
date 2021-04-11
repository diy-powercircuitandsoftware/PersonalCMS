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

    }

    Advertisement(txt, time) {
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
    }
    Alert(txt) {
        var dialog = document.body.appendChild(document.createElement("DIALOG"));
        dialog.innerHTML = "<form method='dialog'>" + txt + "<div style='text-align: right;'><button>OK</button></div></form>";
        dialog.showModal();
    }
    Canvas(w, h) {
        var dialog = document.body.appendChild(document.createElement("DIALOG"));
        dialog.innerHTML = "<form method='dialog'><div style='text-align: right;'><button>x</button></div></form>";
        var canvas = dialog.appendChild(document.createElement('canvas'));
        canvas.style.cssText = "border-style: solid;border-width: thin;";
        canvas.width = w;
        canvas.height = h;
        dialog.showModal();
    }
    ChangePassword(callback) {
        var dialog = document.body.appendChild(document.createElement("DIALOG"));
        dialog.innerHTML = "<div data-output='error'></div>" +
                "<table>" +
                "<tr><td>Old Password:</td><td><input type='password'  style='width:100%;box-sizing: border-box;' name='old' /></td></tr>" +
                "<tr><td>New Password:</td><td><input type='password'  style='width:100%;box-sizing: border-box;' name='new' /></td></tr>" +
                "<tr><td>Confirm Password:</td><td><input type='password'  style='width:100%;box-sizing: border-box;' name='confirm' /></td></tr>" +
                "</table>"+
                "<div style='text-align: right;'><button data-bn='callback'>OK</button><button data-bn='close'>Cancel</button></div>";

        dialog.querySelector('[data-bn="callback"]').addEventListener("click", function () {
            var cpw = dialog.querySelector('[name="new"]').value == dialog.querySelector('[name="confirm"]').value;
            var output = {
                "old": dialog.querySelector('[name="old"]').value,
                "new": dialog.querySelector('[name="new"]').value
            };
            if (!cpw) {
                dialog.querySelector('[data-output="error"]').innerHTML = "password do not match";
            } else if (callback(output) === true && cpw) {
                dialog.close();
            } else {
                dialog.querySelector('[data-output="error"]').innerHTML = callback(output);
            }
        });
        dialog.querySelector('[data-bn="close"]').addEventListener("click", function () {
            dialog.close();
        });

        dialog.showModal();

    }
//edit
    Confirm(txt, callback) {
        var sd = new Dialog();

        sd.Resize(false);
        sd.Content(txt);
        sd.TextAlign("center");
        sd.DestroyAfterClose();
        sd.Show();
        sd.Button({"OK": function () {
                if (typeof callback === "function") {
                    callback();
                    sd.Close();
                }
            }, "Cancel": function () {
                sd.Close();
            }});
        return sd;
    }

    Contact(callback) {
        var sd = this.TableLayout(callback);

        sd.Resize(false);
        sd.AddNewRowElement("Name", '<input type="text"  style="width:100%;box-sizing: border-box;" value="" />');
        sd.AddNewRowElement("Email", '<input type="text"  style="width:100%;box-sizing: border-box;" value="" />');
        sd.AddNewRowElement("Phone", '<input type="text"  style="width:100%;box-sizing: border-box;" value="" />');
        sd.AddNewRowElement("Subject", '<input type="text"  style="width:100%;box-sizing: border-box;" value="" />');
        sd.AddNewRowElement('<textarea style="min-width:100%;" name="Message" ></textarea>');
        sd.AddNewRowElement("Attachment", '<input type="file" name="" />');
        sd.Normalize();
        return sd;
    }

    DropDown(callback) {
        var sd = new Dialog();
        sd.dd = sd.Content(document.createElement("SELECT"));

        sd.dd.style.width = "99%";
        sd.Resize(false);
        sd.DestroyAfterClose();
        sd.Show();
        sd.Button({"OK": function () {
                if (typeof callback === "function") {
                    callback(sd.dd.value);
                    sd.Close();
                }
            }, "Cancel": function () {
                sd.Close();
            }});
        sd.Add = function (k, v) {
            var opt = this.dd.appendChild(document.createElement('option'));
            opt.value = k;
            opt.innerHTML = v;
            return this;
        };
        sd.CopyOption = function (querystring) {
            var ref = this;
            [].forEach.call(document.querySelectorAll(querystring), function (dom) {
                [].forEach.call(dom.querySelectorAll("OPTION"), function (option) {
                    var opt = ref.dd.appendChild(document.createElement('option'));
                    opt.value = option.value;
                    opt.innerHTML = option.innerHTML;
                });

            });

            return this;
        };
        return sd;
    }
    Email(callback) {
        var sd = this.TableLayout(callback);

        sd.Resize(false);
        sd.AddNewRowElement("To", '<input type="text"  style="width:100%;box-sizing: border-box;" value="" />');
        sd.AddNewRowElement("Cc", '<input type="text"  style="width:100%;box-sizing: border-box;" value="" />');
        sd.AddNewRowElement("Bcc", '<input type="text"  style="width:100%;box-sizing: border-box;" value="" />');
        sd.AddNewRowElement("Subject", '<input type="text"  style="width:100%;box-sizing: border-box;" value="" />');
        sd.AddNewRowElement('<textarea style="min-width:100%;" name="Message" ></textarea>');
        sd.AddNewRowElement("Attachment", '<input type="file" name="" />');
        return sd;
    }
    Html(html) {
        var sd = new Dialog();

        sd.Content(html);
        sd.DestroyAfterClose();
        sd.Show();
        sd.Size("80%", "80%");
        return sd;
    }
    Import(...args) {

        var title = args[0];
        var querystring = args[1];
        var node = document.querySelector(querystring);
        var parrent = node.parentNode;
        var sd = new Dialog();

        sd.BeforeClose(function () {
            parrent.appendChild(node);
            if (node.normalhide) {
                node.style.display = "none";
            }
            return true;
        });
        if (args.length === 3) {
            var button = args[2];
            sd.Button(button);
        }
        sd.Content(node);
        sd.DestroyAfterClose();
        node.normalhide = (node.style.display === "none");
        if (node.normalhide) {
            node.style.display = "block";
        }
        sd.Show();
        return sd;
    }
    ImportOkCancel(...args) {
        var d = this.Import(args[0], args[1], {"OK": function () {
                if (typeof args[2] === "function" && args[2]()) {
                    d.Close();
                } else if (typeof args[2] !== "function") {
                    d.Close();
                }
            }, "Cancel": function () {
                d.Close();

            }});
        return d;
    }

    Load(...args) {
        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function () {
            if (this.readyState === 4 && this.status === 200) {
                this.sd.Content(xhttp.responseText);
                this.sd.DestroyAfterClose();
                this.sd.Show();
            }
        };
        xhttp.sd = new Dialog();
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
    License(...args) {
        var sd = new Dialog();

        sd.Resize(false);
        sd.Content(args[0]);
        sd.DestroyAfterClose();
        sd.Show();
        sd.Closeable(false);
        sd.Size("80%", "80%");
        sd.Button({"Accept": function () {
                if (args.length === 2 && typeof args[1] === "function") {
                    args[1]();
                }
                sd.Close();
            }});
        return sd;
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

    TableLayout(callback) {
        var sd = new Dialog();
        sd.table = document.createElement("TABLE");

        sd.Content(sd.table);
        sd.DestroyAfterClose();
        sd.table.style.cssText = "width: 100%; ";
        sd.Show();
        sd.Button({"OK": function () {
                if (typeof callback === "function") {
                    var arrchk = ["checkbox", "radio"];
                    var val = {};
                    [].forEach.call(sd.table.querySelectorAll('select,input,textarea'), function (el) {
                        if (el.tagName === "INPUT" && arrchk.indexOf(el.type) >= 0 && el.checked && el.value === undefined) {
                            val[el.name] = el.checked;
                        } else if (el.tagName === "INPUT" && arrchk.indexOf(el.type) >= 0 && el.checked) {
                            val[el.name] = el.value;
                        } else if (el.tagName === "INPUT" && el.type === "file") {
                            val[el.name] = el.files;
                        } else if (el.tagName === "INPUT" && arrchk.indexOf(el.type) === -1) {
                            val[el.name] = el.value;
                        } else if (el.tagName === "TEXTAREA") {
                            val[el.name] = el.value;
                        } else if (el.tagName === "SELECT" && el.getAttribute("multiple") === "multiple") {
                            val[el.name] = [];
                            for (var i = 0; i < el.options.length; i++) {
                                var option = el.options[i];
                                if (option.selected) {
                                    val[el.name].push(option.value);
                                }
                            }
                        } else if (el.tagName === "SELECT" && el.getAttribute("multiple") !== "multiple") {
                            val[el.name] = el.value;
                        }

                    });

                    if (callback(val)) {
                        sd.Close();
                    }
                } else {
                    sd.Close();
                }
            }, "Cancel": function () {
                sd.Close();
            }});
        sd.AddNewRowElement = function (...args) {
            var row = this.table.insertRow(-1);
            if (args.length === 1) {
                var cell = row.insertCell(-1);
                var data = args[0];
                if (typeof data === 'string' || data instanceof String) {
                    cell.innerHTML = data;
                } else if (data instanceof HTMLElement) {
                    cell.appendChild(data);
                }
                cell.setAttribute("data-marker", "1");
            } else if (args.length === 2) {
                row.insertCell(-1).appendChild(document.createTextNode(args[0] + ":"));
                var data = args[1];
                var cell = row.insertCell(-1);
                if (typeof data === 'string' || data instanceof String) {
                    cell.insertAdjacentHTML('beforeend', data);
                    cell.lastChild.name = args[0].replace(/\s/g, '');
                } else if (data instanceof HTMLElement) {
                    cell.appendChild(data);
                    data.name = args[0].replace(/\s/g, '');
                }
                cell.setAttribute("data-marker", "1");
            } else if (args.length === 3) {
                var id = args[0];
                var name = args[1];
                var data = args[2];
                row.insertCell(-1).appendChild(document.createTextNode(name + ":"));
                var cell = row.insertCell(-1);
                if (typeof data === 'string' || data instanceof String) {
                    cell.insertAdjacentHTML('beforeend', data);
                    cell.lastChild.name = id;
                } else if (data instanceof HTMLElement) {
                    cell.appendChild(data);
                    data.name = id;
                }
                cell.setAttribute("data-marker", "1");
            }
            return this;
        };
        sd.AddNewCellElement = function (...args) {
            var lastrow = this.table.rows.length - 1;
            var row = this.table.rows[lastrow];
            if (args.length === 1) {
                var cell = row.insertCell(-1);
                var data = args[0];
                if (typeof data === 'string' || data instanceof String) {
                    cell.innerHTML = data;
                } else if (data instanceof HTMLElement) {
                    cell.appendChild(data);
                }
                cell.setAttribute("data-marker", "1");
            } else if (args.length === 2) {
                row.insertCell(-1).appendChild(document.createTextNode(args[0] + ":"));
                var data = args[1];
                var cell = row.insertCell(-1);
                if (typeof data === 'string' || data instanceof String) {
                    cell.insertAdjacentHTML('beforeend', data);
                    if (args[0] !== null) {
                        cell.lastChild.name = args[0].replace(/\s/g, '');
                    }
                } else if (data instanceof HTMLElement) {
                    cell.appendChild(data);
                    if (args[0] !== null) {
                        data.name = args[0].replace(/\s/g, '');
                    }
                } else {
                    cell.insertAdjacentHTML('beforeend', data);
                }
                cell.setAttribute("data-marker", "1");
            } else if (args.length === 3) {
                var id = args[0];
                var name = args[1];
                var data = args[2];
                row.insertCell(-1).appendChild(document.createTextNode(name + ":"));
                var cell = row.insertCell(-1);
                if (typeof data === 'string' || data instanceof String) {
                    cell.insertAdjacentHTML('beforeend', data);
                    cell.lastChild.name = id;
                } else if (data instanceof HTMLElement) {
                    cell.appendChild(data);
                    data.name = id;
                }
                cell.setAttribute("data-marker", "1");
            }
            return this;
        };
        sd.AddRowCellElement = function (...args) {
            if (args.length === 4) {
                var data = args[4];
                var cell = this.table.rows[args[0]].cells[args[1]];
                if (typeof data === 'string' || data instanceof String) {
                    cell.insertAdjacentHTML('beforeend', data);
                    cell.lastChild.name = args[3].replace(/\s/g, '');
                } else if (data instanceof HTMLElement) {
                    cell.appendChild(data);
                    data.name = args[3].replace(/\s/g, '');
                }

            }
            return this;
        };
        sd.GetElements = function (...args) {
            if (args.length === 0) {
                return this.table.querySelectorAll('select,input,textarea');
            } else if (args.length === 1) {
                return this.table.querySelectorAll(args[0]);
            }
        };
        sd.Normalize = function () {
            var maxcell = 0;
            for (var r = 0; r < this.table.rows.length; r++) {
                var curr = this.table.rows[r].cells.length;
                maxcell = Math.max(maxcell, curr);
            }
            for (var r = 0; r < this.table.rows.length; r++) {
                var curr = this.table.rows[r].cells.length;
                var marker = this.table.rows[r].querySelectorAll("[data-marker='1']");
                var tddiff = curr - (marker.length);
                var remain = maxcell - tddiff;
                var ratio = (remain / marker.length);
                [].forEach.call(marker, function (el) {
                    el.colSpan = ratio;
                });
            }
        };
        sd.SetValue = function (name, value) {
            [].forEach.call(sd.table.querySelectorAll('select,input,textarea'), function (el) {
                if (el.getAttribute('name') === name) {
                    el.value = value;
                }
            });
        };

        return sd;
    }
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

class Dialog {

    constructor() {
        this.destroyafterclose = false;
        this.dialog = document.body.appendChild(document.createElement("DIALOG"));

        var htmlcode =
                '<div data-domdialog="content" style="max-height: 100%; overflow-y: auto;"></div>' +
                '<div data-domdialog="button" style="text-align: right;"></div>';


        this.dialog.innerHTML = htmlcode;
        document.activeElement.blur();
        //resize: both;overflow: auto;
    }

    Append(data) {
        var content = this.dialog.querySelector('[data-domdialog="content"]');
        if (typeof data === 'string' || data instanceof String) {
            content.insertAdjacentHTML('beforeend', data);
        } else if (data instanceof HTMLElement) {
            content.appendChild(data);
        } else if (data instanceof NodeList || data instanceof HTMLCollection) {
            [].forEach.call(data, function (d) {
                content.appendChild(d);
            });
        }
        return this;
    }

    BeforeClose(...args) {
        if (args.length === 0) {
            return     this.BeforeCloseEvent;
        } else if (args.length === 1 && typeof args[0] === "function") {
            this.BeforeCloseEvent = args[0];
        }
        return this;
    }

    Button(...args) {
        var button = this.dialog.querySelector('[data-domdialog="button"]');
        if (args.length === 0) {
            return   button.getElementsByTagName("button");
        } else if (args.length === 1 && (typeof args[0] === 'string' || args[0]  instanceof String)) {
            var bn = button.appendChild(document.createElement("button"));
            bn.innerHTML = args[0];
            return bn;
        } else if (args.length === 1 && (typeof args[0] === 'object')) {
            var arg = args[0];
            for (var k in arg) {
                var bn = button.appendChild(document.createElement("button"));
                bn.innerHTML = k;
                if (typeof arg[k] === "function") {
                    bn.addEventListener("click", function () {
                        this.cbfn();
                    });
                    bn.cbfn = arg[k];
                }
            }
        }
    }
    ButtonAlign(...args) {
        var content = this.dialog.querySelector('[data-domdialog="button"]');
        if (args.length === 0) {
            return   content.style.textAlign;
        } else if (args.length === 1) {
            content.style.textAlign = args[0];
            return this;
        }
    }
    Close() {
        if (this.destroyafterclose) {
            if (this.BeforeCloseEvent === undefined || this.BeforeCloseEvent === null) {
                this.dialog.parentNode.removeChild(this.dialog);
            } else if (this.BeforeCloseEvent()) {
                this.dialog.parentNode.removeChild(this.dialog);
            }
            this.Show = false;
            this.dialog.null;
        } else {
            if (this.BeforeCloseEvent === undefined || this.BeforeCloseEvent === null) {
                this.dialog.style.display = "none";
            } else if (this.BeforeCloseEvent()) {
                this.dialog.style.display = "none";
            }
        }

    }
    Closeable(...args) {
        var selector = this.dialog.querySelector('[data-domdialog="bnclose"]');
        if (args.length === 0) {
            return selector.style.display !== "none";
        } else if (args.length === 1 && typeof args[0] === "boolean" && args[0] === true) {
            selector.style.display = "";
        } else if (args.length === 1 && typeof args[0] === "boolean" && args[0] === false) {
            selector.style.display = "none";
        }
        return this;
    }
    Content(...args) {
        var content = this.dialog.querySelector('[data-domdialog="content"]');
        if (args.length === 0) {
            return content.childNodes;
        } else if (args.length === 1) {
            content.innerHTML = "";
            var data = args[0];
            if (typeof data === 'string' || data instanceof String) {
                content.innerHTML = data;
                return content.childNodes;
            } else if (data instanceof HTMLElement) {
                content.appendChild(data);
                return data;
            } else if (data instanceof NodeList || data instanceof HTMLCollection) {
                [].forEach.call(data, function (d) {
                    content.appendChild(d);
                });
                return data;
            }
        }
    }
    DestroyAfterClose(...args) {
        if (args.length == 0) {
            this.destroyafterclose = true;
        } else if (args.length == 1 && typeof args[0] === "boolean") {
            this.destroyafterclose = args[0];
        }

    }

    Resize(...args) {
        return this;
        var selector = this.dialog.querySelector('[data-domdialog="frame"]');
        if (args.length === 0) {
            return selector.style.resize;
        } else if (args.length === 1 && typeof args[0] === "boolean" && args[0] === true) {
            selector.style.resize = "both";
        } else if (args.length === 1 && typeof args[0] === "boolean" && args[0] === false) {
            selector.style.resize = "none";
        } else if (args.length === 1 && typeof args[0] === "string") {
            selector.style.resize = args[0];
        }
        return this;
    }

    Show() {
        this.dialog.style.display = "block";
        if (typeof this.dialog.showModal === "function") {
            this.dialog.showModal();
        }
        return this;
    }
    Size(...args) {
        var selector = this.dialog.querySelector('[data-domdialog="frame"]');
        if (args.length === 0) {
            var rect = selector.getBoundingClientRect();
            return {
                "width": rect.width,
                "height": rect.height
            }
        } else if (args.length == 2) {
            selector.style.width = args[0];
            selector.style.height = args[1];
        }
    }
    TextAlign(...args) {
        var content = this.dialog.querySelector('[data-domdialog="content"]');
        if (args.length === 0) {
            return   content.style.textAlign;
        } else if (args.length === 1) {
            content.style.textAlign = args[0];
            return this;
        }
    }

    ZIndex(...args) {
        if (args.length === 0) {
            return this.dialog.style.zIndex;
        } else if (args.length === 1) {
            this.dialog.style.zIndex = args[0];
            this.dialog.querySelector('[data-domdialog="overlay"]').style.zIndex = args[0];
            this.dialog.querySelector('[data-domdialog="frame"]').style.zIndex = args[0] + 1;
            return this;
        }
    }
}
