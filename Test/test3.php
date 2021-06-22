 
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Test</title>

    </head>
    <body>
        <canvas id="drawing" width="300" height="300" style="border-style: solid;" ></canvas>
        <script>
            function draw() {
                var drawing = document.getElementById("drawing"); //Get canvas element

                if (drawing.getContext) {  //Get drawing context
                    var content = drawing.getContext("2d"),
                            radian = 0, //Set the initial radian
                            radian_add = Math.PI / 180;  //Set the radian increment
                    content.beginPath();  //Start drawing



                    content.translate(drawing.width / 2, drawing.height / 2);   
                    

                    content.moveTo(getX(radian), getY(radian));  
                    while (radian <= (Math.PI * 2)) {  
                        radian += radian_add;

                        content.lineTo(getX(radian), getY(radian));
                    }
                    content.strokeStyle = "red";  //Set the stroke style
                    content.stroke();  //Stroke the path

                }
            }


            /*    function getX(t) {   //Get the X coordinate of the rose line
             return 100 * Math.sin(3 * t) * Math.cos(t);
             }
             
             function getY(t) {  //Get the Y coordinate of the rose line
             return 100 * Math.sin(3 * t) * Math.sin(t);
             }*/

            /*  function getX(t) {   //Get the X coordinate of the rose line
             return 100 * Math.sin(4 * t) * Math.cos(t);
             }
             
             function getY(t) {  //Get the Y coordinate of the rose line
             return 100 * Math.sin(4 * t) * Math.sin(t);
             }
             */
            function getX(t) {  //Get the X coordinate of the cardioid line
                return (1)* (16 * Math.pow(Math.sin(t), 3))
            }

            function getY(t) {  //Get the Y coordinate of the cardioid line
                return  (-1)* (13 * Math.cos(t) - 5 * Math.cos(2 * t) - 2 * Math.cos(3 * t) - Math.cos(4 * t))
            }
            draw();
        </script>
    </body>

</html>