class SlideShow2D_Transition_Mosaic extends SlideShow2D_Fill_Transition {
    Initialization() {

        this.CountOFTiles = 12;
        this.Tiles = Math.max(this.canvassize.width, this.canvassize.height) / this.CountOFTiles;
        this.Mosaic = [];
        for (var i = 0; i <= this.CountOFTiles; i++) {
            for (var j = 0; j <= this.CountOFTiles; j++) {
                this.Mosaic.push({
                    "x": j * this.Tiles,
                    "y": i * this.Tiles
                });
            }
        }

        this.Index = 0;
        this.MaxIndex = this.Mosaic.length;

    }
    Template(time) {

        var progress = this.Index / this.MaxIndex;
        var out = [];
        while (progress < time) {// time calibration 
            var mosaic = this.Mosaic.splice(this.Mosaic.length * Math.random() | 0, 1)[0];
            if (mosaic !== undefined) {
                out.push({
                    "command": "rect",
                    "args": [
                        mosaic.x,
                        mosaic.y,
                        this.Tiles,
                        this.Tiles]
                });
            }
            this.Index++;
            progress = this.Index / this.MaxIndex;
        }

        return out;

    }
}
;