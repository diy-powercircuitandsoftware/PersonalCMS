
class PointPoint_Animation_Hide extends PointPoint_Player_Animation {
    constructor(dom) {
        super(dom);
        if (dom !== null) {
            this.dom = dom;
            this.dom.style.display = "none";
        }
    }
    GetName() {
        return "Hide";
    }
    Render(fps) {
         this.dom.style.display = "";
         this.Stop();
    }
    Stop( ) {
        this.dom.style.display = "";
        super.Stop();
    }
}
;
