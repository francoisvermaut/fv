<?php
header('Content-type: text/plain');
include_once("Neuron.php");
include_once("NeuronLayer.php");
include_once("NeuralNet.php");


/*$neu = new Neuron(5);
print_r($neu);
$in = array(1,1,1,1,1);
echo $neu->calculate($in);*/

/*$neul = new NeuronLayer(5, 3);
$in = array(0.5,0.5,0.5,0.5,0.5);
$out = $neul->process($in);
print_r($out);*/


/*$n = new NeuralNet(5, 1, 2, 3);
// 5 inputs, 1 output, 2 layers, 3 neurons per layer : 5 inputs -> 3 -> 3 -> 1 output
// there should be 27 weights
print_r($n);

$in = array(1,1,1,1,1);
$out = $n->update($in);
print_r($out);*/


$n = new NeuralNet(4, 2, 1, 6);
for ($i=-500;$i<500;$i++) {
    $inputs = array($i, $i, 0, 0);
    echo $inputs[0]."  -->  ";
    $outputs = $n->update($inputs);
    echo $outputs[0]."  ".$outputs[1];
    echo "\n";
}


?>
