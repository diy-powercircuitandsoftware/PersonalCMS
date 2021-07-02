class PointPoint_Player {
    constructor(...args) {
        if (args.length === 1 && typeof args[0] === 'string' || args[0] instanceof String) {
            this.player = document.querySelector(args[0]).appendChild(document.createElement("div"));
        } else if (args.length === 1 && args[0] instanceof HTMLElement) {
            this.player = args[0].appendChild(document.createElement("div"));
        } else {
            this.player = document.body.appendChild(document.createElement("div"));
        }
    }
    SetDom(dom) {
        console.log(dom);
         this.player.innerHTML="";
        this.player.appendChild(dom);
    }
    AddPlayerEvent(...args) {
        this.player.addEventListener(...args);
    }
    
}
class   PointPoint_Player_Animation_Render{
     constructor(...args) {
         
     }
     HasAnimation(){
         
     }
}
class PointPoint_Player_Timer {
    constructor(fps = 60) {
        this.requestID = 0;
        this.fps = fps;
        this.animate = function () {};
    }
    SetAnimate(animate) {
        this.animate = animate;
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