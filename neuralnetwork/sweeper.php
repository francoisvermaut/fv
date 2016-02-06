<?php
header('Content-type: text/plain');
include_once("Neuron.php");
include_once("NeuronLayer.php");
include_once("NeuralNet.php");
include_once("GeneticAlgorithmFloat.php");
include_once("MineSweeper.php");

// Initialize mines
$numMines = 4; // 40
$fieldmaxx = 500;
$fieldmaxy = 500;
$mines = array();
for ($i=0;$i<$numMines;$i++) {
    $coord = array();
    $coord[] = round(lcg_value() * $fieldmaxx);
    $coord[] = round(lcg_value() * $fieldmaxy);
    $mines[] = $coord;
}

// Display mines
echo "Mines\n";
foreach ($mines as $key => $mine) {
    echo "X:".$mine[0]." Y:".$mine[1]."\n";
}

// Create n sweepers
$numSweepers = 1; //10
$sweepers = array();
for ($i=0; $i<$numSweepers; $i++) {
    $sweepers[] = new MineSweeper($fieldmaxx, $fieldmaxy);
}

// Display sweepers initial positions
echo "\nSweepers Initial Positions\n";
foreach ($sweepers as $key => $sweeper) {
    echo $key." X:".$sweeper->posx." Y:".$sweeper->posy."\n";
}

$numWeights = $sweepers[0]->brain->getNumberOfWeights();

// 1 chromosome per sweeper,  genes = the weights of the neural network, 400 max generations
$ga = new GeneticAlgorithmFloat($numSweepers, $numWeights, 400, 0.7, 0.1, 0.3);
for ($i=0;$i<$numSweepers;$i++) {
    $sweepers[$i]->brain->putWeights($ga->population[$i]);
}


for ($k=0;$k<40;$k++) {
    // Update
    for ($j=0;$j<2000;$j++) {
        for ($i=0; $i<$numSweepers;$i++) {
            $sweepers[$i]->update($mines);

            //echo " X:".$sweepers[$i]->posx." Y:".$sweepers[$i]->posy."\n";

            $minefound = $sweepers[$i]->checkForMine($mines, 2);
            if ($minefound>=0) {
                echo "Found a mine at $minefound : ".$mines[$minefound][0]."  ".$mines[$minefound][1]."\n";
            }
        }
    }

    $ga->epoch();
    for ($i=0;$i<$numSweepers;$i++) {
        $sweepers[$i]->brain->putWeights($ga->population[$i]);
    }
}

// Display sweepers new positions
//echo "\nSweepers New Positions\n";
//foreach ($sweepers as $key => $sweeper) {
//    echo $key." X:".$sweeper->posx." Y:".$sweeper->posy."\n";
//}



