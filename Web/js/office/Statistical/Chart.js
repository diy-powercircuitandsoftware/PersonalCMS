class Chart {
    constructor(...args) {
        if (args.length === 1 && typeof args[0] === 'string' || args[0] instanceof String) {
            var e = document.querySelector(args[0]);
            if (e.tagName == "canvas") {
                this.Canvas = e;
            } else {
                this.Canvas = e.appendChild(document.createElement("canvas"));
            }
        } else if (args.length === 1 && args[0] instanceof HTMLElement) {
            if (args[0].tagName == "canvas") {
                this.Canvas = args[0];
            } else {
                this.Canvas = args[0].appendChild(document.createElement("canvas"));
            }
        } else {
            this.Canvas = document.body.appendChild(document.createElement("canvas"));
        }
        this.Canvas.getContext('2d').font = "14pt sans-serif";
        this.Data = {};
    }
   SetData  (lab, value) {
        this.Data[lab] = value;
    }
    
   ReduceArraySize  (assoarray, len) {
        len = Math.floor(len);
        if (Object.keys(assoarray).length > len) {
            var array = [];
            for (var k in assoarray) {
                array.push({"Key": k, "Value": assoarray[ k ]});
            }
            array.sort(function (a, b) {
                return b.Value - a.Value;
            });
            var assoarray = {};
            for (var i = 0; i < len; i++) {
                assoarray[array[i]["Key"]] = array[i]["Value"];
            }

            assoarray["other"] = assoarray["other"] || 0;
            for (var i = len; i < array.length; i++) {
                assoarray["other"] = assoarray["other"] + array[i]["Value"];
            }

            return assoarray;
        }
        return assoarray;
    }
   ReduceArrayKey  (assoarray, len) {
        len = Math.floor(len);
        var out = {};
        for (var k in assoarray) {
            if (k.length <= len) {
                out[k] = assoarray[k];
            } else {
                var ks = k.substr(0, len / 2);
                for (var i = ks.length; i < len; i++) {
                    ks = ks + ".";
                }
                out[ks] = assoarray[k];
            }
        }
        return out;
    }
   PaintXYScale  (data) {
        var ctx = this.Canvas.getContext('2d');
        ctx.save();
        var fontsize = parseInt(ctx.font.match(/\d+/g).map(Number));
        var w80 = Math.floor(ctx.this.Canvas.width * 0.8);
        var h = (ctx.this.Canvas.height - (1.5 * fontsize));
        var maxbar = Math.floor(Math.min(w80 / (5 * fontsize), Object.keys(this.Data).length));
        var maxycount = (Math.floor(h / (3 * fontsize))) - 1;
        var data = this.ReduceArrayKey(this.ReduceArraySize(this.Data, maxbar), maxbar);
        var ydata = (Math.max.apply(null, Object.values(data)) * 1.2) / maxycount;
        var offsetleft = Math.min(Math.floor(ctx.this.Canvas.width * 0.2), ctx.measureText((ydata * maxycount).toFixed(2)).width + fontsize);
        var offsettop = fontsize;
        var ystep = Math.floor((h - offsettop) / maxycount);
        var ystring = Math.floor(h - ystep);
        ctx.clearRect(0, 0, ctx.this.Canvas.width, ctx.this.Canvas.height);
        ctx.fillStyle = "black";
        ctx.beginPath();
        ctx.moveTo(offsetleft, 0);
        ctx.lineTo(offsetleft, h);
        ctx.moveTo(offsetleft, h);
        ctx.lineTo(ctx.this.Canvas.width, h);
        ctx.stroke();
        ctx.beginPath();
        if (Object.values(data).join("").indexOf(".") == -1) {
            ydata = Math.ceil(ydata);
        }
        for (var i = 1; i < maxycount; i++) {
            var drawindex = (i * ydata).toFixed(2);
            ctx.fillText(drawindex, 3, ystring);
            ctx.moveTo(offsetleft, ystring);
            ctx.lineTo(ctx.this.Canvas.width, ystring);
            ystring -= ystep;
        }
        ctx.fillText((ydata * maxycount).toFixed(2), 3, ystring);
        ctx.moveTo(offsetleft, ystring);
        ctx.lineTo(ctx.this.Canvas.width, ystring);
        ctx.stroke();
        var labelwidth = (ctx.this.Canvas.width - offsetleft) / (Object.keys(data).length + 1);
        var xaccumulate = offsetleft + (labelwidth / 2);
        var maxvalue = ydata * (maxycount - 1);
        var maxheight = Math.floor(h - ystep) - ystring;
        for (var k in data) {
            ctx.fillText(k, xaccumulate, h + fontsize);
            xaccumulate = xaccumulate + (labelwidth);
        }
        ctx.restore();
        return{
            "Data": data,
            "LabelWidth": labelwidth,
            "Left": offsetleft,
            "MaxValue": maxvalue,
            "MaxHeight": maxheight,
            "MaxDrawHeight": h,
            "CTX": ctx
        };
    }
   DrawBarChart  () {
        var paintdata = this.PaintXYScale(this.Data);
        var data = paintdata.Data;
        var ctx = paintdata.CTX;
        ctx.save();
        var xaccumulate = paintdata.Left + (paintdata.LabelWidth / 2);
        for (var k in data) {
            ctx.fillStyle = 'rgb(' +
                    Math.floor(Math.random() * 256) + ',' +
                    Math.floor(Math.random() * 256) + ',' +
                    Math.floor(Math.random() * 256) + ')';
            var rv = data[k] / paintdata.MaxValue;
            var hbar = rv * paintdata.MaxHeight;
            ctx.fillRect(xaccumulate, paintdata.MaxDrawHeight - hbar, paintdata.LabelWidth / 2, hbar);
            xaccumulate = xaccumulate + (paintdata.LabelWidth);
        }
        ctx.restore();
    }
   DrawDotChart  () {
        var paintdata = this.PaintXYScale(this.Data);
        var data = paintdata.Data;
        var ctx = paintdata.CTX;
        ctx.save();
        var xaccumulate = paintdata.Left + (paintdata.LabelWidth / 2);
        ctx.fillStyle = "black";
        for (var k in data) {
            var rv = data[k] / paintdata.MaxValue;
            var hbar = rv * paintdata.MaxHeight;
            ctx.fillRect(xaccumulate, paintdata.MaxDrawHeight - hbar, 5, 5);
            xaccumulate = xaccumulate + (paintdata.LabelWidth);
        }
        ctx.restore();
    }
   DrawLineChart  () {
        var prev = null;
        var paintdata = this.PaintXYScale(this.Data);
        var data = paintdata.Data;
        var ctx = paintdata.CTX;
        ctx.save();
        var lw = ctx.lineWidth;
        var xaccumulate = paintdata.Left + (paintdata.LabelWidth / 2);
        ctx.fillStyle = "black";
        ctx.lineWidth = 10;
        ctx.beginPath();
        for (var k in data) {
            var rv = data[k] / paintdata.MaxValue;
            var hbar = rv * paintdata.MaxHeight;
            ctx.fillRect(xaccumulate, paintdata.MaxDrawHeight - hbar, 5, 5);
            if (prev == null) {
                ctx.moveTo(xaccumulate, paintdata.MaxDrawHeight - hbar);
            } else {
                ctx.moveTo(prev.x, prev.y);
            }
            ctx.lineTo(xaccumulate, paintdata.MaxDrawHeight - hbar);
            prev = {
                "x": xaccumulate,
                "y": paintdata.MaxDrawHeight - hbar
            }
            xaccumulate = xaccumulate + (paintdata.LabelWidth);
        }
        ctx.lineWidth = lw;
        ctx.stroke();
        ctx.restore();
    }
   DrawPieChart  () {
        var ctx = this.Canvas.getContext('2d');
        ctx.save();
        var w80 = this.Canvas.width * 0.8;
        var fontsize = parseInt(ctx.font.match(/\d+/g).map(Number));
        var maxdata = Math.floor((ctx.this.Canvas.height - fontsize - fontsize) / fontsize) - 1;
        var maxlentext = (this.Canvas.width - ctx.measureText("(100%)").width - w80) / fontsize;
        var data = this.ReduceArrayKey(this.ReduceArraySize(this.Data, maxdata), maxlentext);
        var total = Object.values(data).reduce((a, b) => a + b, 0);
        var angle = 0;
        var radius = Math.min(w80 / 2, ctx.this.Canvas.height / 2);
        var txty = fontsize;
        ctx.clearRect(0, 0, ctx.this.Canvas.width, ctx.this.Canvas.height);
        for (var k in data) {
            var rcolor = 'rgb(' +
                    Math.floor(Math.random() * 256) + ',' +
                    Math.floor(Math.random() * 256) + ',' +
                    Math.floor(Math.random() * 256) + ')';
            var v = data[k] / total;
            var valueangle = (2 * Math.PI) * v;
            ctx.fillStyle = rcolor;
            ctx.beginPath();
            ctx.moveTo(w80 / 2, this.Canvas.height / 2);
            ctx.arc(w80 / 2, this.Canvas.height / 2, radius, angle, angle + valueangle);
            ctx.closePath();
            ctx.fill();
            var text = k + "(" + ((v) * 100).toFixed(2) + "%)";
            ctx.fillRect(w80, txty - fontsize, fontsize, fontsize);
            ctx.fillStyle = "black";
            ctx.fillText(text, w80 + fontsize, txty);
            txty += fontsize + 1;
            angle += valueangle;
        }
        ctx.restore();
    }
   DrawRingChart  () {
        this.DrawPieChart();
        var ctx = this.Canvas.getContext('2d');
        var x = this.Canvas.width * 0.4;
        var y = this.Canvas.height * 0.5;
        var r = this.Canvas.width * 0.2;
        ctx.save();
        ctx.beginPath();
        ctx.arc(x, y, r, 0, 2 * Math.PI, false);
        ctx.closePath();
        ctx.globalCompositeOperation = 'destination-out';
        ctx.fill();
        ctx.globalCompositeOperation = "source-over";
        ctx.clip();
        ctx.restore();
    }
}

 