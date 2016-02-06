<?php
$portName = 'com3:';
$baudRate = 115200;
$bits = 8;
$spotBit = 1;

$feedbackmessage = "";

$inputtype = "";
if (isset($_GET['inputtype']) && $_GET['inputtype']!="") {
    $inputtype = $_GET['inputtype'];
} else {
    if (isset($_POST['inputtype']) && $_POST['inputtype']!="") {
        $inputtype = $_POST['inputtype'];
    }
}
$letter = "";
if (isset($_GET['letter']) && $_GET['letter']!="") {
    $letter = $_GET['letter'];
} else {
    if (isset($_POST['letter']) && $_POST['letter']!="") {
        $letter = $_POST['letter'];
    }
}

function echoFlush($string) {
    echo $string . "\n";
    flush();
    ob_flush();
}

if(!extension_loaded('dio')) {
    //echoFlush( "PHP Direct IO does not appear to be installed.");
    $feedbackmessage = "PHP Direct IO does not appear to be installed.";
    header("Location: index.php?feedbackmessage=".$feedbackmessage);
    exit;
}

try 
{
    //the serial port resource
    $bbSerialPort;
    //echoFlush(  "Connecting to serial port: {$portName}" );
    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') { 
        $bbSerialPort = dio_open($portName, O_RDWR );
        exec("mode {$portName} baud={$baudRate} data={$bits} stop={$spotBit} parity=n xon=on");
    } else {
        $bbSerialPort = dio_open($portName, O_RDWR | O_NOCTTY | O_NONBLOCK );
        dio_fcntl($bbSerialPort, F_SETFL, O_SYNC);
        dio_tcsetattr($bbSerialPort, array(
                'baud' => $baudRate,
                'bits' => $bits,
                'stop'  => $spotBit,
                'parity' => 0
        ));
    }

    if(!$bbSerialPort) {
        //echoFlush( "Could not open Serial port {$portName} ");
        $feedbackmessage = "Could not open Serial port";
        header("Location: index.php?feedbackmessage=".$feedbackmessage);
        exit;        
    }

    // send data
    $dataToSend = $inputtype;
    if ($inputtype==4) {
        $dataToSend = $inputtype.$letter;
    }
    $bytesSent = dio_write($bbSerialPort, $dataToSend );
        
    dio_close($bbSerialPort);

    $feedbackmessage = "OK";
} 
catch (Exception $e) {
    //echoFlush(  $e->getMessage() );
    $feedbackmessage = $e->getMessage();
    header("Location: index.php?feedbackmessage=".$feedbackmessage);
    exit;        
} 

header("Location: index.php?feedbackmessage=".$feedbackmessage);
exit;

?>
