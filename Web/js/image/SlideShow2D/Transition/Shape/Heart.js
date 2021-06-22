class SlideShow2D_Transition_HeartIn extends SlideShow2D_Fill_Transition {
    Initialization() {
        this.DoubleMaxCanvasSize = Math.max(this.canvassize.width, this.canvassize.height) * 2;
        this.ImageA = 2;
        this.ImageB = 1;
    }
    Template(time, tick) {
        var stack = [];
        var d = this.DoubleMaxCanvasSize * (1 - time); //The Size of the hearting
        var k = (this.canvassize.width / 2) - (d / 2); // The Position of the heart
        var kd4 = k + d / 4;
        var kd2 = k + d / 2;
        var kd34 = k + d * 3 / 4;
        var kd = k + d;
        stack.push({
            "command": "moveTo",
            "args": [k, kd4]
        }, {
            "command": "quadraticCurveTo",
            "args": [k, k, kd4, k]

        }, {
            "command": "quadraticCurveTo",
            "args": [kd2, k, kd2, kd4]

        }, {
            "command": "quadraticCurveTo",
            "args": [kd2, k, kd34, k]

        }, {
            "command": "quadraticCurveTo",
            "args": [kd, k, kd, kd4]
        }, {
            "command": "quadraticCurveTo",
            "args": [kd, kd2, kd34, kd34]
        }, {
            "command": "lineTo",
            "args": [kd2, kd]
        }, {
            "command": "lineTo",
            "args": [kd4, kd34]
        }, {
            "command": "quadraticCurveTo",
            "args": [k, kd2, k, kd4]
        });

        return stack;

    }
}
;

class SlideShow2D_Transition_HeartOut extends SlideShow2D_Fill_XY_Equation_Transition {
    Initialization() {
        var max=Math.max(this.canvassize.width, this.canvassize.height);
        this.MaxCanvasSize = Math.sqrt(max)*3;
        this.ReDrawingImageA = false;
    }
    GetX(radian, time, tick) {
        return (this.MaxCanvasSize * time) * (16 * Math.pow(Math.sin(radian), 3))
    }
    GetY(radian, time, tick) {
        return  (-this.MaxCanvasSize * time) * (13 * Math.cos(radian) - 5 * Math.cos(2 * radian) - 2 * Math.cos(3 * radian) - Math.cos(4 * radian))
    }
}
;
