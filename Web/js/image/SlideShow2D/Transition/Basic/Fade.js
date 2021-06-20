 class SlideShow2D_Transition_FadeOutFadeIn extends SlideShow2D_Transition {
    Update(time) {
        var stack = [];
        if (time == 0) {
            return[{
                    "command": "clearRect",
                    "args": [0, 0, this.canvassize.width, this.canvassize.height]
                }, {
                    "command": "globalAlpha",
                    "value": 1
                }, {
                    "command": "DrawCenter",
                    "address": 1,
                    "extends": true
                }];
        }

        if (time < 0.5) {
            stack.push({
                "command": "clearRect",
                "args": [0, 0, this.canvassize.width, this.canvassize.height]
            });
            stack.push({
                "command": "globalAlpha",
                "value": 1 - (2 * time)
            }, {
                "command": "DrawCenter",
                "address": 1,
                "extends": true
            });

        } else if (time > 0.5) {
            stack.push({
                "command": "clearRect",
                "args": [0, 0, this.canvassize.width, this.canvassize.height]
            });
            stack.push({
                "command": "globalAlpha",
                "value": (2 * time) - 1
            }, {
                "command": "DrawCenter",
                "address": 2,
                "extends": true
            });
        }
        if (time >= 0.9) {
            return[{
                    "command": "globalAlpha",
                    "value": 1
                }];
        }
        return stack;
    }
};
 