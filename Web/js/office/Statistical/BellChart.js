function BellChart(canvas) {
    var Method = {};
    Method.Above = function (za2, m, sd) {
        if (sd > 0) {
            this.DrawNormal(m, sd, parseFloat(za2), 999999, false);
        }
    };
    Method.Below = function (za2, m, sd) {
        if (sd > 0) {
            this.DrawNormal(m, sd, -999999, parseFloat(za2), false);
        }
    };
    Method.Between = function (za2l, za2u, m, sd) {
        if (sd > 0) {
            this.DrawNormal(m, sd, parseFloat(za2l), parseFloat(za2u), false);
        }
    };
    Method.Outside = function (za2l, za2u, m, sd) {
        if (sd > 0) {
            this.DrawNormal(m, sd, parseFloat(za2l), parseFloat(za2u), true);
        }
    };
    Method.DrawNormal = function (M, sd, lFill, hFill, tail) {
        var ctx = canvas.getContext('2d');
        ctx.save();
        ctx.clearRect(0, 0, ctx.canvas.width, ctx.canvas.height);
        ctx.fillStyle = "black";
        ctx.beginPath();
        var v = sd * sd;
        var constant = 1 / Math.sqrt(2 * Math.PI * v);
        var x = M;
        var Ay = (canvas.height / 1.1);
        var by = -canvas.height / (1.2 * constant);
        var lowX = M - 3.5 * sd;
        var highX = M + 3.5 * sd;
        var bx = canvas.width / (highX - lowX);
        var Ax = canvas.width / 2 - bx * ((highX + lowX) / 2);
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
    };
    return Method;
}