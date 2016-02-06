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
        <title>Clothing Remote Control</title>
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
        });
        </script>
    </head>
    <body>
        <h1>Clothing Remote Control</h1>
        <form id="picsForm" action="send.php" method="GET" accept-charset="UTF-8">
            <table border='0'>
                <tr>
                    <td><input type="radio" name="inputtype" value="1" checked="checked"></td>
                    <td>Marquee</td>
                </tr>
                <tr>
                    <td><input type="radio" name="inputtype" value="2"></td>
                    <td>Rainbow</td>
                </tr>
                <tr>
                    <td><input type="radio" name="inputtype" value="3"></td>
                    <td>Hilfiger Logo</td>
                </tr>
                <tr>
                    <td><input type="radio" name="inputtype" value="4"></td>
                    <td>Letter</td>
                    <td><input type="text" name="letter" id="letter"/></td>
                </tr>
            </table>
            <br/>
            <button type="submit" name="submit" value="submit-value">GO</button>        
        </form>
        <p><?php echo $feedbackmessage;?></p>
    </body>
</html>
