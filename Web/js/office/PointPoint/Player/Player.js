class PointPoint_Player {
    constructor(...args) {
        if (args.length === 1 && typeof args[0] === 'string' || args[0] instanceof String) {
            this.player = document.querySelector(args[0]).appendChild(document.createElement("div"));
        } else if (args.length === 1 && args[0] instanceof HTMLElement) {
            this.player = args[0].appendChild(document.createElement("div"));
        } else {
            this.player = document.body.appendChild(document.createElement("div"));
        }
        this.player.style.overflow = "hidden";
        this.Animation = {};
        this.DomsAnimation = [];
        this.Timer = new PointPoint_Player_Timer();
    }

    AddAnimation(name, classname) {
        this.Animation[name] = classname;
    }
    Click() {
        this.player.click();
    }
    End() {
        var size = this.player.getBoundingClientRect();
        this.player.innerHTML = "";
        var div = this.player.appendChild(document.createElement("DIV"));
        div.style.width = size.width + "px";
        div.style.height = size.height + "px";
        div.innerHTML = '<div style="text-align: center;">end of presentation</div>';
    }
    HasAnimation() {
        return this.DomsAnimation.length > 0;
    }
    PlayAnimation() {
        if (this.HasAnimation()) {
            var frist = this.DomsAnimation[0];

            if (frist.animationclass !== undefined) {
                frist.animationclass.End();
                this.DomsAnimation.shift();
                
            } else {

                this.DomsAnimation.shift();
            }

        }


    }
    SetDom(dom) {
        this.player.innerHTML = "";
        this.player.appendChild(dom);
        var ref = this;
        ref.DomsAnimation = [];
        [].forEach.call(dom.querySelectorAll("[pointpoint-animate]"), function (d) {
            if (d.getAttribute("pointpoint-animate") != "") {

                ref.DomsAnimation.push(d);

                var animatename = (d.getAttribute("pointpoint-animate"));
                var animationclass = ref.Animation;
                if (animationclass.hasOwnProperty(animatename)) {

                    d.animationclass = new animationclass[animatename](d);
                } else {

                    d.setAttribute("pointpoint-animate", "");
                }




            }
        });
    }
    AddPlayerEvent(...args) {
        this.player.addEventListener(...args);
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
        cancelAnimationFrame(this.requestID);
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

class PointPoint_Player_Animation {
    constructor(dom) {
        this.playing = false;
    }
    GetName() {
        return this.constructor.name;
    }
    GetClassName() {
        return this.constructor.name;
    }
    Render(fps) {

    }
    End( ) {

    }
}