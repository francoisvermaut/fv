<?php
header('Content-type: text/plain');
include_once("GeneticAlgorithm.php");
include_once("GeneticAlgorithmFloat.php");

/*$CROSSOVER_RATE = 0.7;
$MUTATION_RATE = 0.001;
$POP_SIZE = 100; //must be an even number
$CHROMO_LENGTH = 300;
$GENE_LENGTH = 4;
$MAX_ALLOWABLE_GENERATIONS = 400;*/

//$ga = new GeneticAlgorithm(100, 300, 4, 400, 0.7, 0.001);
//$ga->findSolution(42);

$ga = new GeneticAlgorithmFloat(30, 27, 400, 0.7, 0.1, 0.3);
// 30 chromosome, 27 genes = the 27 weights of the neural network, 400 max generations
print_r($ga);
$ga->epoch();
print_r($ga);
