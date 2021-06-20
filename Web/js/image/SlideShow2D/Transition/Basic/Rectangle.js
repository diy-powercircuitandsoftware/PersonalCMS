class SlideShow2D_Transition_BottomToTop extends SlideShow2D_Fill_Transition {
    Initialization() {
        this.ReDrawingImageA = false;
    }
    Template(time) {
        return {
            "command": "rect",
            "args": [
                0, this.canvassize.height * (1 - time),
                this.canvassize.width, this.canvassize.height * time
            ]

        };
    }
    ;
}
;

class SlideShow2D_Transition_CornerLeftToRight extends SlideShow2D_Fill_Transition {
    Initialization() {
        this.ReDrawingImageA = false;
    }
    Template(time) {
        return {
            "command": "rect",
            "args": [
                0, 0,
                Math.round(this.canvassize.width * time),
                Math.round(this.canvassize.height * time)
            ]

        };
    }
    ;
}
;

class SlideShow2D_Transition_FromHorizontalCenter extends SlideShow2D_Fill_Transition {
    Initialization() {
        this.halfheight = this.canvassize.height / 2;
        this.ReDrawingImageA = false;
    }
    Template(time) {
        return [{
                "command": "rect",
                "args": [0, this.halfheight, this.canvassize.width, this.halfheight * time]
            }, {

                "command": "rect",
                "args": [0, this.halfheight * (1 - time), this.canvassize.width, this.halfheight * time]
            }];
    }
}
;

class SlideShow2D_Transition_FromVerticalCenter extends SlideShow2D_Fill_Transition {
    Initialization() {
        this.halfwidth = this.canvassize.width / 2;
        this.ReDrawingImageA = false;
    }
    Template(time) {
        return [
            {
                "command": "rect",
                "args": [this.halfwidth, 0, this.halfwidth * time, this.canvassize.height]
            },
            {
                "command": "rect",
                "args": [this.halfwidth * (1 - time), 0, this.halfwidth * time, this.canvassize.height]
            }
        ];
    }
    ;
}
;
class SlideShow2D_Transition_LeftToRight extends SlideShow2D_Fill_Transition {
    Initialization() {
        this.ReDrawingImageA = false;
    }
    Template(time) {
        return [{
                "command": "rect",
                "args": [0,
                    0,
                    this.canvassize.width * time,
                    this.canvassize.height]

            }];
    }
}
;
class SlideShow2D_Transition_RightToLeft extends SlideShow2D_Fill_Transition {
    Initialization() {
        this.ReDrawingImageA = false;
    }
    Template(time) {
        return {
            "command": "rect",
            "args": [
                Math.round(this.canvassize.width * (1 - time)), 0,
                this.canvassize.width * time, this.canvassize.height
            ]

        };
    }
    ;
}
;
class SlideShow2D_Transition_ToHorizontalCenter extends SlideShow2D_Fill_Transition {
    Initialization() {
        this.halfheight = this.canvassize.height / 2;
        this.ReDrawingImageA = false;
    }
    Template(time) {
        return [{
                "command": "rect",
                "args": [0, 0, this.canvassize.width, this.halfheight * time]
            }, {
                "command": "rect",
                "args": [0, this.halfheight + (this.halfheight * (1 - time)),
                    this.canvassize.width, this.halfheight * time]
            }];
    }
}
;
class SlideShow2D_Transition_TopToBottom extends SlideShow2D_Fill_Transition {
    Initialization() {
        this.ReDrawingImageA = false;
    }
    Template(time) {
        return [{
                "command": "rect",
                "args": [0,
                    0,
                    this.canvassize.width,
                    this.canvassize.height * time]

            }];
    }
}
;
class SlideShow2D_Transition_ToVerticalCenter extends SlideShow2D_Fill_Transition {
    Initialization() {
        this.halfwidth = this.canvassize.width / 2;
        this.ReDrawingImageA = false;
    }
    Template(time) {

        return [{
                "command": "rect",
                "args": [
                    this.halfwidth + (this.halfwidth * (1 - time)),
                    0,
                    this.halfwidth * time,
                    this.canvassize.height]
            }, {
                "command": "rect",
                "args": [
                    0,
                    0,
                    this.halfwidth * time,
                    this.canvassize.height]
            }];
    }
}
;

