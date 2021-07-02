class PointPoint_Animation {
    GetName() {

    }
    Render() {

    }
}


class PointPoint_Player {
    constructor(...args) {
       
        this.domlist = this.div.appendChild(document.createElement("div"));
    

        this.slides = [];
        this.slidesindex = -1;
        this.slidesitemindex = 0;
        this.domlist.style.position = "relative";
        /* var render = new PointPoint_Player_RenderEngine();
         render.ref = this;
         render.SetAnimate(function (v) {
         if (this.ref.slidesindex < this.ref.slides.length) {
         var ctx = this.ref.canvas.getContext('2d');
         
         if (slideobj !== null) {
         var root = slideobj.GetSlideData();
         var rootwidth = root.getAttribute("width");
         var rootheight = root.getAttribute("height");
         if (this.ref.canvas.width == rootwidth && this.ref.canvas.height == rootheight) {
         ctx.clearRect(0, 0, rootwidth, rootheight);
         var cn = root.childNodes;
         
         for (var i = 0; i < Math.min(cn.length, this.ref.slidesitemindex); i++) {
         
         
         if (cn[i].tagName == "text") {
         
         var x = (parseInt(rootwidth) / 100) * (parseFloat(cn[i].getAttribute("x")));
         var y = (parseInt(rootheight) / 100) * (parseFloat(cn[i].getAttribute("y")));
         //animation
         var textnode = cn[i].childNodes;
         //x=0;
         for (var itn = 0; itn < textnode.length; itn++) {
         
         
         if (textnode[itn].tagName == "text") {
         
         }
         //                                 
         }
         // ctx.font = '48px serif';
         
         //     ctx.fillStyle = cn[i].getAttribute("color");
         //console.log(cn[i]);
         
         }
         }
         
         
         } else {
         this.ref.canvas.width = root.getAttribute("width");
         this.ref.canvas.height = root.getAttribute("height");
         }
         
         }
         }
         
         });
         render.Start();*/
    }
   
    AddSlide(s) {
        if (s === null || s instanceof PointPoint_Slide) {
            this.slides.push(s);
        }
    }
    EndOfSlides() {
        return  this.slidesindex >= this.slides.length;
    }
    IsNull() {
        return  this.slides[this.slidesindex] === null;
    }
    NextItem() {
        if (this.slidesindex >= 0) {
            var sd = this.slides[this.slidesindex].GetSlideData();
            this.domlist.style.width = sd.style.width;
            this.domlist.style.height = sd.style.height;
            var cn = Array.from(sd.childNodes);
            if (this.slidesitemindex < cn.length) {
                this.domlist.appendChild(cn[ this.slidesitemindex].cloneNode(true));
                this.slidesitemindex++;
                return true;
            }
        }
        return false;
    }
    NextSlide() {
        this.domlist.innerHTML = "";
        if (this.slidesindex < this.slides.length - 1) {
            this.slidesindex++;
            this.slidesitemindex = 0;
            return true;
        }
        return false;
    }
    ReplaceSlideAt(index, slide) {
        if (slide === null || slide instanceof PointPoint_Slide) {
            this.slides[index] = slide;
        }
    }
    SetSlide(index) {
        if (index < this.slides.length - 1) {
            this.domlist.innerHTML = "";
            this.slidesindex = index;
            this.slidesitemindex =0;
        }

    }
}







class PointPoint_LeftToRight_Animation extends PointPoint_Animation {
    GetName() {
        return "LeftToRight";
    }
    Render() {

    }
}


