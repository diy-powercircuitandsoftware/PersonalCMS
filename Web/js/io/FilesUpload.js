class FilesUpload {
    constructor(variables, param) {
        this.chunksize = 8192 * 128;
        this.variables = variables;
        this.param = param;
        this.mutex = false;
    }
    SetFiles(files) {
        this.files = files;
    }
    Send() {
        if (!this.mutex) {

            this.mutex = true;
            var filereader = new FilesUpload_Reader();
            var ajax = new FilesUpload_Ajax();
            ajax.ref=this;
            var variables = this.variables;
            var chunksize = this.chunksize;
            ajax.Complete(function () {
                
                if (!filereader.fd_complete) {
                    
                    filereader.Read(chunksize);
                }
                else{
                    
                }
            });
            filereader.GetUint8Array(function (f) {
                var fd = new FormData();
                fd.append(variables["files"], new File([f], this.name));
                fd.append("header", 206);
                this.fd_complete = false;
                ajax.Send(variables["url"], fd);
                 
            });
            filereader.EndOfFile(function (f) {
               
                var fd = new FormData();
                fd.append(variables["files"], this.name);
                fd.append("header", 200);
                this.fd_complete = true;
                ajax.Send(variables["url"], fd);

            });
            
            filereader.SetFile(this.files);
            filereader.Read(chunksize);
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
        this.xmlhttp.addEventListener("load", function(e){
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

}












/*
 function FilesUpload() {
 var Method = {};
 
 Method.URL = "";
 Method.PostName = "Upload";
 Method.PostJson = {};
 
 Method.Complete = function (cb) {
 
 };
 Method.GetCurrentFileName = function (cb) {
 
 };
 Method.OverallProgress = function (cb) {
 
 };
 Method.UploadProgress = function (cb) {
 
 };
 Method.ByteTransferProgress = function (cb) {
 
 };
 Method.Abort = function ( ) {
 
 };
 Method.Error = function (cb) {
 
 };
 
 
 Method.Upload = function (FileArray) {
 if (Object.prototype.toString.call(FileArray) == "[object FileList]") {
 Method.UploadProgress(0);
 Method.OverallProgress(0);
 Method.BeforeUpload();
 var FilesQueue = new Method.Queue();
 FilesQueue.End = FileArray.length;
 FilesQueue.ExecFunction = function (i) {
 var CurrentFile = FileArray[i]
 var QueueFile = Method.Queue();
 QueueFile.End = Math.ceil(CurrentFile.size / Method.ChunkSize) + 1;
 Method.GetCurrentFileName(CurrentFile.name);
 QueueFile.ExecFunction = function (i) {
 
 var start = i * Method.ChunkSize;
 var stop = start + Method.ChunkSize;
 var blob = CurrentFile.slice(start, stop);
 reader.onloadend = function (evt) {
 var xmlhttp = new XMLHttpRequest();
 var fd = new FormData();
 var bufferdata = (evt.target.result);
 var ajaxfile = new File([bufferdata], CurrentFile.name);
 for (var k in  Method.PostJson) {
 fd.append(k, Method.PostJson[k]);
 }
 fd.append("complete", ajaxfile.size == 0);
 fd.append(Method.PostName, ajaxfile);
 xmlhttp.onloadend = function () {
 if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
 Method.UploadProgress(QueueFile.Progress());
 QueueFile.Next();
 } else if (xmlhttp.readyState == 4 && xmlhttp.status >= 400 && xmlhttp.status < 600) {
 Method.UploadProgress(QueueFile.Progress(0));
 Method.Error(xmlhttp.responseText);
 }
 };
 xmlhttp.upload.onprogress = function (e) {
 var progress = Math.ceil(e.loaded / e.total);
 Method.ByteTransferProgress(progress);
 };
 xmlhttp.onerror = function () {
 Method.Error("Ajax Error");
 };
 xmlhttp.onabort = function () {
 Method.Error("User Abort:" + CurrentFile.name);
 };
 Method.Abort = function () {
 xmlhttp.abort();
 };
 xmlhttp.open("POST", Method.URL);
 xmlhttp.send(fd);
 
 };
 reader.readAsArrayBuffer(blob);
 };
 QueueFile.Complete = function () {
 Method.OverallProgress(FilesQueue.Progress());
 FilesQueue.Next();
 };
 QueueFile.Next();
 };
 FilesQueue.Complete = function () {
 Method.OverallProgress(1);
 Method.GetCurrentFileName("");
 Method.Complete();
 };
 FilesQueue.Next();
 }
 
 };
 
 return Method;
 }*/