
class SlideShow2D_Transition_StarIn extends SlideShow2D_Fill_Transition {
    Initialization() {
        this.P = 5;
        this.M = 0.5;
        this.MaxCanvasSize = Math.max(this.canvassize.width, this.canvassize.height);
        this.ImageA = 2;
        this.ImageB = 1;
    }
    Template(time,tick) {
        var stack = [];
        var r = this.MaxCanvasSize * (1 - time);
        stack.push({
            "command": "translate",
            "args": [
                this.canvassize.width / 2,
                this.canvassize.height / 2]
        });

        for (var i = 0; i < this.P; i++)
        {
            stack.push({
                "command": "rotate",
                "args": [Math.PI / this.P]
            });
            stack.push({
                "command": "lineTo",
                "args": [0, -(r * this.M)]

            });
            stack.push({
                "command": "rotate",
                "args": [Math.PI / this.P]
            });
            stack.push({
                "command": "lineTo",
                "args": [0, -r]

            });
        }
        stack.push({
            "command": "translate",
            "args": [-this.canvassize.width / 2, -this.canvassize.height / 2]

        });

        return stack;
    }
}
;
class SlideShow2D_Transition_StarOut extends SlideShow2D_Fill_Transition {
    Initialization() {
        this.P = 5;
        this.M = 0.5;
        this.MaxCanvasSize = Math.max(this.canvassize.width, this.canvassize.height);
        this.ReDrawingImageA = false;
    }
    Template(time,tick) {
        var stack = [];
        var r = this.MaxCanvasSize * time;
        stack.push({
            "command": "translate",
            "args": [
                this.canvassize.width / 2,
                this.canvassize.height / 2]
        });

        for (var i = 0; i < this.P; i++)
        {
            stack.push({
                "command": "rotate",
                "args": [Math.PI / this.P]
            });
            stack.push({
                "command": "lineTo",
                "args": [0, -(r * this.M)]

            });
            stack.push({
                "command": "rotate",
                "args": [Math.PI / this.P]
            });
            stack.push({
                "command": "lineTo",
                "args": [0, -r]

            });
        }
        stack.push({
            "command": "translate",
            "args": [-this.canvassize.width / 2, -this.canvassize.height / 2]

        });

        return stack;
    }
}
;