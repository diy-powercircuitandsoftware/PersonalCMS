class SlideShow2D {
    constructor(...args) {
       
 

  
    Start() {

        if (!this.Running) {
           



                         

                       
                        } else if (cmd.command === "QuadraticCurveTo") {
                            ctx.quadraticCurveTo(cmd.cpx, cmd.cpy, cmd.x, cmd.y);
                        }

                    }

            

}
 
   


class SlideShow2D_Transition_PageTurn extends SlideShow2D_TransitionsEngine {
    Start() {
        this.CenterA = this.Center(this.image1size, this.canvassize, this.scale(this.image1size, this.canvassize));
        this.CenterB = this.Center(this.image2size, this.canvassize, this.scale(this.image2size, this.canvassize));
        this.P = (Math.sqrt(Math.pow(this.canvassize.width, 2) + Math.pow(this.canvassize.height, 2))) * 1.5;
        return[{
                "command": "clearRect",
                "x": 0,
                "y": 0,
                "width": this.canvassize.width,
                "height": this.canvassize.height
            }, {
                "command": "DrawImage",
                "image": 1,
                "src": this.Rect(0, 0, this.image1size.width, this.image1size.height),
                "dest": this.CenterA
            }];
    }
    Running(time) {
        var stack = [];
        var x = (this.P * time) + 0.1;
        var y = (this.P * time) + 0.1;

        stack.push({
            "command": "save"
        }, {
            "command": "fillRect",
            "x": 0,
            "y": 0,
            "width": this.canvassize.width,
            "height": this.canvassize.height

        }, {
            "command": "DrawImage",
            "image": 2,
            "src": this.Rect(0, 0, this.image2size.width, this.image2size.height),
            "dest": this.CenterB
        }, {
            "command": "beginPath"
        }, {
            "command": "moveTo",
            "x": y * y / 2 / x + x / 2,
            "y": 0
        }, {
            "command": "lineTo",
            "x": this.canvassize.width * 2,
            "y": 0
        }, {
            "command": "lineTo",
            "x": 0,
            "y": this.canvassize.height * 2
        }, {
            "command": "lineTo",
            "x": 0,
            "y": x * x / 2 / y + y / 2
        }, {
            "command": "closePath"
        }, {
            "command": "globalCompositeOperation",
            "value": "destination-out"
        }, {
            "command": "fill"
        }
        , {
            "command": "globalCompositeOperation",
            "value": "source-over"
        }, {
            "command": "clip"
        }, {
            "command": "DrawImage",
            "image": 1,
            "src": this.Rect(0, 0, this.image1size.width, this.image1size.height),
            "dest": this.CenterA
        }, {
            "command": "translate",
            "x": x,
            "y": y
        }, {
            "command": "Rotate",
            "value": Math.atan2(y, x) * 2
        }, {
            "command": "scale",
            "x": -1,
            "y": 1
        }, {
            "command": "DrawImage",
            "image": 1,
            "src": this.Rect(0, 0, this.image1size.width, this.image1size.height),
            "dest": this.CenterA
        }, {
            "command": "translate",
            "x": x,
            "y": y
        }, {
            "command": "restore"
        });
        return stack;
    }

}
;

 
class SlideShow2D_Transition_StarOut extends SlideShow2D_Transition_fillEngine {
    Start() {
        this.P = 5;
        this.M = 0.5;
        this.MaxCanvasSize = Math.max(this.canvassize.width, this.canvassize.height);
        super.Start();
    }
    Shape(time) {
        var stack = [];
        var r = this.MaxCanvasSize * time;
        stack.push({
            "command": "translate",
            "x": this.canvassize.width / 2,
            "y": this.canvassize.height / 2
        });
        for (var i = 0; i < this.P; i++)
        {

            stack.push({
                "command": "Rotate",
                "value": Math.PI / this.P
            });
            stack.push({
                "command": "lineTo",
                "x": 0,
                "y": -(r * this.M)
            });
            stack.push({
                "command": "Rotate",
                "value": Math.PI / this.P
            });
            stack.push({
                "command": "lineTo",
                "x": 0,
                "y": -r
            });

        }
        stack.push({
            "command": "translate",
            "x": -this.canvassize.width / 2,
            "y": -this.canvassize.height / 2
        });
        return stack;
    }
}
;






class SlideShow2D_Transition_HeartOut extends SlideShow2D_Transition_fillEngine {
    Start() {
        this.DoubleMaxCanvasSize = Math.max(this.canvassize.width, this.canvassize.height) * 2;
        super.Start();
    }
    Shape(time) {
        var stack = [];
        var d = this.DoubleMaxCanvasSize * time; //The Size of the hearting
        var k = (this.canvassize.width / 2) - (d / 2); // The Position of the heart
        var kd4 = k + d / 4;
        var kd2 = k + d / 2;
        var kd34 = k + d * 3 / 4;
        var kd = k + d;
        stack.push({
            "command": "moveTo",
            "x": k,
            "y": kd4
        }, {
            "command": "QuadraticCurveTo",
            "cpx": k,
            "cpy": k,
            "x": kd4,
            "y": k
        }, {
            "command": "QuadraticCurveTo",
            "cpx": kd2,
            "cpy": k,
            "x": kd2,
            "y": kd4
        }, {
            "command": "QuadraticCurveTo",
            "cpx": kd2,
            "cpy": k,
            "x": kd34,
            "y": k
        }, {
            "command": "QuadraticCurveTo",
            "cpx": kd,
            "cpy": k,
            "x": kd,
            "y": kd4
        }, {
            "command": "QuadraticCurveTo",
            "cpx": kd,
            "cpy": kd2,
            "x": kd34,
            "y": kd34
        }, {
            "command": "lineTo",
            "x": kd2,
            "y": kd
        }, {
            "command": "lineTo",
            "x": kd4,
            "y": kd34
        }, {
            "command": "QuadraticCurveTo",
            "cpx": k,
            "cpy": kd2,
            "x": k,
            "y": kd4
        });


        return stack;


    }
}
;

class SlideShow2D_Transition_RectWipe extends SlideShow2D_Transition_fillEngine {
    Start() {
        super.Start();
        this.CountOFTiles = 12;
        this.Tiles = Math.max(this.canvassize.width, this.canvassize.height) / this.CountOFTiles;
        this.Starty = 0;
        this.Startx = 0;

        this.Index = 0;
        this.MaxIndex = this.CountOFTiles * this.CountOFTiles;
    }
    Shape(time) {

        var progress = this.Index / this.MaxIndex;
        var out = [];
        while (progress < time) {// time calibration 

            if (this.Startx > this.canvassize.width) {
                this.Starty = this.Starty + this.Tiles;
                this.Startx = 0;
            } else {
                this.Startx = this.Startx + this.Tiles;
            }
            out.push({
                "command": "Rect",
                "x": this.Startx,
                "y": this.Starty,
                "width": this.Tiles,
                "height": this.Tiles
            });
            this.Index++;
            progress = this.Index / this.MaxIndex;
        }

        return out;

    }
}
;

class SlideShow2D_Transition_Mosaic extends SlideShow2D_Transition_fillEngine {
    Start() {
        super.Start();
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
    Shape(time) {

        var progress = this.Index / this.MaxIndex;
        var out = [];
        while (progress < time) {// time calibration 
            var mosaic = this.Mosaic.splice(this.Mosaic.length * Math.random() | 0, 1)[0];
            if (mosaic !== undefined) {
                out.push({
                    "command": "Rect",
                    "x": mosaic.x,
                    "y": mosaic.y,
                    "width": this.Tiles,
                    "height": this.Tiles
                });
            }
            this.Index++;
            progress = this.Index / this.MaxIndex;
        }

        return out;

    }
}
;


class SlideShow2D_Transition_SpinRight extends SlideShow2D_TransitionsEngine {
    Start() {
        this.CenterA = this.Center(this.image1size, this.canvassize, this.scale(this.image1size, this.canvassize));
        this.CenterB = this.Center(this.image2size, this.canvassize, this.scale(this.image2size, this.canvassize));
        this.CX = this.canvassize.width / 2;
        this.CY = this.canvassize.height / 2;
    }

    Running(time) {
        var stack = [];
        stack.push({
            "command": "clearRect",
            "x": 0,
            "y": 0,
            "width": this.canvassize.width,
            "height": this.canvassize.height

        });
        if (time < 0.4) {
            stack.push({
                "command": "DrawImage",
                "image": 1,
                "src": this.Rect(0, 0, this.image1size.width, this.image1size.height),
                "dest": this.CenterA
            });
        } else if (time > 0.4 && time < 0.6) {

            stack.push({
                "command": "save",

            }, {
                "command": "translate",
                "x": this.CX,
                "y": this.CY
            }, {
                "command": "Rotate",
                "value": (12 * 360 * time) * Math.PI / 180

            }, {
                "command": "translate",
                "x": -this.CX,
                "y": -this.CY
            }, {
                "command": "DrawImage",
                "image": 2,
                "src": this.Rect(0, 0, this.image2size.width, this.image2size.height),
                "dest": this.CenterB
            }, {
                "command": "restore"

            });
        } else if (time > 0.6) {

            stack.push({
                "command": "DrawImage",
                "image": 2,
                "src": this.Rect(0, 0, this.image2size.width, this.image2size.height),
                "dest": this.CenterB
            });
        }

        return stack;
    }

}
;



class SlideShow2D_Transition_ZoomInOut extends SlideShow2D_TransitionsEngine {
    Start() {
        this.CenterA = this.Center(this.image1size, this.canvassize, this.scale(this.image1size, this.canvassize));
        this.CenterB = this.Center(this.image2size, this.canvassize, this.scale(this.image2size, this.canvassize));
        this.CX = this.canvassize.width / 2;
        this.CY = this.canvassize.height / 2;
    }

    Running(time) {
        var stack = [];
        var progress = 0;
        var inout = "";
        stack.push({
            "command": "save"
        }, {
            "command": "clearRect",
            "x": 0,
            "y": 0,
            "width": this.canvassize.width,
            "height": this.canvassize.height

        });


        if (time < 0.2) {
            progress = (time - 0) / 0.2;
            inout = "i";
        } else if (time > 0.2 && time < 0.4) {
            progress = (time - 0.2) / 0.2;
            inout = "o";
        } else if (time > 0.4 && time < 0.6) {
            inout = "";
        } else if (time > 0.6 && time < 0.8) {
            progress = (time - 0.6) / 0.2;
            inout = "i";
        } else if (time > 0.8) {
            progress = (time - 0.8) / 0.2;
            inout = "o";
        }
        stack.push({
            "command": "translate",
            "x": this.CX,
            "y": this.CY
        });

        var size = 3 * progress;
        var invsize = 3 * (1 - progress);
        if (inout === "i" && size > 1) {
            stack.push({
                "command": "scale",
                "x": size,
                "y": size
            });
        } else if (inout === "o" && invsize > 1) {

            stack.push({
                "command": "scale",
                "x": invsize,
                "y": invsize
            });
        }
        stack.push({
            "command": "translate",
            "x": -this.CX,
            "y": -this.CY
        });
        if (time < 0.5) {
            stack.push({
                "command": "DrawImage",
                "image": 1,
                "src": this.Rect(0, 0, this.image1size.width, this.image1size.height),
                "dest": this.CenterA
            });
        } else if (time > 0.5) {
            stack.push({
                "command": "DrawImage",
                "image": 2,
                "src": this.Rect(0, 0, this.image2size.width, this.image2size.height),
                "dest": this.CenterB
            });
        }


        stack.push({
            "command": "restore"
        });


        return stack;
    }

}
;
