<!DOCTYPE html>
<!--
Created using JS Bin
http://jsbin.com

Copyright (c) 2015 by Jthe4th (http://jsbin.com/hozide/54/edit)

Released under the MIT license: http://jsbin.mit-license.org
-->
<meta name="robots" content="noindex">
<html>
    <head>
        <meta name="description" content="Wind farm - v2">
        <meta charset="utf-8">
        <title>Wind farm - v2</title>


        <style id="jsbin-css">
            canvas {

                display:block;
                border:1px solid black;
                width: 90%;
                margin: 0 auto;

            }

            form div {

                margin:10px;
                padding:10px;

            }

            output { 

                border: 1px solid black;
                padding:1px 8px;

            }
        </style>
    </head>
    <body>

        <canvas id=myCanvas width=600 height=600>No canvas support...</canvas>

        <form id=settings>

            <div>
                <label for=windSpeed>Wind speed: &nbsp;</label>
                0 <input id=windSpeed type=range min="-0.2" max="0.2" step="0.0025" value="0.01">1
                <output id="windSpeedOutput"></output>
            </div>

            <div>
                <label for=bladeText>Text on blade: &nbsp;</label>
                <input id=bladeText type=text value="beGreen" maxlen=8>
            </div>


        </form>



        <script id="jsbin-javascript">
//localStorage.clear();   //just for debug 

            var canvas, ctx, grassImg, bladeSpeed, speedIncr, speed, bladeImg, bladeText;

            window.onload = function () {

                canvas = document.querySelector('#myCanvas');
                ctx = canvas.getContext('2d');
                bladeSpeed = 0.1;

                //Check if windSpeed is in localStorage, if not, set it based on range input slider
                if (localStorage.windSpeed !== undefined) {

                    speedIncr = localStorage.windSpeed; //Set speed

                    console.log("windSpeed in localStorage = " + localStorage.windSpeed);

                    document.getElementById('windSpeedOutput').value = localStorage.windSpeed; //set <output> to show windSpeed

                    console.log("windSpeed in localStorage = " + localStorage.windSpeed); //Log to console for debug purposes

                    document.getElementById('windSpeed').value = localStorage.windSpeed; //set the range <input> to the value from localStorage

                } else {

                    speedIncr = 0.014; //Set a default starting speed if not in localStorage

                }


                //Look for bladeText in localStorage
                if (localStorage.bladeText !== undefined) {

                    bladeText = localStorage.bladeText;

                    document.getElementById('bladeText').value = localStorage.bladeText; //set the text <input> for bladeText to the value from localStorage

                }


                //Load and draw the grass image
                grassImg = new Image();
                grassImg.onload = function () {
                    ctx.drawImage(grassImg, 0, 255);
                }
                grassImg.src = "http://feezer-iv-general-storage.s3.amazonaws.com/HTML5.1x/grass.jpg";


                bladeImg = new Image();
                bladeImg.onload = function () {
                    ctx.drawImage(bladeImg, -100, -100);
                }
                bladeImg.src = "http://feezer-iv-general-storage.s3.amazonaws.com/HTML5.1x/wind-turbine-blade-smaller.png";


                requestAnimationFrame(animate);
            }



            function animate() {

                //Save the context state
                ctx.save();

                //clear canvas
                ctx.clearRect(0, 0, canvas.width, canvas.height);

                //Draw the grass
                ctx.drawImage(grassImg, 0, 255);

                //Draw the sky gradient
                var skyGradient = ctx.createLinearGradient(0, 0, 0, 255);
                skyGradient.addColorStop(0.7, "lightblue");
                skyGradient.addColorStop(1, "lightyellow");

                ctx.fillStyle = skyGradient; //Uncomment for sunset

                ctx.fillRect(0, 0, 600, 255);


                //Clouds
                drawCloud(-60, 30, 1);
                drawCloud(130, 10, 1);
                drawCloud(300, 60, 0.8);
                drawCloud(450, 20, 0.9);
                drawCloud(380, 100, 0.9);
                drawCloud(80, 100, 0.9);

                //Back row
                drawThreeBladeTower(90, 215, 0.2, bladeSpeed * 0.3);
                drawThreeBladeTower(210, 215, 0.2, bladeSpeed * 0.39);
                drawThreeBladeTower(350, 215, 0.2, -bladeSpeed * 0.3);
                drawThreeBladeTower(500, 215, 0.2, bladeSpeed * 0.35);

                //Middle row
                drawThreeBladeTower(150, 180, 0.4, -bladeSpeed * 0.75);
                drawThreeBladeTower(290, 180, 0.4, bladeSpeed * 0.3);
                drawThreeBladeTower(425, 180, 0.4, bladeSpeed * 0.4);

                //Front row
                drawThreeBladeTower(80, 230, 1, bladeSpeed * 0.65);
                drawThreeBladeTower(310, 230, 1, bladeSpeed * 0.37);
                drawThreeBladeTower(520, 230, 1, bladeSpeed * 0.49);


                // **** Set speed ****

                speedIncr = parseFloat(document.getElementById('windSpeed').value);

                bladeSpeed = bladeSpeed + speedIncr;


                //Animate
                requestAnimationFrame(animate);

                ctx.restore(); //Restore the original state of the context
            }



            //drawTower() is no longer in use. It would create the old double-blade version
            function drawTower(xpos, ypos, scale, deg) {

                ctx.save();

                ctx.translate(xpos, ypos);
                ctx.scale(scale, scale);

                //Tower
                ctx.beginPath();
                ctx.strokeStyle = 'lightgray';
                ctx.fillStyle = 'darkgray';
                ctx.moveTo(-10, 250);
                ctx.lineTo(-4, 0);
                ctx.lineTo(6, 0);
                ctx.lineTo(20, 250);
                ctx.closePath();

                //Generator box
                ctx.fillRect(-12, -12, 25, 25);

                ctx.stroke();
                ctx.fill();

                ctx.restore();

                //Draw and spin the blade
                ctx.save();

                ctx.translate(xpos, ypos);
                ctx.scale(scale, scale);
                ctx.rotate(Math.PI * deg);  //Make the blade spin!

                ctx.fillStyle = "white";
                ctx.strokeStyle = "#ccc";
                ctx.fillRect(-125, -6, 250, 12);
                ctx.strokeRect(-125, -6, 250, 12);

                //Blade tips
                ctx.fillStyle = "gray";
                ctx.fillRect(-125, -6, 4, 12);
                ctx.fillRect(125, -6, 4, 12);

                //Axis/nose
                ctx.fillStyle = "black";
                ctx.strokeStyle = "black";
                ctx.beginPath();
                ctx.arc(0, 0, 5, Math.PI * 0, Math.PI * 2);
                ctx.fill();

                //Text on blade
                ctx.fillStyle = "green";
                ctx.fillText("beGreen", 80, 3, 250);

                ctx.restore();

            }


            // New function for building a more modern-looking windmill
            function drawThreeBladeTower(xpos, ypos, scale, deg) {

                ctx.save();

                ctx.translate(xpos, ypos);
                ctx.scale(scale, scale);

                //Tower
                ctx.beginPath();
                ctx.strokeStyle = 'lightgray';

                //A gradient should give the tower a more cylindrical look
                var towerColor = ctx.createLinearGradient(0, 0, 40, 0);

                towerColor.addColorStop(0, "gray");
                towerColor.addColorStop(0.5, "darkgray");
                towerColor.addColorStop(1, "gray");

                ctx.fillStyle = towerColor;

                ctx.moveTo(-10, 250);
                ctx.lineTo(-4, 0);
                ctx.lineTo(6, 0);
                ctx.lineTo(20, 250);
                ctx.closePath();

                //Generator box
                ctx.fillRect(-12, -12, 25, 25);
                ctx.stroke();
                ctx.fill();

                ctx.restore();


                //Draw and spin the blade
                ctx.save();

                ctx.translate(xpos, ypos);
                ctx.scale(scale, scale);
                ctx.rotate(Math.PI * deg);  //Make the blade spin!


                //Blade image & text

                ctx.drawImage(bladeImg, -147, -173);

                //Axis/nose
                var noseColor = ctx.createRadialGradient(0, 0, 19, 0, 0, 1);
                noseColor.addColorStop(0, "darkgreen");
                noseColor.addColorStop(0.5, "green");
                noseColor.addColorStop(1, "darkgreen");

                ctx.fillStyle = noseColor;
                ctx.beginPath();
                ctx.arc(0, 0, 12, Math.PI * 0, Math.PI * 2);
                ctx.fill();


                //Text on blade

                ctx.save();
                ctx.rotate(Math.PI * 0.170);
                ctx.fillStyle = "green";
                ctx.fillText(bladeText, 30, 3);
                ctx.restore();


                ctx.restore();

            }




            function drawCloud(xpos, ypos, scale) {

                //In progress...

                ctx.save();

                ctx.translate(xpos, ypos);
                ctx.scale(scale + 0.95, scale);

                ctx.fillStyle = "#efefef";

                ctx.beginPath();
                ctx.arc(50, 50, 20, Math.PI * 0, Math.PI * 2);
                ctx.fill();

                ctx.beginPath();
                ctx.arc(30, 50, 10, Math.PI * 0, Math.PI * 2);
                ctx.fill();

                ctx.beginPath();
                ctx.arc(70, 50, 20, Math.PI * 0, Math.PI * 2);
                ctx.fill();

                ctx.beginPath();
                ctx.arc(90, 50, 10, Math.PI * 0, Math.PI * 2);
                ctx.fill();

                ctx.restore();
            }





//Event listeners

            document.getElementById('windSpeed').addEventListener('input', function (evt) {

                //This event listener just updates the <output> to show thew current speed

                var outputTextbox = document.getElementById('windSpeedOutput');

                var slider = document.getElementById('windSpeed').value;

                outputTextbox.value = slider;

                //Save current speed to localStorage
                localStorage.windSpeed = slider;

            });



            







        </script>
    </body>
</html>