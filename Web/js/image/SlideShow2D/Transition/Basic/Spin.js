
/*class SlideShow2D_Transition_SpinRight extends SlideShow2D_Fill_Transition {
    Initialization() {     
        this.CX = this.canvassize.width / 2;
        this.CY = this.canvassize.height / 2;
    }

    Template(time) {
        var stack = [];
        stack.push({
            "command": "clearRect",
            "x": 0,
            "y": 0,
            "width": this.canvassize.width,
            "height": this.canvassize.height

        });
        if (time < 0.4) {
            stack.push({
                "command": "DrawImage",
                "image": 1,
                "src": this.Rect(0, 0, this.image1size.width, this.image1size.height),
                "dest": this.CenterA
            });
        } else if (time > 0.4 && time < 0.6) {

            stack.push({
                "command": "save",

            }, {
                "command": "translate",
                "x": this.CX,
                "y": this.CY
            }, {
                "command": "Rotate",
                "value": (12 * 360 * time) * Math.PI / 180

            }, {
                "command": "translate",
                "x": -this.CX,
                "y": -this.CY
            }, {
                "command": "DrawImage",
                "image": 2,
                "src": this.Rect(0, 0, this.image2size.width, this.image2size.height),
                "dest": this.CenterB
            }, {
                "command": "restore"

            });
        } else if (time > 0.6) {

            stack.push({
                "command": "DrawImage",
                "image": 2,
                "src": this.Rect(0, 0, this.image2size.width, this.image2size.height),
                "dest": this.CenterB
            });
        }

        return stack;
    }

}
;*/