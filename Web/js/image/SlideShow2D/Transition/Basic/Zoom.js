/*class SlideShow2D_Transition_ZoomInOut extends SlideShow2D_Fill_Transition {
    Start() {
        this.CenterA = this.Center(this.image1size, this.canvassize, this.scale(this.image1size, this.canvassize));
        this.CenterB = this.Center(this.image2size, this.canvassize, this.scale(this.image2size, this.canvassize));
        this.CX = this.canvassize.width / 2;
        this.CY = this.canvassize.height / 2;
    }

    Running(time) {
        var stack = [];
        var progress = 0;
        var inout = "";
        stack.push({
            "command": "save"
        }, {
            "command": "clearRect",
            "x": 0,
            "y": 0,
            "width": this.canvassize.width,
            "height": this.canvassize.height

        });


        if (time < 0.2) {
            progress = (time - 0) / 0.2;
            inout = "i";
        } else if (time > 0.2 && time < 0.4) {
            progress = (time - 0.2) / 0.2;
            inout = "o";
        } else if (time > 0.4 && time < 0.6) {
            inout = "";
        } else if (time > 0.6 && time < 0.8) {
            progress = (time - 0.6) / 0.2;
            inout = "i";
        } else if (time > 0.8) {
            progress = (time - 0.8) / 0.2;
            inout = "o";
        }
        stack.push({
            "command": "translate",
            "x": this.CX,
            "y": this.CY
        });

        var size = 3 * progress;
        var invsize = 3 * (1 - progress);
        if (inout === "i" && size > 1) {
            stack.push({
                "command": "scale",
                "x": size,
                "y": size
            });
        } else if (inout === "o" && invsize > 1) {

            stack.push({
                "command": "scale",
                "x": invsize,
                "y": invsize
            });
        }
        stack.push({
            "command": "translate",
            "x": -this.CX,
            "y": -this.CY
        });
        if (time < 0.5) {
            stack.push({
                "command": "DrawImage",
                "image": 1,
                "src": this.Rect(0, 0, this.image1size.width, this.image1size.height),
                "dest": this.CenterA
            });
        } else if (time > 0.5) {
            stack.push({
                "command": "DrawImage",
                "image": 2,
                "src": this.Rect(0, 0, this.image2size.width, this.image2size.height),
                "dest": this.CenterB
            });
        }


        stack.push({
            "command": "restore"
        });


        return stack;
    }

}
;
*/