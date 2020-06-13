class SSQueryFW {
    S(qstring) {
        if (typeof qstring === 'string' || qstring instanceof String) {
            this.element = document.querySelectorAll(qstring);
        }
        return this;
    }

    Append(...args) {
        if (args.length === 1) {
            if (typeof args[0] === 'string') {
                this.ForEach(this.element, function (el) {
                    el.innerHTML = el.innerHTML + args[0];
                });
            }

        } else if (args.length === 2) {
            this.ForEach(this.element, function (el) {
                if (el.tagName === "SELECT") {
                    var opt = el.appendChild(document.createElement("OPTION"));
                    opt.value = args[0];
                    opt.innerHTML = args[1];
                }
            });
        }
        return this;
    }
    Change(...args) {
        this.EventListener("change", ...args);
        return  this;
    }
    Click(...args) {
        this.EventListener("click", ...args);
        return  this;
    }
    Data(...args) {
        if (args.length === 1 && (typeof args[0] === 'string' || args[0] instanceof String)) {
            var output = [];
            this.ForEach(this.element, function (el) {
                output.push(el.getAttribute("data-") + args[0]);
            });
            if (output.length === 1) {
                return output[0];
            }
            return output;
        } else if (args.length === 1 && (args[0] instanceof Object)) {
            this.ForEach(this.element, function (el) {
                for (var k in args[0]) {
                    el.setAttribute("data-" + k, args[0][k]);
                }
            });
        } else if (args.length === 2) {
            this.ForEach(this.element, function (el) {
                el.setAttribute("data-" + args[0], args[1]);
            });
        }
    }
     Disable(bool){
        
    }
    DocumentReady(callback) {
        document.addEventListener('DOMContentLoaded', callback);
    }
    Empty() {
        this.ForEach(this.element, function (el) {
            el.innerHTML = "";
        });
    }
    EventListener(...args) {
        var eventName = args[0];
        if (args.length === 1) {
            this.ForEach(this.element, function (el) {
                if (eventName === "click") {
                    el.click();
                } else {
                    var event = new CustomEvent(eventName);
                    el.dispatchEvent(event);
                }
            });
        } else if (args.length === 2 || typeof args[1] === "function") {
            var handler = args[1];
            this.ForEach(this.element, function (el) {
                el.addEventListener(eventName, handler);
            });
        }
        return  this;
    }
    ForEach(array, callback) {
        if (array instanceof Array) {
            for (var i = 0; i < array.length; i++) {
                callback(array[i]);
            }
        } else if (array instanceof NodeList) {
            for (var i = 0; i < array.length; i++) {
                callback(array[i]);
            }
        } else if (array instanceof Object) {
            for (var k in array) {
                allback(array[k]);
            }
        }
    }
    GeoLocation(callback) {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function (position) {
                callback({
                    "latitude": position.coords.latitude,
                    "longitude": position.coords.longitude
                });
            });
        }
    }
    Hide() {
        this.ForEach(this.element, function (el) {
            el.style.display = "none";
        });
    }
    Html(...args) {
        if (args.length === 0) {
            var html = "";
            this.ForEach(this.element, function (el) {
                html = html + el.innerHTML;
            });
            return html;
        } else if (args.length === 1) {
            this.ForEach(this.element, function (el) {
                el.innerHTML = args[0];
            });
            return this;
        }
    }
    InArray(string, array) {
        return    array.indexOf(string) >= 0;
    }
    Input(...args) {
        this.EventListener("input", ...args);
        return  this;
    }
    IsFloat(n) {
        return Number(n) === n && n % 1 !== 0;
    }
    IsInt(n) {
        return Number(n) === n && n % 1 === 0;
    }
    KeyUp(...args) {
        this.EventListener("keyup", ...args);
        return  this;
    }

    Load(...args) {//test
        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function () {
            if (this.readyState === 4 && this.status === 200) {
                this.ref.ForEach(this.refelement, function (el) {
                    el.innerHTML = xhttp.responseText;
                });
            }
        };
        xhttp.ref = this;
        xhttp.refelement = this.element;
        if (args.length === 1) {
            xhttp.open("GET", args[0], true);
            xhttp.send();
        } else if (args.length === 2) {
            var json = args[1];
            xhttp.open("GET", args[0] + '?' +
                    Object.keys(json).map(function (key) {
                return encodeURIComponent(key) + '=' +
                        encodeURIComponent(json[key]);
            }).join('&'), true);
            xhttp.send( );
        }
    }
   
    Show() {
        var block = ["html", "address", "blockquote", "body", "dd", "div", "dl", "dt", "fieldset", "form", "frame", "frameset", "h1", "h2", " h3", " h4", "h5", " h6", "noframes", "ol", "p", "ul", "center", "dir", "hr", "menu", "pre"];
        var other = {
            "li": "list-item",
            "head": "none",
            "table": "table",
            "tr": "table-row",
            "thead": "table-header-group",
            "tbody": "table-row-group",
            "tfoot": "table-footer-group",
            "col": "table-column",
            "colgroup": "table-column-group",
            "td": "table-cell",
            "th": "table-cell",
            "caption": "table-caption"
        };
        this.ForEach(this.element, function (el) {
            if (window.getComputedStyle(el).getPropertyValue("display") == "none") {
                var tagname = el.tagName.toLowerCase();
                if (block.indexOf(tagname) >= 0) {
                    el.style.display = "block";
                } else if (other.hasOwnProperty(tagname)) {
                    el.style.display = other[tagname];
                }
            } else {
                el.style.display = "";
            }
        });
        return this;
    }
    Sprintf(...args) {
        if (args.length === 1) {
            return args[0];
        } else {
            var str = args[0];
            for (var i = 1; i < args.length; i++) {
                if (this.IsInt(args[i])) {
                    str = str.replace("%d", args[i]);
                } else if (this.IsFloat(args[i])) {
                    str = str.replace("%f", args[i]);
                } else {
                    str = str.replace("%s", args[i]);
                }
            }
            return str;
        }
    }
    StrLeft(str, length) {
        return str.substring(0, length);
    }
    StrMid(...args) {
        var str = args[0];
        var index = args[1];
        if (args.length === 2) {
            return str.substring(index - 1);
        } else if (args.length === 3) {
            return str.substring(index - 1, index + args[2] - 1);
        }
    }
    StrRight(str, length) {
        return str.substring(str.length() - length);
    }

    Val(...args) {
        var arrchk = ["checkbox", "radio"];
        var hassrc = ["video", "audio", "img"];
        if (args.length === 0) {
            var output = [];
            this.ForEach(this.element, function (el) {
                if (el.tagName === "INPUT" && arrchk.indexOf(el.type) >= 0 && el.checked && el.value === undefined) {
                    output.push(el.checked);
                } else if (el.tagName === "INPUT" && arrchk.indexOf(el.type) >= 0 && el.checked) {
                    output.push(el.value);
                } else if (el.tagName === "INPUT" && el.type === "file") {
                    output.push(el.files);
                } else if (el.tagName === "TEXTAREA") {
                    output.push(el.value);
                } else if (el.tagName === "SELECT" && el.getAttribute("multiple") == "multiple") {
                    for (var i = 0; i < el.options.length; i++) {
                        var option = el.options[i];
                        if (option.selected) {
                            output.push(option.value);
                        }
                    }
                } else if (hassrc.indexOf(el.tagName) >= 0) {
                    output.push(el.src);
                } else if (arrchk.indexOf(el.type) === -1) {
                    output.push(el.value);
                }
            });
            if (output.length === 1) {
                return output[0];
            }
            return output;
        } else {

            this.ForEach(this.element, function (el) {
                if (el.tagName === "INPUT" && arrchk.indexOf(el.type) >= 0 && typeof (args[0]) === "boolean") {
                    el.checked = args[0];
                } else if (el.tagName === "INPUT" && el.type == "date") {
                    if (args[0] instanceof Date) {
                        el.valueAsDate = args[0];
                    }
                } else if (hassrc.indexOf(el.tagName) >= 0) {
                    el.src = args[0];
                } else {
                    el.value = args[0];
                }
            });
            return  this;
        }
    }
    ValByName(...args) {
        var arrchk = ["checkbox", "radio"];
        var hassrc = ["VIDEO", "AUDIO", "IMG"];
        if (args.length === 0) {
            var output = {};
            this.ForEach(this.element, function (el) {
                if (el.getAttribute("name") !== undefined) {
                    var name = el.getAttribute("name");
                    if (el.tagName === "INPUT" && arrchk.indexOf(el.type) >= 0 && el.checked) {
                        output[name] = (el.checked);
                    } else if (el.tagName === "INPUT" && el.type == "file") {
                        output[name] = el.files;
                    } else if (el.tagName === "SELECT" && el.getAttribute("multiple") == "multiple") {
                        output[name] = [];
                        for (var i = 0; i < el.options.length; i++) {
                            var option = el.options[i];
                            if (option.selected) {
                                output[name].push(option.value);
                            }
                        }
                    } else if (hassrc.indexOf(el.tagName) >= 0) {
                        output[name] = (el.src);
                    } else if (arrchk.indexOf(el.type) === -1) {
                        output[name] = (el.value);
                    }
                }

            });

            return output;
        } else {

            this.ForEach(this.element, function (el) {
                if (el.getAttribute("name") !== undefined) {
                    var name = el.getAttribute("name");
                    if (args[0].hasOwnProperty(name)) {
                        if (el.tagName === "INPUT" && arrchk.indexOf(el.type) >= 0 && typeof (args[0][name]) === "boolean") {
                            el.checked = args[0][name];
                        } else if (hassrc.indexOf(el.tagName) >= 0) {
                            el.src = args[0][name];
                        } else {
                            el.value = args[0][name];
                        }
                    }
                }

            });
            return  this;
        }
    }

}

 