class SuperDialog_Template_Multimedia extends  SuperDialog {

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
}