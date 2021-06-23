class SlideShow2D_Transition_HeartIn extends SlideShow2D_Fill_XY_Equation_Transition {

    Initialization() {
        var maxcanvassize = Math.max(this.canvassize.width, this.canvassize.height);
        this.ImageA = 2;
        this.ImageB = 1;
        this.MaxCanvasSize = maxcanvassize / (16);

    }
    MultiplyX(time, tick) {
        return this.MaxCanvasSize * (1 - time);
    }
    MultiplyY(time, tick) {      
        return -this.MaxCanvasSize * (1 - time);
    }
    GetX(radian, time, tick) {
        return  16 * Math.pow(Math.sin(radian), 3);
    }
    GetY(radian, time, tick) {
        return    13 * Math.cos(radian) - 5 * Math.cos(2 * radian) - 2 * Math.cos(3 * radian) - Math.cos(4 * radian);
    }
}
;

class SlideShow2D_Transition_HeartOut extends SlideShow2D_Fill_XY_Equation_Transition {
    Initialization() {
        var maxcanvassize = Math.max(this.canvassize.width, this.canvassize.height);
        this.MaxCanvasSize = maxcanvassize / (16);
        this.ReDrawingImageA = false;
    }
    MultiplyX(time, tick) {
        return this.MaxCanvasSize * time;
    }
    MultiplyY(time, tick) {
        return -this.MaxCanvasSize * time;
    }
    GetX(radian, time, tick) {
        return   16 * Math.pow(Math.sin(radian), 3);
    }
    GetY(radian, time, tick) {
        return    13 * Math.cos(radian) - 5 * Math.cos(2 * radian) - 2 * Math.cos(3 * radian) - Math.cos(4 * radian);
    }
}
;
