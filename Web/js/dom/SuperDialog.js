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
                this.ref.Confirm("Cancel????", function () {
                    ref.Cancel();
                    ref.close();
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

    MediaPlayer(...args) {
        var dialog = this.Dialog();
        var image = ["gif", "png", "jpg", "jpeg", "webp"];
        var video = ["mp4", "webm"];
        var audio = ["mp3", "ogg"];
        dialog.Title("MediaPlayer");
        dialog.DestroyAfterClose();
        if (Array.isArray(args[0]))
        {
            var frame = dialog.AddContent(' <div style="background-color: black;width: 640px;height: 360px"></div>');
            frame.list = args[0];
            frame.index = 0;
            var control = dialog.AddContent('<div style="text-align: center;"><button data-dom="-1">&lt;</button><button data-dom="1">&gt;</button></div>');
            control.frame = frame;
            control.addEventListener("click", function (e) {
                if (e.target.tagName.toLowerCase() == "button") {
                    if (e.target.getAttribute("data-dom") == "-1") {
                        this.frame.index = (this.frame.index + 1) % this.frame.list.length;
                    } else if (e.target.getAttribute("data-dom") == "1") {
                        this.frame.index = (this.frame.list.length + this.frame.index - 1) % this.frame.list.length;
                    }
                    var ext = (this.frame.list[this.frame.index].split('.').pop()).toLowerCase();

                    if (image.indexOf(ext) >= 0) {
                        this.frame.innerHTML = ('<img style="max-width: 100%;max-height: 100%;" src="' + this.frame.list[this.frame.index] + '"/>');
                    } else if (video.indexOf(ext) >= 0) {
                        this.frame.innerHTML = ('<video  style="background-color: black;width: 100%;height: 100%;" controls="controls" autoplay="autoplay" src="' + this.frame.list[this.frame.index] + '"></video>');
                    } else if (audio.indexOf(ext) >= 0) {
                        this.frame.innerHTML = ('<audio style="background-color: black;width: 100%;" controls="controls" autoplay="autoplay" src="' + this.frame.list[this.frame.index] + '"></audio>');
                    }
                }
            });

        } else if (args.length === 1) {
            var ext = (args[0].split('.').pop()).toLowerCase();
            if (image.indexOf(ext) >= 0) {
                dialog.AddContent(' <div style="background-color: black;width: 100%;height: 100%;"><img style="max-width: 100%;max-height: 100%;" src="' + args[0] + '"/></div>');
            } else if (video.indexOf(ext) >= 0) {
                dialog.AddContent(' <div style="background-color: black;width: 100%;height: 100%;"><video  style="background-color: black;width: 100%;height: 100%;" controls="controls" autoplay="autoplay" src="' + args[0] + '"></video></div>');
            } else if (audio.indexOf(ext) >= 0) {
                dialog.AddContent(' <div style="min-width: 300px;background-color: black;width: 100%;height: 100%;"><audio style="background-color: black;width: 100%;" controls="controls" autoplay="autoplay" src="' + args[0] + '"></audio></div>');
            }
        } else if (args.length === 2) {
            if (image.indexOf(args[1]) >= 0) {
                dialog.AddContent(' <div style="background-color: black;width: 100%;height: 100%;"><img style="max-width: 100%;max-height: 100%;" src="' + args[0] + '"/></div>');
            } else if (video.indexOf(args[1]) >= 0) {
                dialog.AddContent(' <div style="background-color: black;width: 100%;height: 100%;"><video  style="background-color: black;width: 100%;height: 100%;" controls="controls" autoplay="autoplay" src="' + args[0] + '"></video></div>');
            } else if (audio.indexOf(args[1]) >= 0) {
                dialog.AddContent(' <div style="min-width: 300px;background-color: black;width: 100%;height: 100%;"><audio style="background-color: black;width: 100%;" controls="controls" autoplay="autoplay" src="' + args[0] + '"></audio></div>');
            }
        }
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
        dialog.AddRow( "City", "<input type= 'text '  style= 'width:100%;box-sizing: border-box; ' name= 'city' />");
        dialog.AddRow( "Zip Code", "<input type= 'text '  style= 'width:100%;box-sizing: border-box; ' name= 'zip' />");
        dialog.AddRow( "Country", "<input type= 'text '  style= 'width:100%;box-sizing: border-box; ' name= 'country' />");
        return dialog;
    }
 
    PleaseWait() {
        var dialog = this.Dialog();     
        dialog.AddContent("<div style='cursor:wait;'>Please Wait</div>"); 
        dialog.DestroyAfterClose();
        return dialog;        
    }
  
    Prompt(callback) {
        var dialog = this.Dialog();
        dialog.Title("Prompt");
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
     
    RowCol(callback) {       
        var dialog = this.TwoRow(function (v) {
            return callback(v);
        });
        dialog.Title("Row/Column");
        dialog.AddRow("Row:", "<input type='number'  style='width:100%;box-sizing: border-box;' name='row' />");
        dialog.AddRow("Column:", "<input type='number'  style='width:100%;box-sizing: border-box;' name='column' />");     
        return dialog;
         
    }
    
    Quiz(question, callback) {
          var dialog = this.TwoRow(function (v) {
            return callback(v);
        });
        dialog.AddRadioButton = function (name,txt, value) {
            this.AddRow( '<input type="radio" name="'+name+'"  style="box-sizing: border-box;" value="' + value + '" />'+txt);
            return this;
        };
        dialog.AddSelectOption = function (name,txt, value) {
           var option=""; 
            for (var k in value) {
                var opt = document.createElement("OPTION");
                opt.value = k
                opt.innerHTML = value[k];
                option=option+opt.outerHTML;
            }
            this.AddRow(txt+'<select name="'+name+'">'+option+'</select>' );
            return this;
        };
        dialog.Title(question);     
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

    Size(callback) {
           var dialog = this.TwoRow(function (v) {
            return callback(v);
        });
        dialog.Title("Size");
        dialog.AddRow("width:", "<input type='number'  style='width:100%;box-sizing: border-box;' name='width' />");
        dialog.AddRow("height:", "<input type='number'  style='width:100%;box-sizing: border-box;' name='height' />");       
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
                    } else if(["radio", "checkbox"].indexOf(dom.type)==-1)   {
                        output[dom.name] = dom.value;
                    }                    
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
    YesNoCancel(txt,callback) {
        var dialog = this.Dialog();
        dialog.AddButton(1, "Yes");
        dialog.AddButton(-1, "No");
        dialog.AddButton(0, "Cancel");
        dialog.AddContent(txt);
        dialog.DestroyAfterClose();
        dialog.CallBack = callback;
        return dialog;
    }
         
}

