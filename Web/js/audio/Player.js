class AudioPlayer {
    constructor(...args) {
        if (args.length === 1 && typeof args[0] === 'string' || args[0] instanceof String) {
            this.audio = document.querySelector(args[0]).appendChild(document.createElement("AUDIO"));
        } else if (args.length === 1 && args[0] instanceof HTMLElement) {
            this.audio = args[0].appendChild(document.createElement("AUDIO"));
        } else {
            this.audio = document.body.appendChild(document.createElement("AUDIO"));
        }
        this.audio.style = "width:100%";
        this.audio.controls = "controls";
    }
    Play() {
        this.audio.play();
    }
    Stop() {
        this.audio.pause();
    }
    Src(src) {
        this.audio.src = src;
    }
}
class Visualizer {
    constructor(...args) {
        if (args.length === 1 && typeof args[0] === 'string' || args[0] instanceof String) {
            this.canvas = document.querySelector(args[0]).appendChild(document.createElement("CANVAS"));
        } else if (args.length === 1 && args[0] instanceof HTMLElement) {
            this.canvas = args[0].appendChild(document.createElement("CANVAS"));
        } else {
            this.canvas = document.body.appendChild(document.createElement("CANVAS"));
        }
    }
    Size(w, h) {
        this.canvas.width = w;
        this.canvas.height = h;
    }
}