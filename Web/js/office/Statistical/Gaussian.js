class Gaussian {
    //https://github.com/miguelamezola/bell-curve-js/blob/master/src/CurveGUI.js
    
    constructor(...args) {
        if (typeof args[0] === 'string' || args[0] instanceof String) {
            var e = document.querySelector(args[0]);
            if (e.tagName == "CANVAS") {
                this.Canvas = e;
            } else {
                this.Canvas = e.appendChild(document.createElement("canvas"));
            }
        } else if (args[0] instanceof HTMLElement) {
            if (args[0].tagName == "CANVAS") {
                this.Canvas = args[0];
            } else {
                this.Canvas = args[0].appendChild(document.createElement("canvas"));
            }
        } else {
            this.Canvas = document.body.appendChild(document.createElement("canvas"));
        }
    }
    DrawNormal(M, sd, lFill, hFill, tail) {
        var ctx = this.Canvas.getContext('2d');
        ctx.save();
        ctx.clearRect(0, 0, ctx.canvas.width, ctx.canvas.height);
        ctx.fillStyle = "black";
        ctx.beginPath();
        var v = sd * sd;
        var constant = 1 / Math.sqrt(2 * Math.PI * v);
        var x = M;
        var Ay = (this.Canvas.height / 1.1);
        var by = -this.Canvas.height / (1.2 * constant);
        var lowX = M - 3.5 * sd;
        var highX = M + 3.5 * sd;
        var bx = this.Canvas.width / (highX - lowX);
        var Ax = this.Canvas.width / 2 - bx * ((highX + lowX) / 2);
        var x0 = lowX * bx + Ax;
        var xf = highX * bx + Ax;
        ctx.moveTo(xf, Ay);
        ctx.lineTo(x0, Ay);
        var inc = 1 / bx;
        var dmax = 0;
        for (var i = lowX; i <= highX; i += inc * .5) {
            var xp = bx * i + Ax;
            var d = constant * Math.exp(-Math.pow((i - M), 2) / (2 * v));
            dmax = Math.max(dmax, d);
            var dp = by * d + Ay;
            ctx.lineTo(xp, dp);
            if (tail) {
                if (i >= hFill || i <= lFill) {
                    ctx.moveTo(xp, Ay);
                    ctx.lineTo(xp, dp + 1);
                }
            } else
            if (i <= hFill && i >= lFill) {
                ctx.moveTo(xp, Ay);
                ctx.lineTo(xp, dp + 1);
            }
        }
        ctx.textAlign = "center";
        ctx.strokeStyle = "black";
        var y = Ay + 15;
        for (var i = M - 3 * sd; i <= M + 3 * sd; i += sd) {
            x = bx * i + Ax;
            ctx.moveTo(x, Ay);
            ctx.lineTo(x, Ay + 4);
            var xlab = Math.round(1000 * i);
            xlab = xlab / 1000;
            ctx.fillText(xlab, x, y + 2);
        }
        ctx.closePath();
        ctx.stroke();
        ctx.restore();
    }
    Above(za2, m, sd) {
        if (sd > 0) {
            this.DrawNormal(m, sd, parseFloat(za2), 999999, false);
        }
    }

    Below(za2, m, sd) {
        if (sd > 0) {
            this.DrawNormal(m, sd, -999999, parseFloat(za2), false);
        }
    }

    Bell_Above(za2, arr) {
        var z = this.Z(Math.abs(parseFloat(za2)), arr);
        return Math.round(this.ZTable(z) * 10000) / 10000;
    }
    Bell_Below(za2, arr) {
        var z = this.Z(Math.abs(parseFloat(za2)), arr);
        var p = 1 - this.ZTable(z);
        return Math.round(p * 10000) / 10000;
    }
    Bell_Between(za2l, za2u, arr) {
        var z1 = this.Z((parseFloat(za2l)), arr);
        var z2 = this.Z((parseFloat(za2u)), arr);
        var zp = this.ZTable(z2) - this.ZTable(z1);
        return Math.round(zp * 10000) / 10000;
    }
    Bell_Outside(za2l, za2u, arr) {
        var z1 = this.Z((parseFloat(za2l)), arr);
        var z2 = this.Z((parseFloat(za2u)), arr);
        var zp = this.ZTable(z1) + (1 - this.ZTable(z2));
        return Math.round(zp * 10000) / 10000;
    }

    Between(za2l, za2u, m, sd) {
        if (sd > 0) {
            this.DrawNormal(m, sd, parseFloat(za2l), parseFloat(za2u), false);
        }
    }

    ConfidenceLevelTOZA2(CL) {
        var a2 = (1 - CL) / 2;
        return this.ZTableInvert(a2);
    }
    Outside(za2l, za2u, m, sd) {
        if (sd > 0) {
            this.DrawNormal(m, sd, parseFloat(za2l), parseFloat(za2u), true);
        }
    }

    ZTable(z) {
        if (z < -7) {
            return 0.0;
        }
        if (z > 7) {
            return 1.0;
        }
        z = Math.abs(z);
        var b = 0.0;
        var s = Math.sqrt(2) / 3 * z;
        var HH = .5;
        for (var i = 0; i < 12; i++) {
            var a = Math.exp(-HH * HH / 9) * Math.sin(HH * s) / HH;
            b = b + a;
            HH = HH + 1.0;
        }
        var p = .5 - b / Math.PI;
        if (z >= 0.0) {
            return 1.0 - p;
        }
        return p;
    }

    ZTableInvert(p) {
        var t, v, theSign;
        if (p >= 1) {
            return 7;
        } else if (p <= 0) {
            return -7;
        }
        if (p < .5) {
            t = p;
            theSign = -1;
        } else
        {
            t = 1 - p;
            theSign = 1;
        }
        v = Math.sqrt(-2.0 * Math.log(t));
        var x = 2.515517 + (v * (0.802853 + v * 0.010328));
        var y = 1 + (v * (1.432788 + v * (0.189269 + v * 0.001308)));
        var Q = theSign * (v - (x / y));
        return Q;
    }

}

 