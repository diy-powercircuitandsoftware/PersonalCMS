class SlideShow2D_Transition_HorizontalBlind extends SlideShow2D_Fill_Transition {
    Initialization() {

        this.bar = 12;
        this.barheight = this.canvassize.height / this.bar;
    }
    Template(time) {
        var stack = [];
        for (var i = 0; i < this.bar; i++) {
            stack.push({
                "command": "rect",
                "args": [
                    0,this.barheight * i,
                    this.canvassize.width,
                    this.barheight * time]
            });
        }
        return stack;

    }
}
;

class SlideShow2D_Transition_VerticalBlind extends SlideShow2D_Fill_Transition {
    Initialization() {
        this.bar = 12;
        this.barwidth = this.canvassize.width / this.bar;
    }
    Template(time) {
        var stack = [];
        for (var i = 0; i < this.bar; i++) {
            stack.push({
                "command": "rect", "args": [
                    this.barwidth * i,0,
                    this.barwidth * time,
                    this.canvassize.height]
            });
        }
        return stack;

    }
}
;


