<?php
Class GeneticAlgorithm {
    public $CROSSOVER_RATE;
    public $MUTATION_RATE;
    public $POP_SIZE;
    public $CHROMO_LENGTH;
    public $GENE_LENGTH;
    public $MAX_ALLOWABLE_GENERATIONS;
    public $population;
    public $fitness;
    
    public function GeneticAlgorithm($pop_size, $chromo_len, $gene_len, $max_generation, $crossoverrate, $mutationrate) {
        $this->CROSSOVER_RATE = $crossoverrate;
        $this->MUTATION_RATE = $mutationrate;
        $this->POP_SIZE = $pop_size;
        $this->CHROMO_LENGTH = $chromo_len;
        $this->GENE_LENGTH = $gene_len;
        $this->MAX_ALLOWABLE_GENERATIONS = $max_generation;
        
        $this->population = array();
        $this->fitness = array();
        // Generate a random population
        for ($i=0; $i<$this->POP_SIZE;$i++) {
            $this->population[$i] = $this->getRandomChromo($this->CHROMO_LENGTH);
            $this->fitness[$i] = 0;
        }
    }
    
    /* How to interpret the chromosome */
    /* Adapt to the new model */
    function parseChromo($bits) {
        // step through bits a gene at a time until end and store decimal values
        // of valid operators and numbers. Don't forget we are looking for operator - 
        // number - operator - number and so on... We ignore the unused genes 1111
        // and 1110

        $result = array();
        $operator = false;

        for ($i=0; $i<$this->CHROMO_LENGTH; $i+=$this->GENE_LENGTH) {
            //convert the current gene to decimal
            $this_gene = bindec(substr($bits, $i, $this->GENE_LENGTH));
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

    /* How to calculate the value of the chromosome */
    function calculateChromo($bits) {
        $chromo = $this->parseChromo($bits);
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

    /* Adapt this function to the new model */
    function calculateFitness($bits, $target) {
        $score = $this->calculateChromo($bits);
        if ($score == $target) {
            return 999;
        } else {
            return 1/abs($target - $score);
        }
    }

    /* Print functions */
    /* Adapt to the new model */
    function printPopulation($population) {
        foreach ($population as $key => $chromo) {
            $this->printChromo($chromo);
            echo "\n";
        }
    }

    function printChromo($bits) {
        $chromo = $this->parseChromo($bits);
        foreach ($chromo as $key => $gene) {
            $this->printGeneSymbol($gene);
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

    
    
    
    
    /* The following functions are standard and valid for all models */
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

    function roulette($totalfitness, $population, $fitness) {
        $slice = lcg_value() * $totalfitness;
        $fitnesssofar = 0;

        for ($i=0;$i<$this->POP_SIZE;$i++) {
            $fitnesssofar += $fitness[$i];
            if ($fitnesssofar >= $slice) {
                return $population[$i];
            }
        }

        return "";
    }

    function crossover($offspring1, $offspring2) {
        $result = array();
        if (lcg_value() < $this->CROSSOVER_RATE) {
            $cross = round(lcg_value() * ($this->CHROMO_LENGTH - 1));
            $t1 = substr($offspring1, 0, $cross) . substr($offspring2, $cross, $this->CHROMO_LENGTH - $cross +1);
            $t2 = substr($offspring2, 0, $cross) . substr($offspring1, $cross, $this->CHROMO_LENGTH - $cross +1);
            $result[] = $t1;
            $result[] = $t2;
        }
        return $result;
    }

    function mutate($offspring) {
        for ($i=0;$i<strlen($offspring);$i++) {
            if (lcg_value() < $this->MUTATION_RATE) {
                if ($offspring[$i] == '1') {
                    $offspring[$i] = '0';
                } else {
                    $offspring[$i] = '1';
                }
            }
        }
        return $offspring;
    }

    function findSolution($target) {
        $found = false;
        $iterations = 0;
        while (!$found) {
            $totalfitness = 0;
            // test and update the fitness of every chromosome in the population
            for ($i=0; $i<$this->POP_SIZE;$i++) {
                $this->fitness[$i] = $this->calculateFitness($this->population[$i], $target);
                $totalfitness += $this->fitness[$i];
            }
            // check to see if we have found any solutions (fitness will be 999)
            for ($i=0; $i<$this->POP_SIZE;$i++) {
                if ($this->fitness[$i] == 999) {
                    echo "Found a solution after ".$iterations." iterations !!\n";
                    $this->printChromo($this->population[$i]);
                    $found = true;
                    break;
                }
            }

            // create a new population by selecting two parents at a time and creating offspring
            // by applying crossover and mutation. Do this until the desired number of offspring
            // have been created. 
            $temppop = array();
            $cpop = 0;
            while ($cpop < $this->POP_SIZE) {
                // we are going to create the new population by grabbing members of the old population
                // two at a time via roulette wheel selection.
                $offspring1 = $this->roulette($totalfitness, $this->population, $this->fitness);
                $offspring2 = $this->roulette($totalfitness, $this->population, $this->fitness);

                // and crossover dependent on the crossover rate
                $crossresult = $this->crossover($offspring1, $offspring2);
                if (isset($crossresult[0])) $offspring1 = $crossresult[0];
                if (isset($crossresult[1])) $offspring2 = $crossresult[1];

                // now mutate dependen on the mutation rate
                $offspring1 = $this->mutate($offspring1);
                $offspring2 = $this->mutate($offspring2);

                // add the offsprings in the population with fitness at 0
                $temppop[$cpop++] = $offspring1;
                $temppop[$cpop++] = $offspring2;
            }
            // Reset fitness
            $this->fitness = array();
            for ($i=0; $i<$this->POP_SIZE;$i++) {
                $this->fitness[$i] = 0;
            }
            // Copy temp population to population
            $this->population = $temppop;

            if (!$found) $this->printPopulation($this->population);

            $iterations++;

            if ($iterations==$this->MAX_ALLOWABLE_GENERATIONS) {
                echo "No solution found\n";
                $found = true;
            }

        }
    }

}




//print_r($population);
//printPopulation($population);

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






?>