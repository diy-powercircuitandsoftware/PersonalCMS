class SlideShow2D_Transition_PageTurn_TopDown extends SlideShow2D_Transition {
    
    Running(time) {
        if (time==0){
             this.P = (Math.sqrt(Math.pow(this.canvassize.width, 2) + Math.pow(this.canvassize.height, 2))) * 1.5;      
        }
        var stack = [];
        var x = (this.P * time) + 0.1;
        var y = (this.P * time) + 0.1;
 
        stack.push({
            "command": "save"
        }, {
            "command": "fillRect",
            "args":[0,0, this.canvassize.width,this.canvassize.height]
        },        
        {
            "command": "DrawCenter",
            "address": 2,         
            "extends": true
        }, {
            "command": "beginPath"
        }, {
            "command": "moveTo",
            "args":[  y * y / 2 / x + x / 2, 0]
        }, {
            "command": "lineTo",
            "args":[ this.canvassize.width * 2,0]
        }, {
            "command": "lineTo",
              "args":[ 0,this.canvassize.height * 2]
        }, {
            "command": "lineTo",
              "args":[0, x * x / 2 / y + y / 2]
        }, {
            "command": "closePath"
        }, {
            "command": "globalCompositeOperation",
            "value": "destination-out"
        }, {
            "command": "fill"
        }
        , {
            "command": "globalCompositeOperation",
            "value": "source-over"
        }, {
            "command": "clip"
        }, {
            "command": "DrawCenter",
            "address": 1,
            "extends": true
            
        }, {
            "command": "translate",
            "args":[x,y]
             
        }, {
            "command": "rotate",
            "args":[ Math.atan2(y, x) * 2]
        }, {
            "command": "scale",
             "args":[-1,1]            
        }, {
            "command": "DrawCenter",
            "address": 1,         
            "extends": true
        }, {
            "command": "translate",
             "args":[x,y]            
        }, {
            "command": "restore"
        });
        return stack;
    }
}
;

 class SlideShow2D_Transition_PageTurn_BottomToTop extends SlideShow2D_Transition {
    
    Running(time) {
        if (time==0){
             this.P = (Math.sqrt(Math.pow(this.canvassize.width, 2) + Math.pow(this.canvassize.height, 2))) * 1.5;      
        }
        var stack = [];
        var x = (this.P * (1-time)) - 0.1;
        var y = (this.P * (1-time)) - 0.1;
 
        stack.push({
            "command": "save"
        }, {
            "command": "fillRect",
            "args":[0,0, this.canvassize.width,this.canvassize.height]
        },        
        {
            "command": "DrawCenter",
            "address": 1,         
            "extends": true
        }, {
            "command": "beginPath"
        }, {
            "command": "moveTo",
            "args":[  y * y / 2 / x + x / 2, 0]
        }, {
            "command": "lineTo",
            "args":[ this.canvassize.width * 2,0]
        }, {
            "command": "lineTo",
              "args":[ 0,this.canvassize.height * 2]
        }, {
            "command": "lineTo",
              "args":[0, x * x / 2 / y + y / 2]
        }, {
            "command": "closePath"
        }, {
            "command": "globalCompositeOperation",
            "value": "destination-out"
        }, {
            "command": "fill"
        }
        , {
            "command": "globalCompositeOperation",
            "value": "source-over"
        }, {
            "command": "clip"
        }, {
            "command": "DrawCenter",
            "address": 2,
            "extends": true
            
        }, {
            "command": "translate",
            "args":[x,y]
             
        }, {
            "command": "rotate",
            "args":[ Math.atan2(y, x) * 2]
        }, {
            "command": "scale",
             "args":[-1,1]            
        }, {
            "command": "DrawCenter",
            "address": 1,         
            "extends": true
        }, {
            "command": "translate",
             "args":[x,y]            
        }, {
            "command": "restore"
        });
        return stack;
    }
}
;

 