class SlideShow2D_Transition_RectWipe extends SlideShow2D_Fill_Transition {
    Initialization() {

        this.CountOFTiles = 12;
        this.Tiles = Math.max(this.canvassize.width, this.canvassize.height) / this.CountOFTiles;
        this.Starty = 0;
        this.Startx = -this.Tiles;
        this.Index = 0;
        this.MaxIndex = this.CountOFTiles * this.CountOFTiles;
        this.ReDrawingImageA = false;
    }
    Template(time, tick) {

        var progress = this.Index / this.MaxIndex;
        var out = [];
        while (progress < time) {// time calibration 

            if (this.Startx > this.canvassize.width) {
                this.Starty = this.Starty + this.Tiles;
                this.Startx = 0;
            } else {
                this.Startx = this.Startx + this.Tiles;
            }
            out.push({
                "command": "rect", "args": [
                    this.Startx,
                    this.Starty,
                    this.Tiles,
                    this.Tiles]
            });
            this.Index++;
            progress = this.Index / this.MaxIndex;
        }

        return out;
    }
}
;


class SlideShow2D_Transition_Wiper_LeftToRight extends SlideShow2D_Fill_Transition {
    Initialization() {
        this.MinCanvasSize = Math.max(this.canvassize.width, this.canvassize.height) * 1.2;
        this.halfw = this.canvassize.width / 2;
    }
    Template(time, tick) {
        return [{
                "command": "arc",
                "args": [
                    this.halfw,
                    this.canvassize.height,
                     this.MinCanvasSize,
                    Math.PI,
                    (1.0 + (1 * time)) * Math.PI
                ]
            },{
                "command": "lineTo",
                 "args": [ this.halfw,this.canvassize.height]
            }];
    }
}
;


 