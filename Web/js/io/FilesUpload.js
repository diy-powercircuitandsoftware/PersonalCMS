class FilesUpload {
    constructor(variables, param) {

        this.chunksize = 8192 * 128;
        this.variables = variables;
        this.param = param;
        this.mutex = false;
        this.filesindex = 0;
        this.filescount = 0;
        this.filereader = new FilesUpload_Reader();
        var ajax = new FilesUpload_Ajax();

        var variables = this.variables;
        var chunksize = this.chunksize;
        ajax.ref = this;
        ajax.Complete(function () {
            if (!this.ref.filereader.fd_complete) {
                this.ref.filereader.Read(chunksize);
            } else if (this.ref.filereader.fd_complete) {
                this.ref.filesindex++;
                if (this.ref.filesindex < this.ref.filescount) {
                    this.ref.filereader.SetFile(this.ref.files[ this.ref.filesindex]);
                    this.ref.filereader.Read(this.ref.chunksize);
                }
            }
        });
        ajax.Progress(function(v){
           
            var spfile= (this.ref.filereader.Tell()/this.ref.filereader.Size())*100;
            var all=((this.ref.filesindex+1)/this.ref.filescount)*100;
            this.ref.Log({"AjaxProgress":v,"FileProgress":spfile,"AllProgress":all});
        });
        this.filereader.GetUint8Array(function (f) {
            var fd = new FormData();
            fd.append(variables["files"], new File([f], this.name));
            fd.append("header", 206);
            this.fd_complete = false;
            ajax.Send(variables["url"], fd);
        });
        this.filereader.EndOfFile(function (f) {
            var fd = new FormData();
            fd.append(variables["files"], this.name);
            fd.append("header", 200);
            this.fd_complete = true;
            ajax.Send(variables["url"], fd);

        });

    }

    SetFiles(files) {
        this.files = files;
        this.filesindex = 0;
        this.filescount = this.files.length;

    }

    SetParam(key, val) {
        this.param[key] = val;
    }

    Send() {
        //variables["files"
        //variables["url"


        if (!this.mutex && this.filescount > 0) {
            this.mutex = true;
            this.filereader.SetFile(this.files[ this.filesindex]);
            this.filereader.Read(this.chunksize);
        } else {
            this.Log({"Error": "progressing"});
        }
    }
    Log(cb) {
        if (typeof cb === "function") {
            this.Log = cb;
        }
    }

}

class FilesUpload_Ajax {
    constructor() {
        this.xmlhttp = new XMLHttpRequest();
        this.xmlhttp.addEventListener("load", function (e) {
            this.ref.Complete(e);
        });
        this.xmlhttp.addEventListener("progress", function (e) {
            var pg = e.loaded / e.total * 100;
            this.ref.Progress(pg);
        });
        this.xmlhttp.addEventListener("error", function (e) {
            this.ref.Progress(0);
            this.ref.Error(e);
        });
        this.xmlhttp.addEventListener("abort", function (e) {
            this.ref.Progress(0);
            this.ref.Error(e);
        });
        this.xmlhttp.ref = this;
    }
    Abort() {
        this.xmlhttp.abort();
    }

    Complete(cb) {
        if (typeof cb === "function") {
            this.Complete = cb;
        }
    }
    Error(cb) {
        if (typeof cb === "function") {
            this.Error = cb;
        }
    }

    Progress(cb) {
        if (typeof cb === "function") {
            this.Progress = cb;
        }
    }
    Send(url, formdata) {
        this.xmlhttp.open("POST", url);
        this.xmlhttp.send(formdata);
    }
}
class FilesUpload_Reader {

    constructor() {
        this.file = null;
        this.name = "";
        this.startbytes = 0;

    }
    EndOfFile(cb) {
        if (typeof cb === "function") {
            this.EndOfFile = cb;
            this.startbytes = 0;

        }
    }
    GetUint8Array(cb) {
        if (typeof cb === "function") {
            this.GetUint8Array = cb;
        }
    }

    Read(length) {
        var reader = new FileReader();
        reader.addEventListener('loadend', function (evt) {
            var arraybuffer = evt.target.result;
            this.ref.startbytes = this.ref.startbytes + arraybuffer.byteLength;
            if (arraybuffer.byteLength > 0) {
                this.ref.GetUint8Array(new Uint8Array(arraybuffer));

            } else {
                this.ref.EndOfFile();
            }
        });
        reader.readAsArrayBuffer(this.file.slice(this.startbytes, this.startbytes + length));
        reader.ref = this;
    }
    SetFile(file) {
        this.file = file;
        this.name = this.file.name;
        this.startbytes = 0;
    }
    Size() {
        return this.file.size;
    }
    Tell() {
        return this.startbytes;
    }
}

 