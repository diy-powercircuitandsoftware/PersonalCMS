class PointPoint_Player {
    constructor(...args) {
        if (args.length === 1 && typeof args[0] === 'string' || args[0] instanceof String) {
            this.player = document.querySelector(args[0]).appendChild(document.createElement("div"));
        } else if (args.length === 1 && args[0] instanceof HTMLElement) {
            this.player = args[0].appendChild(document.createElement("div"));
        } else {
            this.player = document.body.appendChild(document.createElement("div"));
        }
        this.audioplayer = document.body.appendChild(document.createElement("audio"));
        this.player.style.overflow = "hidden";
        this.Animation = {};
        this.AudioResource = {};
        this.DomsAnimation = [];
        this.Timer = new PointPoint_Player_Timer();
        this.Timer.Start();
    }

    AddAnimation(name, classname) {
        this.Animation[name] = classname;
    }
    AddAudioFromResource(name, path) {
        this.AudioResource[name] = path;
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
    FullScreen() {
        var playerrect = this.player.getBoundingClientRect();
        var sw = window.innerWidth / playerrect.width;
        var sh = window.innerHeight / playerrect.height;
        var minswsh = Math.min(sw, sh);
        this.player.style.width = window.innerWidth + "px";
        this.player.style.height = window.innerHeight + "px";

        [].forEach.call(this.player.querySelectorAll("[pointpoint-type]"), function (d) {
            if (d.getAttribute("pointpoint-type") == "slide") {
                d.style.width = window.innerWidth + "px";
                d.style.height = window.innerHeight + "px";
            } else {
                var fontsize = parseInt(window.getComputedStyle(d, null).getPropertyValue('font-size'));
                d.style.fontSize = (fontsize * minswsh) + "px";

            }
        });
    }
    HasAnimation() {
        return this.DomsAnimation.length > 0;
    }
    PlayAnimation() {

        if (this.HasAnimation()) {
            var frist = this.DomsAnimation[0];
            var ref = this;
            if (frist.animationclass !== undefined) {
                if (frist.animationclass.playing) {
                    frist.animationclass.End();
                    this.DomsAnimation.shift();
                } else if (!frist.animationclass.ended) {
                    this.Timer.SetAnimate(function (fps) {
                        frist.animationclass.Render(fps);
                    });

                    var audioname = (frist.getAttribute("pointpoint-animate-audio"));
                    if (ref.AudioResource.hasOwnProperty(audioname)) {                      
                        ref.audioplayer.pause();
                        ref.audioplayer.src = ref.AudioResource[audioname];
                        ref.audioplayer.play();
                    }
                    frist.animationclass.Start();

                } else {
                    this.DomsAnimation.shift();
                    this.PlayAnimation();
                }
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
        var ref = this;
        cancelAnimationFrame(this.requestID);
        let then = performance.now();
        const interval = 1000 / this.fps;
        const tolerance = 0.1;
        const animateLoop = (now) => {
            const delta = now - then;
            if (delta >= interval - tolerance) {
                then = now - (delta % interval);
                ref.animate(delta);
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
        this.ended = false;
    }
    Start() {
        this.playing = true;
    }
    GetName() {
        return this.constructor.name;
    }
    GetClassName() {
        return this.constructor.name;
    }
    Render(fps) {

    }
    Stop( ) {
        this.playing = false;
        this.ended = true;
    }

}