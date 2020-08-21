class PointPoint_Editor {
    constructor(...args) {
        if (args.length === 1 && typeof args[0] === 'string' || args[0] instanceof String) {
            this.editor = document.querySelector(args[0]).appendChild(document.createElement("DIV"));
        } else if (args.length === 1 && args[0] instanceof HTMLElement) {
            this.editor = args[0].appendChild(document.createElement("DIV"));
        } else {
            this.editor = document.body.appendChild(document.createElement("DIV"));
        }
    }
}
class PointPoint_Player {

}
class PointPoint_Player_RenderEngine {
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
class PointPoint_Animation {

}
class PointPoint_Object {
    Sound() {

    }
    Animation() {

    }
}
class PointPoint_Slide {

}






