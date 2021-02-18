class BellChart {
    constructor(...args) {
        if ( typeof args[0] === 'string' || args[0] instanceof String) {
            var e = document.querySelector(args[0]);
            if (e.tagName == "CANVAS") {
                this.Canvas = e;
            } else {
                this.Canvas = e.appendChild(document.createElement("canvas"));
            }
        } else if ( args[0] instanceof HTMLElement) {
            if (args[0].tagName == "CANVAS") {
                this.Canvas = args[0];
            } else {
                this.Canvas = args[0].appendChild(document.createElement("canvas"));
            }
        } else {
            this.Canvas = document.body.appendChild(document.createElement("canvas"));
        }
        this.Canvas.getContext('2d').font = "14pt sans-serif";

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
    Between(za2l, za2u, m, sd) {
        if (sd > 0) {
            this.DrawNormal(m, sd, parseFloat(za2l), parseFloat(za2u), false);
        }
    }
    Outside(za2l, za2u, m, sd) {
        if (sd > 0) {
            this.DrawNormal(m, sd, parseFloat(za2l), parseFloat(za2u), true);
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

}

 