class SlideShow2D_Transition_CircleIn extends SlideShow2D_Fill_Transition {
    Initialization() {
        this.MinCanvasSize = Math.min(this.canvassize.width, this.canvassize.height);
        this.ImageA = 2;
        this.ImageB = 1;
    }
    Template(time,tick) {
        return {
            "command": "arc",
            "args": [
                this.canvassize.width / 2,
                this.canvassize.height / 2,
                this.MinCanvasSize * (1 - time),
                0, 2 * Math.PI, false
            ]

        };
    }
}
;


class SlideShow2D_Transition_CircleOut extends SlideShow2D_Fill_Transition {
    Initialization() {
        this.MinCanvasSize = Math.min(this.canvassize.width, this.canvassize.height);
        this.ReDrawingImageA = false;
    }
    Template(time,tick) {
        return {
            "command": "arc",
            "args": [
                this.canvassize.width / 2,
                this.canvassize.height / 2,
                this.MinCanvasSize * time,
                0, 2 * Math.PI, false
            ]

        };
    }
}
;
