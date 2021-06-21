class SlideShow2D_Transition_Polygons extends SlideShow2D_Fill_Transition {

    Initialization() {
        this.MinCanvasSize = Math.max(this.canvassize.width, this.canvassize.height) * 1.2;
        this.ReDrawingImageA = false;
        this.Dot=3;
    }
    Template(time) {

        return {
            "command": "Polygons",
            "extends": true,
            "args": [
                (this.canvassize.width / 2),
                this.canvassize.height / 2,
                this.MinCanvasSize * time,
                 this.Dot
            ]

        };
    }
}
;
