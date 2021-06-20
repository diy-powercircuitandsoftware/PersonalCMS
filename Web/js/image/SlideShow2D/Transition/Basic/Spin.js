class SlideShow2D_Transition_Spin extends SlideShow2D_Transition {
    Running(time) {
        if (time == 0) {
            this.CX = this.canvassize.width / 2;
            this.CY = this.canvassize.height / 2;
            this.Round=12;
        }
        var stack = [];
        var x = (this.Round * 360 * time) * Math.PI / 180;
        stack.push({
            "command": "save"
        }, {
            "command": "clearRect",
            "args": [0, 0,
                this.canvassize.width, this.canvassize.height]
        });
        if (time < 0.5) {         
            stack.push({
                "command": "translate",
                "args": [this.CX, this.CY]
            }, {
                "command": "rotate",
                "args": [x]

            }, {
                "command": "translate",
                "args": [-this.CX, -this.CY]
            }, {
                "command": "DrawCenter",
                "address": 1,
                "extends": true
            });
        } else if (time > 0.5) {
            
            stack.push({
                "command": "translate",
                "args": [this.CX, this.CY]
            }, {
                "command": "rotate",
                "args": [-x]

            }, {
                "command": "translate",
                "args": [-this.CX, -this.CY]
            }, {
                "command": "DrawCenter",
                "address": 2,
                "extends": true
            });
        }
        stack.push({
            "command": "restore"
        });
        return stack;

    }
}
;
