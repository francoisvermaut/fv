<?php

/*$xpm = array(
"8 8 7 1",
" 	c #000000",
".	c #FF0000",
"+	c #FFFFFF",
"@	c #A05000",
"#	c #FFB57F",
"$	c #FFFF00",
"%	c #00AEFF",
"   ...+ ",
"   .....",
"  @#@ # ",
"  @##@@#",
"   @### ",
" ..$%%$ ",
"+ %%%%%#",
"  @   @ "
);*/


function getImageStringFromFile($xpmfile) {
    $file = fopen($xpmfile,"r");
    $content = fread($file, filesize($xpmfile));
    // remove all " and CR
    $content = str_replace(array("\"","\n", "\r"), "", $content);
    // find position of { and of } and substr between these 2
    $start = strpos($content, "{")+1; // to remove the {
    $stop = strpos($content, "}");
    $imgdescr = substr($content, $start, $stop - $start);
    // explode
    $xpm = explode(",", $imgdescr);
    
    //print_r($xpm);
    
    return getImageString($xpm);
}

function getImageString($xpm) {
    // Get values
    $values = explode(" ", $xpm[0]);
    $maxx = $values[0];
    $maxy = $values[1];
    $maxcolors = $values[2];
    $charpercolor = $values[3];

    // Get all colors
    $colors = array();
    for ($c = 1; $c<=$maxcolors ; $c++) {
        $key = substr($xpm[$c], 0, $charpercolor);
        $diesepos = strpos($xpm[$c], "#", $charpercolor);
        $color = substr($xpm[$c], $diesepos, 7);
        $colors[$key] = $color;
    }

    //print_r($colors);
    
    // Get all pixels and translate color, line per line
    $imagestring = "";
    for ($y = 1 + $maxcolors; $y<=$maxy + $maxcolors; $y++) {
        for ($x = 0; $x<$maxx; $x++) {
            $ch = substr($xpm[$y],$x, $charpercolor);
            $imagestring .= $colors[$ch];
            if ($x!=$maxx-1 || $y!=$maxy + $maxcolors) $imagestring .= ",";
        }
    }

    return $imagestring;
}
