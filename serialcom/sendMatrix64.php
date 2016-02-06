<?php
include_once("xpmtohex.php");
//header('Content-type: text');

$portName = 'com5:';
$baudRate = 115200;
$bits = 8;
$spotBit = 1;

$feedbackmessage = "";

$drawinginput = "";
if (isset($_GET['drawinginput']) && $_GET['drawinginput']!="") {
    $drawinginput = $_GET['drawinginput'];
} else {
    if (isset($_POST['drawinginput']) && $_POST['drawinginput']!="") {
        $drawinginput = $_POST['drawinginput'];
    }
}

$spritename = "";
if (isset($_GET['spritename']) && $_GET['spritename']!="") {
    $spritename = $_GET['spritename'];
} else {
    if (isset($_POST['spritename']) && $_POST['spritename']!="") {
        $spritename = $_POST['spritename'];
    }
}

function echoFlush($string) {
    echo $string . "\n";
    flush();
    ob_flush();
}


// What did we receive ?  Spritename has priority.
if ($spritename!="") {
    $drawinginput = getImageStringFromFile($spritename);
}

echo $drawinginput."\n";
// Translate all hex into dec
$finalstring = "";
$colors = explode(',', $drawinginput);
foreach ($colors as $key => $color) {
    $c = hexdec($color);
    $finalstring.=$c;
    $finalstring.=',';
}
$finalstring = substr($finalstring, 0, strlen($finalstring)-1);
//echo $finalstring;


if(!extension_loaded('dio')) {
    echoFlush( "PHP Direct IO does not appear to be installed.");
    //$feedbackmessage = "PHP Direct IO does not appear to be installed.";
    //header("Location: matrix64.php?feedbackmessage=".$feedbackmessage);
    exit;
}

try 
{
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
        echoFlush( "Could not open Serial port {$portName} ");
        $feedbackmessage = "Could not open Serial port";
        //header("Location: matrix64.php?feedbackmessage=".$feedbackmessage);
        exit;        
    }

    // send data
    $dataToSend = $finalstring;
    $bytesSent = dio_write($bbSerialPort, $dataToSend );
        
    dio_close($bbSerialPort);

    $feedbackmessage = "OK";
} 
catch (Exception $e) {
    echoFlush(  $e->getMessage() );
    $feedbackmessage = $e->getMessage();
    //header("Location: matrix64.php?feedbackmessage=".$feedbackmessage);
    exit;        
} 

//header("Location: matrix64.php?feedbackmessage=".$feedbackmessage);
exit;

?>
