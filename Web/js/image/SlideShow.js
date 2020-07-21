class SlideShow {
    constructor(...args) {
        if (args.length === 1 && typeof args[0] === 'string' || args[0] instanceof String) {
            this.canvas = document.querySelector(args[0]).appendChild(document.createElement("CANVAS"));
        } else if (args.length === 1 && args[0] instanceof HTMLElement) {
            this.canvas = args[0].appendChild(document.createElement("CANVAS"));
        } else {
            this.canvas = document.body.appendChild(document.createElement("CANVAS"));
        }
        
    }
}

class SlideShowImageList {

}

class SlideShowImageList {

}
/*
function SlideShow() {
    var Method = document.createElement("canvas");
    Method.style.cssText = "width:100%";
    Method.FPS = 60;
    Method.Transitions = {};
    Method.Math = {};
    Method.Math.Center = function (rect, refrect, ratio) {
        var w = rect.width * ratio;
        var h = rect.height * ratio;
        var x = refrect.width / 2 - w / 2;
        var y = refrect.height / 2 - h / 2;
        return {"x": x, "y": y, "width": w, "height": h, "ratio": ratio};
    };
    Method.Math.Scale = function (src, dest) {
        return    Math.min(dest.width / src.width, dest.height / src.height);
    };
    Method.Math.SplitBlock = function (xcount, ycount, width, height) {
        var barwidth = width / xcount;
        var barheight = height / ycount;
        var tilesdata = [];
        for (var yi = 0; yi < ycount; yi++) {
            for (var xi = 0; xi < xcount; xi++) {
                tilesdata.push({
                    "x": xi * barwidth,
                    "y": yi * barheight,
                    "width": barwidth,
                    "height": barheight
                });
            }
        }
        return tilesdata;
    };

    Method.Render = function (fps, start, end, render, finish) {
        var ms = 1000 / fps;
        if (start >= end) {
            finish();
        } else {
            var ref = this;
            setTimeout(function () {
                render({"time": start, "ratio": (start / end)});
                start = start + ms;
                window.requestAnimationFrame(function () {
                    ref.Render(fps, start, end, render, finish);
                });
            }, ms);
        }
    };
    Method.Transitions.BottomToTop = function (imagea, imageb, s, fps, finish) {
        var CenterA = Method.Math.Center(imagea, Method, Method.Math.Scale(imagea, Method));
        var CenterB = Method.Math.Center(imageb, Method, Method.Math.Scale(imageb, Method));
        var ctx = Method.getContext('2d');
        ctx.drawImage(imagea, 0, 0, imagea.width, imagea.height, CenterA.x, CenterA.y, CenterA.width, CenterA.height);
        Method.Render(fps, 0, s * 1000, function (r) {
            ctx.save();
            ctx.beginPath();
            ctx.rect(0, Method.height * (1 - r.ratio), Method.width, Method.height * r.ratio);
            ctx.closePath();
            ctx.globalCompositeOperation = 'destination-out';
            ctx.fill();
            ctx.globalCompositeOperation = 'source-over';
            ctx.clip();
            ctx.drawImage(imageb, 0, 0, imageb.width, imageb.height, CenterB.x, CenterB.y, CenterB.width, CenterB.height);
            ctx.restore();
        }, function () {
            ctx.clearRect(0, 0, Method.width, Method.height);
            ctx.drawImage(imageb, 0, 0, imageb.width, imageb.height, CenterB.x, CenterB.y, CenterB.width, CenterB.height);
            finish();
        });
    };

    Method.Transitions.CircleOut = function (imagea, imageb, s, fps, finish) {
        var max = Math.min(Method.width, Method.height);
        var CenterA = Method.Math.Center(imagea, Method, Method.Math.Scale(imagea, Method));
        var CenterB = Method.Math.Center(imageb, Method, Method.Math.Scale(imageb, Method));
        var ctx = Method.getContext('2d');
        ctx.drawImage(imagea, 0, 0, imagea.width, imagea.height, CenterA.x, CenterA.y, CenterA.width, CenterA.height);
        Method.Render(fps, 0, s * 1000, function (r) {
            ctx.save();
            ctx.beginPath();
            ctx.arc(Method.width / 2, Method.height / 2, max * r.ratio, 0, 2 * Math.PI);
            ctx.closePath();
            ctx.globalCompositeOperation = 'destination-out';
            ctx.fill();
            ctx.globalCompositeOperation = 'source-over';
            ctx.clip();
            ctx.drawImage(imageb, 0, 0, imageb.width, imageb.height, CenterB.x, CenterB.y, CenterB.width, CenterB.height);
            ctx.restore();
        }, function () {
            ctx.clearRect(0, 0, Method.width, Method.height);
            ctx.drawImage(imageb, 0, 0, imageb.width, imageb.height, CenterB.x, CenterB.y, CenterB.width, CenterB.height);
            finish();
        });
    };

    Method.Transitions.Corner = function (imagea, imageb, s, fps, finish) {
        var CenterA = Method.Math.Center(imagea, Method, Method.Math.Scale(imagea, Method));
        var CenterB = Method.Math.Center(imageb, Method, Method.Math.Scale(imageb, Method));
        var ctx = Method.getContext('2d');
        ctx.drawImage(imagea, 0, 0, imagea.width, imagea.height, CenterA.x, CenterA.y, CenterA.width, CenterA.height);
        Method.Render(fps, 0, s * 1000, function (r) {
            ctx.save();
            ctx.beginPath();
            ctx.rect(0, 0, Method.width * r.ratio, Method.height * r.ratio);
            ctx.closePath();
            ctx.globalCompositeOperation = 'destination-out';
            ctx.fill();
            ctx.globalCompositeOperation = 'source-over';
            ctx.clip();
            ctx.drawImage(imageb, 0, 0, imageb.width, imageb.height, CenterB.x, CenterB.y, CenterB.width, CenterB.height);
            ctx.restore();
        }, function () {
            ctx.clearRect(0, 0, Method.width, Method.height);
            ctx.drawImage(imageb, 0, 0, imageb.width, imageb.height, CenterB.x, CenterB.y, CenterB.width, CenterB.height);
            finish();
        });

    };
    Method.Transitions.Eraser = function (imagea, imageb, s, fps, finish) {
        var CenterA = Method.Math.Center(imagea, Method, Method.Math.Scale(imagea, Method));
        var CenterB = Method.Math.Center(imageb, Method, Method.Math.Scale(imageb, Method));
        var ctx = Method.getContext('2d');
        var tilesdata = Method.Math.SplitBlock(12, 12, Method.width, Method.height);
        var arrcount = tilesdata.length;
        ctx.drawImage(imagea, 0, 0, imagea.width, imagea.height, CenterA.x, CenterA.y, CenterA.width, CenterA.height);
        Method.Render(fps, 0, s * 1000, function (r) {
            ctx.save();
            ctx.beginPath();

            while ((tilesdata.length / arrcount) > (1 - r.ratio)) {
                var index = tilesdata.splice(0, 1)[0];
                ctx.rect(index["x"], index["y"], index["width"], index["height"]);
            }

            ctx.closePath();
            ctx.globalCompositeOperation = 'destination-out';
            ctx.fill();
            ctx.globalCompositeOperation = 'source-over';
            ctx.clip();
            ctx.drawImage(imageb, 0, 0, imageb.width, imageb.height, CenterB.x, CenterB.y, CenterB.width, CenterB.height);
            ctx.restore();
        }, function () {
            ctx.clearRect(0, 0, Method.width, Method.height);
            ctx.drawImage(imageb, 0, 0, imageb.width, imageb.height, CenterB.x, CenterB.y, CenterB.width, CenterB.height);
            finish();
        });
    };
    Method.Transitions.FadeOutFadeIn = function (imagea, imageb, s, fps, finish) {
        var CenterA = Method.Math.Center(imagea, Method, Method.Math.Scale(imagea, Method));
        var CenterB = Method.Math.Center(imageb, Method, Method.Math.Scale(imageb, Method));
        var ctx = Method.getContext('2d');
        Method.Render(fps, 0, s * 1000, function (r) {
            ctx.clearRect(0, 0, Method.width, Method.height);
            if (r.ratio < 0.5) {
                ctx.globalAlpha = 1 - r.ratio - 0.4;
                ctx.drawImage(imagea, 0, 0, imagea.width, imagea.height, CenterA.x, CenterA.y, CenterA.width, CenterA.height);
            } else if (r.ratio > 0.5 && r.ratio < 0.9) {
                ctx.globalAlpha = r.ratio - 0.1;
                ctx.drawImage(imageb, 0, 0, imageb.width, imageb.height, CenterB.x, CenterB.y, CenterB.width, CenterB.height);
            } else {
                ctx.globalAlpha = 1;
                ctx.drawImage(imageb, 0, 0, imageb.width, imageb.height, CenterB.x, CenterB.y, CenterB.width, CenterB.height);
            }
        }, function () {
            ctx.clearRect(0, 0, Method.width, Method.height);
            ctx.drawImage(imageb, 0, 0, imageb.width, imageb.height, CenterB.x, CenterB.y, CenterB.width, CenterB.height);
            finish();
        });
    };
    Method.Transitions.FromHorizontalCenter = function (imagea, imageb, s, fps, finish) {
        var CenterA = Method.Math.Center(imagea, Method, Method.Math.Scale(imagea, Method));
        var CenterB = Method.Math.Center(imageb, Method, Method.Math.Scale(imageb, Method));
        var ctx = Method.getContext('2d');
        var halfheight = Method.height / 2;
        ctx.drawImage(imagea, 0, 0, imagea.width, imagea.height, CenterA.x, CenterA.y, CenterA.width, CenterA.height);
        Method.Render(fps, 0, s * 1000, function (r) {
            ctx.save();
            ctx.beginPath();
            ctx.rect(0, halfheight, Method.width, halfheight * r.ratio);
            ctx.rect(0, halfheight * (1 - r.ratio), Method.width, halfheight * r.ratio);
            ctx.closePath();
            ctx.globalCompositeOperation = 'destination-out';
            ctx.fill();
            ctx.globalCompositeOperation = 'source-over';
            ctx.clip();
            ctx.drawImage(imageb, 0, 0, imageb.width, imageb.height, CenterB.x, CenterB.y, CenterB.width, CenterB.height);
            ctx.restore();
        }, function () {
            ctx.clearRect(0, 0, Method.width, Method.height);
            ctx.drawImage(imageb, 0, 0, imageb.width, imageb.height, CenterB.x, CenterB.y, CenterB.width, CenterB.height);
            finish();
        });
    };
    Method.Transitions.FromVerticalCenter = function (imagea, imageb, s, fps, finish) {
        var CenterA = Method.Math.Center(imagea, Method, Method.Math.Scale(imagea, Method));
        var CenterB = Method.Math.Center(imageb, Method, Method.Math.Scale(imageb, Method));
        var ctx = Method.getContext('2d');
        var halfwidth = Method.width / 2;
        ctx.drawImage(imagea, 0, 0, imagea.width, imagea.height, CenterA.x, CenterA.y, CenterA.width, CenterA.height);
        Method.Render(fps, 0, s * 1000, function (r) {
            ctx.save();
            ctx.beginPath();
            ctx.rect(halfwidth, 0, halfwidth * r.ratio, Method.height);
            ctx.rect(halfwidth * (1 - r.ratio), 0, halfwidth * r.ratio, Method.height);
            ctx.closePath();
            ctx.globalCompositeOperation = 'destination-out';
            ctx.fill();
            ctx.globalCompositeOperation = 'source-over';
            ctx.clip();
            ctx.drawImage(imageb, 0, 0, imageb.width, imageb.height, CenterB.x, CenterB.y, CenterB.width, CenterB.height);
            ctx.restore();
        }, function () {
            ctx.clearRect(0, 0, Method.width, Method.height);
            ctx.drawImage(imageb, 0, 0, imageb.width, imageb.height, CenterB.x, CenterB.y, CenterB.width, CenterB.height);
            finish();
        });
    };
    Method.Transitions.HeartOut = function (imagea, imageb, s, fps, finish) {
        var CenterA = Method.Math.Center(imagea, Method, Method.Math.Scale(imagea, Method));
        var CenterB = Method.Math.Center(imageb, Method, Method.Math.Scale(imageb, Method));
        var centerx = Method.width / 2;
        var centerylock = Method.height / 2;
        var max = Math.max(Method.width, Method.height);
        var ctx = Method.getContext('2d');
        ctx.drawImage(imagea, 0, 0, imagea.width, imagea.height, CenterA.x, CenterA.y, CenterA.width, CenterA.height);
        Method.Render(fps, 0, s * 1000, function (r) {
            var r = max * r.ratio;
            var centery = centerylock - (r / 3);
            ctx.save();
            ctx.beginPath();
            ctx.moveTo(centerx, centery);
            ctx.quadraticCurveTo(centerx + (r * 0.5), centery - r, centerx + r, centery);
            ctx.quadraticCurveTo(centerx + (r / 0.90), centery + (r * 0.25), centerx, centery + r);
            ctx.moveTo(centerx, centery);
            ctx.quadraticCurveTo(centerx - (r * 0.5), centery - r, centerx - r, centery);
            ctx.quadraticCurveTo(centerx - (r / 0.90), centery + (r * 0.25), centerx, centery + r);
            ctx.closePath();
            ctx.globalCompositeOperation = 'destination-out';
            ctx.fill();
            ctx.globalCompositeOperation = 'source-over';
            ctx.clip();
            ctx.drawImage(imageb, 0, 0, imageb.width, imageb.height, CenterB.x, CenterB.y, CenterB.width, CenterB.height);
            ctx.restore();
        }, function () {
            ctx.clearRect(0, 0, Method.width, Method.height);
            ctx.drawImage(imageb, 0, 0, imageb.width, imageb.height, CenterB.x, CenterB.y, CenterB.width, CenterB.height);
            finish();
        });
    };
    Method.Transitions.HorizontalBlind = function (imagea, imageb, s, fps, finish) {
        var CenterA = Method.Math.Center(imagea, Method, Method.Math.Scale(imagea, Method));
        var CenterB = Method.Math.Center(imageb, Method, Method.Math.Scale(imageb, Method));
        var ctx = Method.getContext('2d');
        var bar = 12;
        var barheight = Method.height / bar;
        ctx.drawImage(imagea, 0, 0, imagea.width, imagea.height, CenterA.x, CenterA.y, CenterA.width, CenterA.height);
        Method.Render(fps, 0, s * 1000, function (r) {
            ctx.save();
            ctx.beginPath();
            for (var i = 0; i < bar; i++) {
                ctx.rect(0, barheight * i, Method.width, barheight * r.ratio);
            }
            ctx.closePath();
            ctx.globalCompositeOperation = 'destination-out';
            ctx.fill();
            ctx.globalCompositeOperation = 'source-over';
            ctx.clip();
            ctx.drawImage(imageb, 0, 0, imageb.width, imageb.height, CenterB.x, CenterB.y, CenterB.width, CenterB.height);
            ctx.restore();
        }, function () {
            ctx.clearRect(0, 0, Method.width, Method.height);
            ctx.drawImage(imageb, 0, 0, imageb.width, imageb.height, CenterB.x, CenterB.y, CenterB.width, CenterB.height);
            finish();
        });
    };
    Method.Transitions.LeftToRight = function (imagea, imageb, s, fps, finish) {
        var CenterA = Method.Math.Center(imagea, Method, Method.Math.Scale(imagea, Method));
        var CenterB = Method.Math.Center(imageb, Method, Method.Math.Scale(imageb, Method));
        var ctx = Method.getContext('2d');
        ctx.drawImage(imagea, 0, 0, imagea.width, imagea.height, CenterA.x, CenterA.y, CenterA.width, CenterA.height);
        Method.Render(fps, 0, s * 1000, function (r) {
            ctx.save();
            ctx.beginPath();
            ctx.rect(Method.width * r.ratio, 0, Method.width * r.ratio, Method.height);
            ctx.closePath();
            ctx.globalCompositeOperation = 'destination-out';
            ctx.fill();
            ctx.globalCompositeOperation = 'source-over';
            ctx.clip();
            ctx.drawImage(imageb, 0, 0, imageb.width, imageb.height, CenterB.x, CenterB.y, CenterB.width, CenterB.height);
            ctx.restore();
        }, function () {
            ctx.clearRect(0, 0, Method.width, Method.height);
            ctx.drawImage(imageb, 0, 0, imageb.width, imageb.height, CenterB.x, CenterB.y, CenterB.width, CenterB.height);
            finish();
        });
    };

    Method.Transitions.Mosaic = function (imagea, imageb, s, fps, finish) {
        var CenterA = Method.Math.Center(imagea, Method, Method.Math.Scale(imagea, Method));
        var CenterB = Method.Math.Center(imageb, Method, Method.Math.Scale(imageb, Method));
        var ctx = Method.getContext('2d');
        var tilesdata = Method.Math.SplitBlock(12, 12, Method.width, Method.height);
        var arrcount = tilesdata.length;
        ctx.drawImage(imagea, 0, 0, imagea.width, imagea.height, CenterA.x, CenterA.y, CenterA.width, CenterA.height);
        Method.Render(fps, 0, s * 1000, function (r) {
            ctx.save();
            ctx.beginPath();

            while ((tilesdata.length / arrcount) > (1 - r.ratio)) {
                var index = tilesdata.splice(tilesdata.length * Math.random() | 0, 1)[0];
                ctx.rect(index["x"], index["y"], index["width"], index["height"]);
            }

            ctx.closePath();
            ctx.globalCompositeOperation = 'destination-out';
            ctx.fill();
            ctx.globalCompositeOperation = 'source-over';
            ctx.clip();
            ctx.drawImage(imageb, 0, 0, imageb.width, imageb.height, CenterB.x, CenterB.y, CenterB.width, CenterB.height);
            ctx.restore();
        }, function () {
            ctx.clearRect(0, 0, Method.width, Method.height);
            ctx.drawImage(imageb, 0, 0, imageb.width, imageb.height, CenterB.x, CenterB.y, CenterB.width, CenterB.height);
            finish();
        });
    };

    Method.Transitions.PageTurn = function (imagea, imageb, s, fps, finish) {
        var CenterA = Method.Math.Center(imagea, Method, Method.Math.Scale(imagea, Method));
        var CenterB = Method.Math.Center(imageb, Method, Method.Math.Scale(imageb, Method));
        var ctx = Method.getContext('2d');
        var p = (Math.sqrt(Math.pow(Method.width, 2) + Math.pow(Method.height, 2))) * 1.2;

        Method.Render(fps, 0, s * 1000, function (r) {
            var x = (p * r.ratio) + 0.1;
            var y = (p * r.ratio) + 0.1;
            ctx.save();
            ctx.fillRect(0, 0, Method.width, Method.height);
            ctx.drawImage(imageb, 0, 0, imageb.width, imageb.height, CenterB.x, CenterB.y, CenterB.width, CenterB.height);
            ctx.beginPath();
            ctx.moveTo(y * y / 2 / x + x / 2, 0);
            ctx.lineTo(Method.width * 2, 0);
            ctx.lineTo(0, Method.height * 2);
            ctx.lineTo(0, x * x / 2 / y + y / 2);
            ctx.closePath();
            ctx.globalCompositeOperation = 'destination-out';
            ctx.fill();
            ctx.globalCompositeOperation = 'source-over';
            ctx.clip();
            ctx.drawImage(imagea, 0, 0, imagea.width, imagea.height, CenterA.x, CenterA.y, CenterA.width, CenterA.height);
            ctx.translate(x, y);
            ctx.rotate(Math.atan2(y, x) * 2);
            ctx.scale(-1, 1);
            ctx.drawImage(imagea, 0, 0, imagea.width, imagea.height, CenterA.x, CenterA.y, CenterA.width, CenterA.height);
            ctx.restore();

        }, function () {
            ctx.clearRect(0, 0, Method.width, Method.height);
            ctx.drawImage(imageb, 0, 0, imageb.width, imageb.height, CenterB.x, CenterB.y, CenterB.width, CenterB.height);
            finish();
        });
    };

    Method.Transitions.RightToLeft = function (imagea, imageb, s, fps, finish) {
        var CenterA = Method.Math.Center(imagea, Method, Method.Math.Scale(imagea, Method));
        var CenterB = Method.Math.Center(imageb, Method, Method.Math.Scale(imageb, Method));
        var ctx = Method.getContext('2d');
        ctx.drawImage(imagea, 0, 0, imagea.width, imagea.height, CenterA.x, CenterA.y, CenterA.width, CenterA.height);
        Method.Render(fps, 0, s * 1000, function (r) {
            ctx.save();
            ctx.beginPath();
            ctx.rect(Method.width * (1 - r.ratio), 0, Method.width * r.ratio, Method.height);
            ctx.closePath();
            ctx.globalCompositeOperation = 'destination-out';
            ctx.fill();
            ctx.globalCompositeOperation = 'source-over';
            ctx.clip();
            ctx.drawImage(imageb, 0, 0, imageb.width, imageb.height, CenterB.x, CenterB.y, CenterB.width, CenterB.height);
            ctx.restore();
        }, function () {
            ctx.clearRect(0, 0, Method.width, Method.height);
            ctx.drawImage(imageb, 0, 0, imageb.width, imageb.height, CenterB.x, CenterB.y, CenterB.width, CenterB.height);
            finish();
        });
    };
    Method.Transitions.SpinRight = function (imagea, imageb, s, fps, finish) {
        var CenterA = Method.Math.Center(imagea, Method, Method.Math.Scale(imagea, Method));
        var CenterB = Method.Math.Center(imageb, Method, Method.Math.Scale(imageb, Method));
        var max = Math.max(Method.width, Method.height);
        var p = 5;
        var m = 0.5;
        var ctx = Method.getContext('2d');
        ctx.drawImage(imagea, 0, 0, imagea.width, imagea.height, CenterA.x, CenterA.y, CenterA.width, CenterA.height);
        Method.Render(fps, 0, s * 1000, function (r) {
            if (r.ratio < 0.8) {
                ctx.save();
                ctx.clearRect(0, 0, Method.width, Method.height);
                ctx.translate(Method.width / 2, Method.height / 2);
                ctx.rotate((12 * 360 * r.ratio) * Math.PI / 180);
                ctx.translate(-(Method.width / 2), -(Method.height / 2));
                ctx.drawImage(imageb, 0, 0, imageb.width, imageb.height, CenterB.x, CenterB.y, CenterB.width, CenterB.height);
                ctx.restore();
            } else {
                ctx.clearRect(0, 0, Method.width, Method.height);
                ctx.drawImage(imageb, 0, 0, imageb.width, imageb.height, CenterB.x, CenterB.y, CenterB.width, CenterB.height);
            }
        }, function () {
            ctx.clearRect(0, 0, Method.width, Method.height);
            ctx.drawImage(imageb, 0, 0, imageb.width, imageb.height, CenterB.x, CenterB.y, CenterB.width, CenterB.height);
            finish();
        });
    };
    Method.Transitions.StarOut = function (imagea, imageb, s, fps, finish) {
        var CenterA = Method.Math.Center(imagea, Method, Method.Math.Scale(imagea, Method));
        var CenterB = Method.Math.Center(imageb, Method, Method.Math.Scale(imageb, Method));
        var max = Math.max(Method.width, Method.height);
        var p = 5;
        var m = 0.5;
        var ctx = Method.getContext('2d');
        ctx.drawImage(imagea, 0, 0, imagea.width, imagea.height, CenterA.x, CenterA.y, CenterA.width, CenterA.height);
        Method.Render(fps, 0, s * 1000, function (r) {
            var r = max * r.ratio;
            ctx.save();
            ctx.translate(Method.width / 2, Method.height / 2);
            ctx.beginPath();
            for (var i = 0; i < p; i++)
            {
                ctx.rotate(Math.PI / p);
                ctx.lineTo(0, -(r * m));
                ctx.rotate(Math.PI / p);
                ctx.lineTo(0, -r);
            }
            ctx.closePath();
            ctx.globalCompositeOperation = 'destination-out';
            ctx.fill();
            ctx.translate(-Method.width / 2, -Method.height / 2);
            ctx.globalCompositeOperation = 'source-over';
            ctx.clip();
            ctx.drawImage(imageb, 0, 0, imageb.width, imageb.height, CenterB.x, CenterB.y, CenterB.width, CenterB.height);
            ctx.restore();
        }, function () {
            ctx.clearRect(0, 0, Method.width, Method.height);
            ctx.drawImage(imageb, 0, 0, imageb.width, imageb.height, CenterB.x, CenterB.y, CenterB.width, CenterB.height);
            finish();
        });

    };

    Method.Transitions.ToHorizontalCenter = function (imagea, imageb, s, fps, finish) {
        var CenterA = Method.Math.Center(imagea, Method, Method.Math.Scale(imagea, Method));
        var CenterB = Method.Math.Center(imageb, Method, Method.Math.Scale(imageb, Method));
        var ctx = Method.getContext('2d');
        var halfheight = Method.height / 2;
        ctx.drawImage(imagea, 0, 0, imagea.width, imagea.height, CenterA.x, CenterA.y, CenterA.width, CenterA.height);
        Method.Render(fps, 0, s * 1000, function (r) {
            ctx.save();
            ctx.beginPath();
            ctx.rect(0, 0, Method.width, halfheight * r.ratio);
            ctx.rect(0, halfheight + (halfheight * (1 - r.ratio)), Method.width, halfheight * r.ratio);
            ctx.closePath();
            ctx.globalCompositeOperation = 'destination-out';
            ctx.fill();
            ctx.globalCompositeOperation = 'source-over';
            ctx.clip();
            ctx.drawImage(imageb, 0, 0, imageb.width, imageb.height, CenterB.x, CenterB.y, CenterB.width, CenterB.height);
            ctx.restore();
        }, function () {
            ctx.clearRect(0, 0, Method.width, Method.height);
            ctx.drawImage(imageb, 0, 0, imageb.width, imageb.height, CenterB.x, CenterB.y, CenterB.width, CenterB.height);
            finish();
        });
    };

    Method.Transitions.ToVerticalCenter = function (imagea, imageb, s, fps, finish) {
        var CenterA = Method.Math.Center(imagea, Method, Method.Math.Scale(imagea, Method));
        var CenterB = Method.Math.Center(imageb, Method, Method.Math.Scale(imageb, Method));
        var ctx = Method.getContext('2d');
        var halfwidth = Method.width / 2;
        ctx.drawImage(imagea, 0, 0, imagea.width, imagea.height, CenterA.x, CenterA.y, CenterA.width, CenterA.height);
        Method.Render(fps, 0, s * 1000, function (r) {
            ctx.save();
            ctx.beginPath();
            ctx.rect(halfwidth + (halfwidth * (1 - r.ratio)), 0, halfwidth * r.ratio, Method.height);
            ctx.rect(0, 0, halfwidth * r.ratio, Method.height);
            ctx.closePath();
            ctx.globalCompositeOperation = 'destination-out';
            ctx.fill();
            ctx.globalCompositeOperation = 'source-over';
            ctx.clip();
            ctx.drawImage(imageb, 0, 0, imageb.width, imageb.height, CenterB.x, CenterB.y, CenterB.width, CenterB.height);
            ctx.restore();
        }, function () {
            ctx.clearRect(0, 0, Method.width, Method.height);
            ctx.drawImage(imageb, 0, 0, imageb.width, imageb.height, CenterB.x, CenterB.y, CenterB.width, CenterB.height);
            finish();
        });
    };

    Method.Transitions.TopToBottom = function (imagea, imageb, s, fps, finish) {
        var CenterA = Method.Math.Center(imagea, Method, Method.Math.Scale(imagea, Method));
        var CenterB = Method.Math.Center(imageb, Method, Method.Math.Scale(imageb, Method));
        var ctx = Method.getContext('2d');
        ctx.drawImage(imagea, 0, 0, imagea.width, imagea.height, CenterA.x, CenterA.y, CenterA.width, CenterA.height);
        Method.Render(fps, 0, s * 1000, function (r) {
            ctx.save();
            ctx.beginPath();
            ctx.rect(0, 0, Method.width, Method.height * r.ratio);
            ctx.closePath();
            ctx.globalCompositeOperation = 'destination-out';
            ctx.fill();
            ctx.globalCompositeOperation = 'source-over';
            ctx.clip();
            ctx.drawImage(imageb, 0, 0, imageb.width, imageb.height, CenterB.x, CenterB.y, CenterB.width, CenterB.height);
            ctx.restore();
        }, function () {
            ctx.clearRect(0, 0, Method.width, Method.height);
            ctx.drawImage(imageb, 0, 0, imageb.width, imageb.height, CenterB.x, CenterB.y, CenterB.width, CenterB.height);
            finish();
        });
    };

    Method.Transitions.VerticalBlind = function (imagea, imageb, s, fps, finish) {
        var CenterA = Method.Math.Center(imagea, Method, Method.Math.Scale(imagea, Method));
        var CenterB = Method.Math.Center(imageb, Method, Method.Math.Scale(imageb, Method));
        var ctx = Method.getContext('2d');
        var bar = 12;
        var barwidth = Method.width / bar;
        ctx.drawImage(imagea, 0, 0, imagea.width, imagea.height, CenterA.x, CenterA.y, CenterA.width, CenterA.height);
        Method.Render(fps, 0, s * 1000, function (r) {
            ctx.save();
            ctx.beginPath();
            for (var i = 0; i < bar; i++) {
                ctx.rect(barwidth * i, 0, barwidth * r.ratio, Method.height);
            }
            ctx.closePath();
            ctx.globalCompositeOperation = 'destination-out';
            ctx.fill();
            ctx.globalCompositeOperation = 'source-over';
            ctx.clip();
            ctx.drawImage(imageb, 0, 0, imageb.width, imageb.height, CenterB.x, CenterB.y, CenterB.width, CenterB.height);
            ctx.restore();
        }, function () {
            ctx.clearRect(0, 0, Method.width, Method.height);
            ctx.drawImage(imageb, 0, 0, imageb.width, imageb.height, CenterB.x, CenterB.y, CenterB.width, CenterB.height);
            finish();
        });
    };
    Method.Transitions.ZoomIn = function (imagea, imageb, s, fps, finish) {
        var CenterB = Method.Math.Center(imageb, Method, Method.Math.Scale(imageb, Method));
        var ctx = Method.getContext('2d');
        var cx = Method.width / 2;
        var cy = Method.height / 2;
        var maxscale = Method.Math.Scale(imagea, Method);
        Method.Render(fps, 0, s * 1000, function (r) {
            var resize = maxscale + (maxscale * r.ratio);
            ctx.clearRect(0, 0, Method.width, Method.height);
            ctx.save();
            ctx.translate(cx, cy);
            ctx.scale(resize, resize);
            ctx.drawImage(imagea, -imagea.width / 2, -imagea.height / 2);
            ctx.restore();
        }, function () {
            ctx.clearRect(0, 0, Method.width, Method.height);
            ctx.drawImage(imageb, 0, 0, imageb.width, imageb.height, CenterB.x, CenterB.y, CenterB.width, CenterB.height);
            finish();
        });
    };
    Method.Transitions.ZoomOut = function (imagea, imageb, s, fps, finish) {
        var CenterB = Method.Math.Center(imageb, Method, Method.Math.Scale(imageb, Method));
        var ctx = Method.getContext('2d');
        var cx = Method.width / 2;
        var cy = Method.height / 2;
        var maxscale = Method.Math.Scale(imagea, Method);
        Method.Render(fps, 0, s * 1000, function (r) {
            var resize = maxscale + (maxscale * (1 - r.ratio));
            ctx.clearRect(0, 0, Method.width, Method.height);
            ctx.save();
            ctx.translate(cx, cy);
            ctx.scale(resize, resize);
            ctx.drawImage(imagea, -imagea.width / 2, -imagea.height / 2);
            ctx.restore();
        }, function () {
            ctx.clearRect(0, 0, Method.width, Method.height);
            ctx.drawImage(imageb, 0, 0, imageb.width, imageb.height, CenterB.x, CenterB.y, CenterB.width, CenterB.height);
            finish();
        });
    };

    return Method;
}

 */