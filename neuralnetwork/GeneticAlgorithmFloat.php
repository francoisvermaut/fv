<?php
Class GeneticAlgorithmFloat {
    // The genes are float numbers, one chromosome of the population is an array of floats
    public $CROSSOVER_RATE;
    public $MUTATION_RATE;
    public $MAX_PERTURBATION;
    public $POP_SIZE;
    public $CHROMO_LENGTH;
    //public $GENE_LENGTH;
    public $MAX_ALLOWABLE_GENERATIONS;
    public $population;
    public $fitness;
    public $totalfitness = 0;
    public $bestfitness = 0;
    public $worstfitness = 0;
    public $avgfitness = 0;
    public $fittest = 0;
    
    public function GeneticAlgorithmFloat($pop_size, $chromo_len, $max_generation, $crossoverrate, $mutationrate, $maxperturb) {
        $this->CROSSOVER_RATE = $crossoverrate;
        $this->MUTATION_RATE = $mutationrate;
        $this->MAX_PERTURBATION = $maxperturb;
        $this->POP_SIZE = $pop_size;
        $this->CHROMO_LENGTH = $chromo_len;
        $this->MAX_ALLOWABLE_GENERATIONS = $max_generation;
        
        $this->population = array();
        $this->fitness = array();
        // Generate a random population
        for ($i=0; $i<$this->POP_SIZE;$i++) {
            $this->population[$i] = $this->getRandomChromo($this->CHROMO_LENGTH);
            $this->fitness[$i] = 0;
        }
        $this->reset();
    }
    
    function getRandomChromo($length) {
        // Get an array of "length" random floats between -1 and 1
        $bits = array();
        for ($i=0;$i<$length;$i++) {
            $bits[] = (lcg_value()*2)-1;
        }
        return $bits;
    }

    function mutate($chromo) {
        for ($i=0;$i<count($chromo);$i++) {
            if (lcg_value() < $this->MUTATION_RATE) {
                $chromo[$i] += ((lcg_value()*2)-1) * $this->MAX_PERTURBATION;
            }
        }
        return $chromo;
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
        if (lcg_value() > $this->CROSSOVER_RATE || $offspring1===$offspring2) {
            $result[] = $offspring1;
            $result[] = $offspring2;
            return $result;
        }
        
        $cross = round(lcg_value() * ($this->CHROMO_LENGTH - 1));
        $t1 = array();
        $t2 = array();
        for ($i=0 ; $i<$cross;$i++) {
            $t1[] = $offspring1[$i];
            $t2[] = $offspring2[$i];
        }
        for ($i=$cross;$i<$this->CHROMO_LENGTH;$i++) {
            $t1[] = $offspring2[$i];
            $t2[] = $offspring1[$i];
        }
        $result[] = $t1;
        $result[] = $t2;
        return $result;
    }

    function reset() {
        $this->totalfitness = 0;
        $this->bestfitness = 0;
        $this->worstfitness = 0;
        $this->avgfitness = 0;
    }
    
    /* Adapt this function to the new model */
    function calculateFitness() {
        $this->totalfitness = 0;
        $highestsofar = 0;
        $lowestsofar = 999999999;
        
        foreach ($this->fitness as $key => $value) {
            if ($value > $highestsofar) {
                $highestsofar = $value;
                $this->fittest = $key;
                $this->bestfitness = $highestsofar;
            }
            if ($value < $lowestsofar) {
                $lowestsofar = $value;
                $this->worstfitness = $lowestsofar;
            }
            $this->totalfitness += $value;
        }
        $this->avgfitness = $this->totalfitness / count($this->fitness);
    }

    function epoch() {
        $this->reset();
        $this->calculateFitness();
        
        $temppop = array();
        $cpop = 0;
        while ($cpop < $this->POP_SIZE) {
            $offspring1 = $this->roulette($this->totalfitness, $this->population, $this->fitness);
            $offspring2 = $this->roulette($this->totalfitness, $this->population, $this->fitness);

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

        $this->population = $temppop;
        
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


?>