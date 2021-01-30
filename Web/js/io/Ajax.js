class Ajax {
    
    Get(...args) {
        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function () {
            if (this.readyState === 4 && this.status === 200) {
                if (args.length === 2 && typeof args[1] === "function") {
                    args[1](xhttp.responseText);
                } else if (args.length === 3 && typeof args[2] === "function") {
                    args[2](xhttp.responseText);
                }
            }
        };

        if (args.length === 2) {
            xhttp.open("GET", args[0], true);
            xhttp.send();
        } else if (args.length === 3) {
            xhttp.open("GET", args[0] + this.JsonToQueryString(args[1]), true);
            xhttp.send( );
        }
    }
    Post(...args) {
        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function () {
            if (this.readyState === 4 && this.status === 200&&args.length===3) {
                args[2](this.responseText);
            }
        };
        xhttp.open("POST", args[0], true);
        xhttp.send(this.JSONToFormData(args[1]));
    }

    JSONToFormData(_obj) {
        var fd = new FormData();
        var callback = function (obj, formdata, collectionstring) {
            collectionstring = collectionstring || "";
            var datatypelist = ["boolean", "number", "string"];
            for (var property in obj) {
                if (obj.hasOwnProperty(property)) {
                    var tf = typeof obj[property];
                    if (tf === "object" && Object.keys(obj[property]).length > 0) {
                        callback(obj[property], formdata, collectionstring + '.' + property);
                    } else if (tf === "object" && obj[property]  instanceof File) {
                        var strmix = (collectionstring + '.' + property).slice(1).split('.').join('][') + "]";
                        strmix = strmix.replace("]", "");
                        fd.append(strmix, obj[property], obj[property].name);
                    } else if (datatypelist.indexOf(tf) >= 0) {
                        var strmix = (collectionstring + '.' + property).slice(1).split('.').join('][') + "]";
                        strmix = strmix.replace("]", "");
                        fd.append(strmix, obj[property]);
                    }
                }
            }
        };
        callback(_obj, fd);
        return fd;
    }
    JsonToQueryString(json) {
        if (json === undefined || json === null)
            return "";
        return '?' +
                Object.keys(json).map(function (key) {
            return (key) + '=' +
                    (json[key]);
        }).join('&');
    }

}

class AjaxScrollBar {

    constructor(url, param) {
        this.mutex = false;//lock
        this.url = url;
        this.param = param || {};
    }
    AddScrollEvent(callback) {
        var ref = this;
        this.callback = callback;
        window.oldscrollevent = window.onscroll || function () {};
        window.onscroll = function () {
            this.oldscrollevent();
            var h = window.innerHeight + window.scrollY;
            var offset = document.body.offsetHeight;
            if (h >= offset) {
                ref.LoadAjax();
            }
        };
        this.LoadAjax();
    }
    JSONToFormData(_obj) {
        var fd = new FormData();
        var callback = function (obj, formdata, collectionstring) {
            collectionstring = collectionstring || "";
            var datatypelist = ["boolean", "number", "string"];
            for (var property in obj) {
                if (obj.hasOwnProperty(property)) {
                    var tf = typeof obj[property];
                    if (tf === "object" && Object.keys(obj[property]).length > 0) {
                        callback(obj[property], formdata, collectionstring + '.' + property);
                    } else if (tf === "object" && obj[property]  instanceof File) {
                        var strmix = (collectionstring + '.' + property).slice(1).split('.').join('][') + "]";
                        strmix = strmix.replace("]", "");
                        fd.append(strmix, obj[property], obj[property].name);
                    } else if (datatypelist.indexOf(tf) >= 0) {
                        var strmix = (collectionstring + '.' + property).slice(1).split('.').join('][') + "]";
                        strmix = strmix.replace("]", "");
                        fd.append(strmix, obj[property]);
                    }
                }
            }
        };
        callback(_obj, fd);
        return fd;
    }
    LoadAjax() {
        if (!this.mutex) {
            this.mutex = true;
            var xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function () {
                if (this.readyState === 4 && this.status === 200) {
                    if (typeof this.ref.callback === "function") {
                        this.ref.callback(xhttp.responseText);
                    }
                    this.ref.mutex = false;
                }
            };
            xhttp.open("POST", this.url, true);
            var formdata = this.JSONToFormData(this.param);
            xhttp.send(formdata);
            xhttp.ref = this;
        }
    }
    Param(...args) {
        if (args.length === 0) {
            return this.param;
        } else if (args.length === 1) {
            this.param = args[0];
        } else if (args.length === 2) {
            this.param[args[0]] = args[1];
        }
    }

}
