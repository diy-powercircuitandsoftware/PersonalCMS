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
        this.ReDrawingImageA = false;

    }
    Template(time,tick) {

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
class SlideShow2D_Transition_BubbleMosaic extends SlideShow2D_Fill_Transition {
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
        this.ReDrawingImageA = false;
        this.Index = 0;
        this.MaxIndex = this.Mosaic.length;
        this.PI2 = 2 * Math.PI;
        this.HalfTiles = this.Tiles / 2;
        this.BubbleSize=this.Tiles;
    }
    Template(time,tick) {

        var progress = this.Index / this.MaxIndex;
        var out = [];
        while (progress < time) {// time calibration 
            var mosaic = this.Mosaic.splice(this.Mosaic.length * Math.random() | 0, 1)[0];
            var ht = this.Tiles / 2;
            if (mosaic !== undefined) {
                out.push({
                    "command": "arc",
                    "args": [
                        mosaic.x + this.HalfTiles ,
                        mosaic.y + this.HalfTiles ,
                        this.BubbleSize,
                        0, this.PI2]
                });
            }
            this.Index++;
            progress = this.Index / this.MaxIndex;
        }

        return out;

    }
}
;