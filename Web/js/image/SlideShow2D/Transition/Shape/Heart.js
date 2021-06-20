class SlideShow2D_Transition_HeartOut extends SlideShow2D_Fill_Transition {
    Initialization() {
        this.DoubleMaxCanvasSize = Math.max(this.canvassize.width, this.canvassize.height) * 2;
    }
    Template(time) {
        var stack = [];
        var d = this.DoubleMaxCanvasSize * time; //The Size of the hearting
        var k = (this.canvassize.width / 2) - (d / 2); // The Position of the heart
        var kd4 = k + d / 4;
        var kd2 = k + d / 2;
        var kd34 = k + d * 3 / 4;
        var kd = k + d;
        stack.push({
            "command": "moveTo",
            "args":[ k,kd4]           
        }, {
            "command": "quadraticCurveTo",
              "args":[ k,k,kd4,k]
            
        }, {
            "command": "quadraticCurveTo",
              "args":[kd2,k,kd2,kd4]
             
        }, {
            "command": "quadraticCurveTo",
              "args":[kd2,k,kd34,k]
             
        }, {
            "command": "quadraticCurveTo",
              "args":[ kd,k,kd,kd4]            
        }, {
            "command": "quadraticCurveTo",
              "args":[kd,kd2,kd34,kd34]            
        }, {
            "command": "lineTo",
              "args":[ kd2,kd]            
        }, {
            "command": "lineTo",
              "args":[kd4,kd34]            
        }, {
            "command": "quadraticCurveTo",
              "args":[k,kd2,k,kd4]             
        });

        return stack;

    }
}
;