class AudioPlayer {
    //https://docs.w3cub.com/dom/baseaudiocontext/createbiquadfilter/
    //https://github.com/mmckegg/soundbank-pitch-shift/blob/master/index.js
    constructor(...args) {
        this.ctx = new AudioContext();
        this.source;
        this.analyser = this.ctx.createAnalyser();
        this.filter = [];
        if (args.length === 1 && typeof args[0] === 'string' || args[0] instanceof String) {
            this.audio = document.querySelector(args[0]).appendChild(document.createElement("AUDIO"));
        } else if (args.length === 1 && args[0] instanceof HTMLElement) {
            this.audio = args[0].appendChild(document.createElement("AUDIO"));
        } else {
            this.audio = document.body.appendChild(document.createElement("AUDIO"));
        }
        this.audio.style = "width:100%";
        this.audio.controls = "controls";
        this.audio.autoplay = "autoplay";
        this.audio.ref = this;
        this.audio.addEventListener("ended", function () {
            this.ref.End();
        });
        this.source = this.ctx.createMediaElementSource(this.audio);
        this.source.connect(this.analyser);
        this.analyser.connect(this.ctx.destination);

    }
    AddFilter(frequency, q, gain, type) {
        var filter = this.ctx.createBiquadFilter();
        filter.type = type;
        filter.gain.value = gain;
        filter.Q.value = q;
        filter.frequency.value = frequency;
        this.filter.push(filter);
    }
//  {"frequency": 32, "Q": 1, "gain": 0, "type": Method.BiquadFilterType.lowshelf},

    End(v) {
        if (typeof v === "function") {

            this.End = v;
        }
    }
    GetAnalyser() {
        return  this.analyser;
    }
    Play() {

        this.ctx.resume().then(function () {

        });
        return this.audio.play();
    }
    Stop() {
        this.audio.pause();
    }
    Src(src) {
        this.audio.src = src;
    }
}
class AudioPlayer_Visualizer {
    constructor(player, renderengine) {
        if (player instanceof AudioPlayer && renderengine instanceof AudioPlayer_Visualizer_RenderEngine) {
            this.analyser = player.GetAnalyser();
            this.renderengine = renderengine;
        }

    }

    Bar() {
        var analyser = this.analyser;
        var canvasctx = this.renderengine.canvas.getContext("2d");
        // analyser.fftSize="";

        this.renderengine.SetAnimate(function (v) {
            var bufferLength = analyser.frequencyBinCount;
            var dataArray = new Uint8Array(bufferLength);
            analyser.getByteFrequencyData(dataArray);
            canvasctx.fillStyle = "orange";
            canvasctx.clearRect(0, 0, canvasctx.canvas.width, canvasctx.canvas.height);
            var barWidth = (canvasctx.canvas.width / bufferLength);
            var barHeight;
            var x = 0;
            for (var i = 0; i < bufferLength; i++) {
                barHeight = dataArray[i];
                canvasctx.fillRect(x, canvasctx.canvas.height - barHeight / 2, barWidth, barHeight / 2);
                x += barWidth + 1;
            }
        });
    }
    Sine() {
        var analyser = this.analyser;
        var canvasctx = this.renderengine.canvas.getContext("2d");

        this.renderengine.SetAnimate(function (v) {
             
            var bufferLength = analyser.frequencyBinCount;
            var dataArray = new Uint8Array(bufferLength);
            var WIDTH = canvasctx.canvas.width;
            var HEIGHT = canvasctx.canvas.height;
            analyser.getByteTimeDomainData(dataArray);
            canvasctx.clearRect(0, 0, canvasctx.canvas.width, canvasctx.canvas.height);
            canvasctx.lineWidth = 2;
            canvasctx.strokeStyle = 'green';

            canvasctx.beginPath();

            var sliceWidth = WIDTH * 1.0 / bufferLength;
            var x = 0;

            for (var i = 0; i < bufferLength; i++) {

                var v = dataArray[i] / 128.0;
                var y = v * HEIGHT / 2;

                if (i === 0) {
                    canvasctx.moveTo(x, y);
                } else {
                    canvasctx.lineTo(x, y);
                }

                x += sliceWidth;
            }

            canvasctx.lineTo(WIDTH, HEIGHT / 2);
            canvasctx.stroke();
        });



    }
}
class AudioPlayer_Visualizer_RenderEngine {
    constructor(...args) {
        if (typeof args[0] === 'string' || args[0] instanceof String) {
            this.canvas = document.querySelector(args[0]).appendChild(document.createElement("CANVAS"));
        } else if (args[0] instanceof HTMLElement) {
            this.canvas = args[0].appendChild(document.createElement("CANVAS"));
        }
        this.requestID = 0;
        this.fps = args[1];
        this.animate = function () {};
    }
    SetAnimate(animate) {
        this.animate = animate;
    }
    Size(w, h) {
        this.canvas.width = w;
        this.canvas.height = h;
    }
    Start() {
        let then = performance.now();
        const interval = 1000 / this.fps;
        const tolerance = 0.1;
        const animateLoop = (now) => {

            const delta = now - then;
            if (delta >= interval - tolerance) {
                then = now - (delta % interval);
                this.animate(delta);
            }
            this.requestID = requestAnimationFrame(animateLoop);
        };
        this.requestID = requestAnimationFrame(animateLoop);
    }

    Stop() {
        cancelAnimationFrame(this.requestID);
    }
}