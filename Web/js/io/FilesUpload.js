class FilesUpload {
    constructor(url, fileparam, startparam, processparam, endparam, abort, param) {
        this.chunksize = 8092 * 8;
        this.url = url;
        this.fileparam = fileparam;
        this.startparam = startparam;
        this.processparam = processparam;
        this.endparam = endparam;
        this.abort = abort;
        this.param = param;
    }
    SetFiles() {

    }
    SendOneFile() {
        var reader = new FileReader();

    }
    Log() {

    }
}
//https://developer.mozilla.org/en-US/docs/Web/API/XMLHttpRequest/Using_XMLHttpRequest
class FilesUpload_Ajax {
    constructor() {
        this.xmlhttp = new XMLHttpRequest();
    }
    Abort() {
        this.xmlhttp.abort();
    }
    /*     xmlhttp.onloadend = function () {
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
     
     };
     */












    Complete() {

    }
    Error() {

    }
    Send(url, formdata) {
        this.xmlhttp.open("POST", url);
        this.xmlhttp.send(formdata);
    }
}

class FilesUpload_Callback {
    Complete() {

    }
    Error() {

    }
}












function FilesUpload() {
    var Method = {};

    Method.URL = "";
    Method.PostName = "Upload";
    Method.PostJson = {};

    Method.BeforeUpload = function (cb) {

    };
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
    Method.Queue = function () {
        var subm = {};
        subm.Start = 0;
        subm.End = 0;
        subm.Complete = function (cb) {

        };
        subm.ExecFunction = function (cb) {

        };
        subm.Progress = function ( ) {
            return subm.Start / subm.End;
        };
        subm.Next = function () {
            if (subm.Start < subm.End) {
                subm.ExecFunction(subm.Start);
                subm.Start = subm.Start + 1;
            } else if (subm.Start == subm.End) {
                subm.Complete();
                subm.Start = subm.Start + 1;
            }
        };
        return subm;
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
                    var reader = new FileReader();
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
}