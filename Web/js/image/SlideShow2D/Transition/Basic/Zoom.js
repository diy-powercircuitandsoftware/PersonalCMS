class SlideShow2D_Transition_ZoomOutZoomIn extends SlideShow2D_Transition {

    Running(time) {
        if (time == 0) {
            this.CX = this.canvassize.width / 2;
            this.CY = this.canvassize.height / 2;
        }
        var stack = [];
        stack.push({
            "command": "save"
        }, {
            "command": "clearRect",
            "args": [0, 0,
                this.canvassize.width, this.canvassize.height]
        });
        if (time < 0.5) {
            var scale = (1 - (time * 2));
            stack.push({
                "command": "translate",
                "args": [this.CX, this.CY]
            }, {
                "command": "scale",
                "args": [scale, scale]

            }, {
                "command": "translate",
                "args": [-this.CX, -this.CY]
            }, {
                "command": "DrawCenter",
                "address": 1,
                "extends": true
            });
        } else if (time > 0.5) {
            var scale = time;
            stack.push({
                "command": "translate",
                "args": [this.CX, this.CY]
            }, {
                "command": "scale",
                "args": [scale, scale]

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
