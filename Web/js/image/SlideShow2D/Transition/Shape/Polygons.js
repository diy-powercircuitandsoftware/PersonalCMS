class SlideShow2D_Transition_PolygonsIn extends SlideShow2D_Fill_Transition {
    Initialization() {
        this.MinCanvasSize = Math.max(this.canvassize.width, this.canvassize.height) * 1.2;
        this.ImageA = 2;
        this.ImageB = 1;
        this.Dot = 1;
    }
    Template(time,tick) {
        return {
            "command": "Polygons",
            "extends": true,
            "args": [
                (this.canvassize.width / 2),
                this.canvassize.height / 2,
                this.MinCanvasSize * (1-time),
                this.Dot
            ]

        };
    }
}
;

class SlideShow2D_Transition_PolygonsOut extends SlideShow2D_Fill_Transition {
    Initialization() {
        this.MinCanvasSize = Math.max(this.canvassize.width, this.canvassize.height) * 1.2;
        this.ReDrawingImageA = false;
        this.Dot = 1;
    }
    Template(time,tick) {

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
class SlideShow2D_Transition_PolygonsOut_Triangle extends SlideShow2D_Transition_PolygonsOut {
    Initialization() {
        super.Initialization();
        this.Dot = 3;
    }
}
;
class SlideShow2D_Transition_PolygonsOut_Square extends SlideShow2D_Transition_PolygonsOut {
    Initialization() {
        super.Initialization();
        this.Dot = 4;
    }
}
;
class SlideShow2D_Transition_PolygonsOut_Pentagon extends SlideShow2D_Transition_PolygonsOut {
    Initialization() {
        super.Initialization();
        this.Dot = 5;
    }
}
;
class SlideShow2D_Transition_PolygonsOut_Hexagon extends SlideShow2D_Transition_PolygonsOut {
    Initialization() {
        super.Initialization();
        this.Dot = 6;
    }
}
;
class SlideShow2D_Transition_PolygonsIn_Triangle extends SlideShow2D_Transition_PolygonsIn {
    Initialization() {
        super.Initialization();
        this.Dot = 3;
    }
}
;
class SlideShow2D_Transition_PolygonsIn_Square extends SlideShow2D_Transition_PolygonsIn {
    Initialization() {
        super.Initialization();
        this.Dot = 4;
    }
}
;
class SlideShow2D_Transition_PolygonsIn_Pentagon extends SlideShow2D_Transition_PolygonsIn {
    Initialization() {
        super.Initialization();
        this.Dot = 5;
    }
}
;
class SlideShow2D_Transition_PolygonsIn_Hexagon extends SlideShow2D_Transition_PolygonsIn {
    Initialization() {
        super.Initialization();
        this.Dot = 6;
    }
}
;