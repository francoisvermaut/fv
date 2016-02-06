<?php

$feedbackmessage = "";
if (isset($_GET['feedbackmessage']) && $_GET['feedbackmessage']!="") {
    $feedbackmessage = $_GET['feedbackmessage'];
} else {
    if (isset($_POST['feedbackmessage']) && $_POST['feedbackmessage']!="") {
        $feedbackmessage = $_POST['feedbackmessage'];
    }
}
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Clothing LED Matrix</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width">
        <script type="text/javascript" src="../js/jquery-1.10.2.min.js"></script>
        <link rel="stylesheet" href="../css/cupertino/jquery-ui-1.10.4.custom.min.css">
        <script src="../js/jquery-ui-1.10.4.custom.min.js"></script>
        <style>
            body {
                    margin:10px;
                    padding:0;
                    border:0;
                    width:100%;
                    background:#fff;
                    min-width:320px;
                    /*font-size: 90%;*/
                    font-family: Helvetica, Arial, sans-serif;
            } 
            .error {
                color: #FF0000;
                font-weight: bold;
            }
            .valid {
                color: #00AA00;
                font-weight: bold;
            }
            table.fv {
                border: 1px solid black;
                /*width: 100%;*/
            }
            th.fv {
                background-color: #A7C942;
                color: white;
            }
            tr.fv {
                background-color: #EAF2D3;
            }
            tr.fv:nth-child(odd) {
                background-color: #ffffff;
            }
            td.fv {
                padding-left: 10px;
                padding-right: 10px;
            }
            #progressbar {
                width: 30%;
            }
            #progressbar .ui-progressbar-value {
                background-color: #ccc;
              }
            .progress-label {
                font-weight: bold;
                text-shadow: 1px 1px 0 #fff;
              }
        </style>
        <script>
        $(document).ready(function() {
            var form = $('#picsForm');
            var drawing = new Array();
            for (i=0;i<25;i++) {
                drawing[i] = '#000000';
            }
            var checkmark = document.getElementById("checkmark");
            checkmark.style.visibility="hidden";
            var chosencolor = '#FFFFFF';
            var colorpicker = document.getElementById("colorpicker");
            var cpctx = colorpicker.getContext("2d");
            var cpLeft = colorpicker.offsetLeft;
            var cpTop = colorpicker.offsetTop;
            var colors = [];
            colorpicker.addEventListener('click', function(event) {
                var x = event.pageX - cpLeft,
                    y = event.pageY - cpTop;

                colors.forEach(function(element) {
                    if (y > element.top && y < element.top + element.height && x > element.left && x < element.left + element.width) {
                        var indx = Math.floor(x / 40);
                        var indy = Math.floor(y / 40);
                        //alert('clicked a color : '+indx+' '+indy+' '+element.colour);
                        chosencolor = element.colour;
                        //cpctx.drawImage(checkmark,indx*40+10,indy*40+10);
                    }
                });
            }, false);
            
            var c11 = document.getElementById("mainCanvas");
            var context = c11.getContext("2d");
            var elemLeft = c11.offsetLeft;
            var elemTop = c11.offsetTop;
            var elements = [];
            c11.addEventListener('click', function(event) {
                var x = event.pageX - elemLeft,
                    y = event.pageY - elemTop;

                elements.forEach(function(element) {
                    if (y > element.top && y < element.top + element.height && x > element.left && x < element.left + element.width) {
                        var indx = Math.floor(x / 40);
                        var indy = Math.floor(y / 40);
                        //alert('clicked an element : '+indx+' '+indy);
                        context.fillStyle = chosencolor;
                        context.fillRect(indx*40, indy*40, 40, 40);
                        rendergrid();
                        drawing[indy*5+indx] = chosencolor;
                    }
                });
            }, false);
            
            // Add element.
            for (i=0;i<5;i++) {
                for (j=0;j<5;j++) {
                    elements.push({
                        colour: '#000000',
                        width: 40,
                        height: 40,
                        top: i*40,
                        left: j*40
                    });
                }
            }

            // Render elements.
            elements.forEach(function(element) {
                context.fillStyle = element.colour;
                context.fillRect(element.left, element.top, element.width, element.height);
            });
            
            // Add colors. Add some gamma
            // 
            colors.push({
                colour: '#FFFFFF',
                width: 40,
                height: 40,
                top: 0,
                left: 0
            });
            colors.push({
                colour: '#000000',
                width: 40,
                height: 40,
                top: 40,
                left: 0
            });
            colors.push({
                colour: '#FF0000',
                width: 40,
                height: 40,
                top: 80,
                left: 0
            });
            colors.push({
                colour: '#00FF00',
                width: 40,
                height: 40,
                top: 0,
                left: 40
            });
            colors.push({
                colour: '#0000FF',
                width: 40,
                height: 40,
                top: 40,
                left: 40
            });
            colors.push({
                colour: '#FFFF00',
                width: 40,
                height: 40,
                top: 80,
                left: 40
            });
            colors.push({
                colour: '#FF00FF',
                width: 40,
                height: 40,
                top: 0,
                left: 80
            });
            colors.push({
                colour: '#00FFFF',
                width: 40,
                height: 40,
                top: 40,
                left: 80
            });
            colors.push({
                colour: '#FF8000', /* 25 if gamma */
                width: 40,
                height: 40,
                top: 80,
                left: 80
            });

            // Render colors.
            colors.forEach(function(color) {
                cpctx.fillStyle = color.colour;
                cpctx.fillRect(color.left, color.top, color.width, color.height);
                if (color.colour=='#FFFFFF') {
                    cpctx.strokeStyle = '#000000';
                    cpctx.strokeRect(color.left, color.top, color.width, color.height);
                }
            });

            // Render grid
            function rendergrid() {
                for (i = 40; i < 200; i+=40) { 
                    context.moveTo(i,0);
                    context.lineTo(i,200);
                    context.stroke();
                    context.moveTo(0,i);
                    context.lineTo(200,i);
                    context.stroke();
                }
            }
            
            rendergrid();
            
            form.submit(function(event) {
                var dinput = $('#drawinginput');
                var txt = '';
                for (i=0;i<25;i++) {
                    txt+=drawing[i];
                    if (i<24) txt+=',';
                }
                dinput.val(txt);
            });
        });
        </script>
    </head>
    <body>
        <h1>Clothing LED Matrix</h1>
        <img id="checkmark" src="check.png"/>
        <form id="picsForm" action="sendMatrix.php" method="POST" accept-charset="UTF-8">
            <!--div id='d1' style="position:absolute; top:100px; left:10px; z-index:1"-->
                <canvas id="mainCanvas" width="200" height="200" style="border:1px solid #000000;"></canvas>
            <!--/div-->
            <!--div id='d2' style="position:absolute; top:100px; left:250px; z-index:1"-->
                <canvas id="colorpicker" width="200" height="200" style=""></canvas>
            <!--/div-->
            <!--div id='d3' style="position:absolute; top:350px; left:10px; z-index:1"-->
                <input type="hidden" name="drawinginput" id="drawinginput"/>
                <button type="submit" name="submit" value="submit-value">GO</button>
            <!--/div-->
        </form>
        <p><?php echo $feedbackmessage;?></p>
    </body>
</html>
