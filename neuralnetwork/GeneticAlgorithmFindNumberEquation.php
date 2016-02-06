<?php
header('Content-type: text/plain');
global $CROSSOVER_RATE;
global $MUTATION_RATE;
global $POP_SIZE;
global $CHROMO_LENGTH;
global $GENE_LENGTH;
global $MAX_ALLOWABLE_GENERATIONS;

$CROSSOVER_RATE = 0.7;
$MUTATION_RATE = 0.001;
$POP_SIZE = 100; //must be an even number
$CHROMO_LENGTH = 300;
$GENE_LENGTH = 4;
$MAX_ALLOWABLE_GENERATIONS = 400;


$target = 95;

$population = array();
$fitness = array();
// Generate a random population
for ($i=0; $i<$POP_SIZE;$i++) {
    $population[$i] = getRandomChromo($CHROMO_LENGTH);
    $fitness[$i] = 0;
}

//print_r($population);
printPopulation($population);

/*echo "Chromosome\n";
echo $population[0];
echo "\n";
echo $population[1];
echo "\n";*/
/*echo "\nParsing\n";
$res = parseChromo($population[0]);
print_r($res);
echo "\nPrint\n";
printChromo($population[0]);
echo "\nCalculation\n";
echo calculateChromo($population[0]);
echo "\nFitness\n";
echo calculateFitness($population[0], $target);*/
/*$crossresult = crossover($population[0], $population[1]);
print_r($crossresult);*/
/*$new = mutate($population[0]);
echo $new;*/


// Try to find the solution
$found = false;
$iterations = 0;
while (!$found) {
    $totalfitness = 0;
    // test and update the fitness of every chromosome in the population
    for ($i=0; $i<$POP_SIZE;$i++) {
        $fitness[$i] = calculateFitness($population[$i], $target);
        $totalfitness += $fitness[$i];
    }
    // check to see if we have found any solutions (fitness will be 999)
    for ($i=0; $i<$POP_SIZE;$i++) {
        if ($fitness[$i] == 999) {
            echo "Found a solution after ".$iterations." iterations !!\n";
            printChromo($population[$i]);
            $found = true;
            break;
        }
    }

    // create a new population by selecting two parents at a time and creating offspring
    // by applying crossover and mutation. Do this until the desired number of offspring
    // have been created. 
    $temppop = array();
    $cpop = 0;
    while ($cpop < $POP_SIZE) {
        // we are going to create the new population by grabbing members of the old population
	// two at a time via roulette wheel selection.
        $offspring1 = roulette($totalfitness, $population, $fitness);
        $offspring2 = roulette($totalfitness, $population, $fitness);
        
        // and crossover dependent on the crossover rate
        $crossresult = crossover($offspring1, $offspring2);
        if (isset($crossresult[0])) $offspring1 = $crossresult[0];
        if (isset($crossresult[1])) $offspring2 = $crossresult[1];
        
        // now mutate dependen on the mutation rate
        $offspring1 = mutate($offspring1);
        $offspring2 = mutate($offspring2);
        
        // add the offsprings in the population with fitness at 0
        $temppop[$cpop++] = $offspring1;
        $temppop[$cpop++] = $offspring2;
    }
    // Reset fitness
    $fitness = array();
    for ($i=0; $i<$POP_SIZE;$i++) {
        $fitness[$i] = 0;
    }
    // Copy temp population to population
    $population = $temppop;
    
    if (!$found) printPopulation($population);
    
    $iterations++;
    
    if ($iterations==$MAX_ALLOWABLE_GENERATIONS) {
        echo "No solution found\n";
        $found = true;
    }
    
}



function roulette($totalfitness, $population, $fitness) {
    global $CROSSOVER_RATE;
    global $MUTATION_RATE;
    global $POP_SIZE;
    global $CHROMO_LENGTH;
    global $GENE_LENGTH;
    global $MAX_ALLOWABLE_GENERATIONS;
    
    $slice = lcg_value() * $totalfitness;
    $fitnesssofar = 0;
    
    for ($i=0;$i<$POP_SIZE;$i++) {
        $fitnesssofar += $fitness[$i];
        if ($fitnesssofar >= $slice) {
            return $population[$i];
        }
    }
    
    return "";
}

function crossover($offspring1, $offspring2) {
    global $CROSSOVER_RATE;
    global $MUTATION_RATE;
    global $POP_SIZE;
    global $CHROMO_LENGTH;
    global $GENE_LENGTH;
    global $MAX_ALLOWABLE_GENERATIONS;

    $result = array();
    if (lcg_value() < $CROSSOVER_RATE) {
        $cross = round(lcg_value() * $CHROMO_LENGTH);
        $t1 = substr($offspring1, 0, $cross) . substr($offspring2, $cross, $CHROMO_LENGTH - $cross +1);
        $t2 = substr($offspring2, 0, $cross) . substr($offspring1, $cross, $CHROMO_LENGTH - $cross +1);
        $result[] = $t1;
        $result[] = $t2;
    }
    return $result;
}

function mutate($offspring) {
    global $CROSSOVER_RATE;
    global $MUTATION_RATE;
    global $POP_SIZE;
    global $CHROMO_LENGTH;
    global $GENE_LENGTH;
    global $MAX_ALLOWABLE_GENERATIONS;
    
    for ($i=0;$i<strlen($offspring);$i++) {
        if (lcg_value() < $MUTATION_RATE) {
            if ($offspring[$i] == '1') {
                $offspring[$i] = '0';
            } else {
                $offspring[$i] = '1';
            }
        }
    }
    return $offspring;
}



function getRandomChromo($length) {
    $bits = "";
    for ($i=0;$i<$length;$i++) {
        if (lcg_value()>0.5) {
            $bits.="1";
        } else {
            $bits.="0";
        }
    }
    return $bits;
}

function calculateFitness($bits, $target) {
    global $CROSSOVER_RATE;
    global $MUTATION_RATE;
    global $POP_SIZE;
    global $CHROMO_LENGTH;
    global $GENE_LENGTH;
    global $MAX_ALLOWABLE_GENERATIONS;
    
    $score = calculateChromo($bits);
    if ($score == $target) {
        return 999;
    } else {
        return 1/abs($target - $score);
    }
}




function calculateChromo($bits) {
    global $CROSSOVER_RATE;
    global $MUTATION_RATE;
    global $POP_SIZE;
    global $CHROMO_LENGTH;
    global $GENE_LENGTH;
    global $MAX_ALLOWABLE_GENERATIONS;

    $chromo = parseChromo($bits);
    $result = $chromo[0];
    for ($i=1; $i<count($chromo);$i++) {
        if (isset($chromo[$i+1])) {
            switch ($chromo[$i]) {
                case 10:
                    $result += $chromo[$i+1];
                    break;
                case 11:
                    $result -= $chromo[$i+1];
                    break;
                case 12:
                    $result *= $chromo[$i+1];
                    break;
                case 13:
                    // Force to int
                    $result = round($result / $chromo[$i+1]);
                    break;
            }
        }
    }
    return $result;
}

function parseChromo($bits) {
    global $CROSSOVER_RATE;
    global $MUTATION_RATE;
    global $POP_SIZE;
    global $CHROMO_LENGTH;
    global $GENE_LENGTH;
    global $MAX_ALLOWABLE_GENERATIONS;
    // step through bits a gene at a time until end and store decimal values
    // of valid operators and numbers. Don't forget we are looking for operator - 
    // number - operator - number and so on... We ignore the unused genes 1111
    // and 1110
    
    $result = array();
    $operator = false;
    
    for ($i=0; $i<$CHROMO_LENGTH; $i+=$GENE_LENGTH) {
        //convert the current gene to decimal
        $this_gene = bindec(substr($bits, $i, $GENE_LENGTH));
        if ($operator) { // Find operator
            if ($this_gene<10 || $this_gene > 13) {
                
            } else {
                $operator = false;
                $result[] = $this_gene;
            }
        } else { // Find number
            if ($this_gene > 9) {
                
            } else {
                $operator = true;
                $result[] = $this_gene;
            }
        }
    }
    
    // Find and replace possible division by 0
    for ($j=0; $j<count($result);$j++) {
        if (isset($result[$j+1])) {
            if ($result[$j]==13 && $result[$j+1]==0) {
                $result[$j] = 10;  // Replace with a +
            }
        }
    }
    
    return $result;
}


function printPopulation($population) {
    foreach ($population as $key => $chromo) {
        printChromo($chromo);
        echo "\n";
    }
}

function printChromo($bits) {
    global $CROSSOVER_RATE;
    global $MUTATION_RATE;
    global $POP_SIZE;
    global $CHROMO_LENGTH;
    global $GENE_LENGTH;
    global $MAX_ALLOWABLE_GENERATIONS;

    $chromo = parseChromo($bits);
    foreach ($chromo as $key => $gene) {
        printGeneSymbol($gene);
    }
}

function printGeneSymbol($gene) {
    if ($gene<10) {
        echo $gene." ";
    } else {
        switch ($gene) {
            case 10:
                echo "+";
                break;
            case 11:
                echo "-";
                break;
            case 12:
                echo "*";
                break;
            case 13:
                echo "/";
                break;
        }
        echo " ";
    }
}

?>