<?php


?>
<!DOCTYPE html>
<html>
    <head>
        <title>LED Matrix</title>
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
            for (i=0;i<64;i++) {
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
                        drawing[indy*8+indx] = chosencolor;
                    }
                });
            }, false);
            
            // Add element.
            for (i=0;i<8;i++) {
                for (j=0;j<8;j++) {
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
                for (i = 40; i < 320; i+=40) { 
                    context.moveTo(i,0);
                    context.lineTo(i,320);
                    context.stroke();
                    context.moveTo(0,i);
                    context.lineTo(320,i);
                    context.stroke();
                }
            }
            
            rendergrid();
            
            form.submit(function(event) {
                var dinput = $('#drawinginput');
                var txt = '';
                for (i=0;i<64;i++) {
                    txt+=drawing[i];
                    if (i<63) txt+=',';
                }
                dinput.val(txt);
                $.get("/fv/serialcom/sendMatrix64.php", { drawinginput: dinput.val() }, function(data){
                    $('#feedbackmessage').html(data);
                });
                event.preventDefault();
            });
            
            var mario = $("#mario");
            mario.click(function() {
                $.get("/fv/serialcom/sendMatrix64.php", { spritename: "mario.xpm" }, function(data){
                    $('#feedbackmessage').html(data);
                });
            })
            var luigi = $("#luigi");
            luigi.click(function() {
                $.get("/fv/serialcom/sendMatrix64.php", { spritename: "luigi.xpm" }, function(data){
                    $('#feedbackmessage').html(data);
                });
            })
            var yoshi = $("#yoshi");
            yoshi.click(function() {
                $.get("/fv/serialcom/sendMatrix64.php", { spritename: "yoshi.xpm" }, function(data){
                    $('#feedbackmessage').html(data);
                });
            })
            var donald = $("#donald");
            donald.click(function() {
                $.get("/fv/serialcom/sendMatrix64.php", { spritename: "donald.xpm" }, function(data){
                    $('#feedbackmessage').html(data);
                });
            })
            var riri = $("#riri");
            riri.click(function() {
                $.get("/fv/serialcom/sendMatrix64.php", { spritename: "riri.xpm" }, function(data){
                    $('#feedbackmessage').html(data);
                });
            })
            var fifi = $("#fifi");
            fifi.click(function() {
                $.get("/fv/serialcom/sendMatrix64.php", { spritename: "fifi.xpm" }, function(data){
                    $('#feedbackmessage').html(data);
                });
            })
            var loulou = $("#loulou");
            loulou.click(function() {
                $.get("/fv/serialcom/sendMatrix64.php", { spritename: "loulou.xpm" }, function(data){
                    $('#feedbackmessage').html(data);
                });
            })
            var fantom1 = $("#fantom1");
            fantom1.click(function() {
                $.get("/fv/serialcom/sendMatrix64.php", { spritename: "fantom1.xpm" }, function(data){
                    $('#feedbackmessage').html(data);
                });
            })
            var pacman = $("#pacman");
            pacman.click(function() {
                $.get("/fv/serialcom/sendMatrix64.php", { spritename: "pacman.xpm" }, function(data){
                    $('#feedbackmessage').html(data);
                });
            })
        });
        </script>
    </head>
    <body>
        <h1>Draw your image</h1>
        <img id="checkmark" src="check.png"/>
        <form id="picsForm" action="" method="POST" accept-charset="UTF-8">
            <!--div id='d1' style="position:absolute; top:100px; left:10px; z-index:1"-->
                <canvas id="mainCanvas" width="320" height="320" style="border:1px solid #000000;"></canvas>
            <!--/div-->
            <!--div id='d2' style="position:absolute; top:100px; left:250px; z-index:1"-->
                <canvas id="colorpicker" width="200" height="200" style=""></canvas>
            <!--/div-->
            <!--div id='d3' style="position:absolute; top:350px; left:10px; z-index:1"-->
                <input type="hidden" name="drawinginput" id="drawinginput"/>
                <button type="submit" name="submit" value="submit-value">GO</button>
            <!--/div-->
        </form>
        <p id="feedbackmessage"></p>
        <form id="picsForm2" action="" method="POST" accept-charset="UTF-8">
            <h1>Or choose an image</h1>
            <table style='background: black;color:white;'>
                <tr>
                    <td style='padding-left: 10px;padding-right:10px;'><img id='mario' src="mario.jpg" width='75'/></td>
                    <td style='padding-left: 10px;padding-right:10px;'><img id='luigi' src="luigi.jpg" width='75'/></td>
                    <td style='padding-left: 10px;padding-right:10px;'><img id='yoshi' src="yoshi.jpg" width='75'/></td>
                    <td style='padding-left: 10px;padding-right:10px;'><img id='donald' src="donald.jpg" width='75'/></td>
                    <td style='padding-left: 10px;padding-right:10px;'><img id='riri' src="riri.jpg" width='75'/></td>
                    <td style='padding-left: 10px;padding-right:10px;'><img id='fifi' src="fifi.jpg" width='75'/></td>
                    <td style='padding-left: 10px;padding-right:10px;'><img id='loulou' src="loulou.jpg" width='75'/></td>
                    <td style='padding-left: 10px;padding-right:10px;'><img id='fantom1' src="fantom1.jpg" width='75'/></td>
                    <td style='padding-left: 10px;padding-right:10px;'><img id='pacman' src="pacman.jpg" width='75'/></td>
                </tr>
                <tr style='text-align:center;'>
                    <td>Mario</td>
                    <td>Luigi</td>
                    <td>Yoshi</td>
                    <td>Donald</td>
                    <td>Riri</td>
                    <td>Fifi</td>
                    <td>Loulou</td>
                    <td>Phantom 1</td>
                    <td>Pacman</td>
                </tr>
            </table>
        </form>
        <form id="picsForm3" action="sendText64.php" method="POST" accept-charset="UTF-8">
            <h1>Or enter some text</h1>
            <input type='text' id='sometext' name='sometext'/>
            <button type="submit" name="submit" value="submit-value">GO</button>
        </form>
    </body>
</html>
