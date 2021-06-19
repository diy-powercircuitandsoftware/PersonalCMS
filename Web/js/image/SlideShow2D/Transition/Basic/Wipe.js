class SlideShow2D_Transition_RectWipe extends SlideShow2D_Fill_Transition {
    Initialization() {

        this.CountOFTiles = 12;
        this.Tiles = Math.max(this.canvassize.width, this.canvassize.height) / this.CountOFTiles;
        this.Starty = 0;
        this.Startx = - this.Tiles;
        this.Index = 0;
        this.MaxIndex = this.CountOFTiles * this.CountOFTiles;
    }
    Template(time) {

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