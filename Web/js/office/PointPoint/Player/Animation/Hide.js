
class PointPoint_Animation_Hide extends PointPoint_Player_Animation {
    constructor(dom) {
        super();
        this.dom = dom;
        this.dom.style.display = "none";
    }
    GetName() {
        return "Hide";
    }
    Render(fps) {
        this.dom.style.display = "";
    }
    End( ) {
        this.dom.style.display = "";
    }
}
;
