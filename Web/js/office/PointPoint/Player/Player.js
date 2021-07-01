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
