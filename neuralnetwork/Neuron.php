<?php

class Neuron {
    public $numInputs = 0;
    public $weights = array();
    
    public function Neuron($nInputs) {
        $this->numInputs = $nInputs;
        for ($i=0; $i<$nInputs+1; $i++) {
            // The last item of the array is to store the bias
            // Initialize with random float between -1 and 1
            $this->weights[] = (lcg_value() * 2) -1;
            //$this->weights[] = rand();
        }
    }
    
    public function calculate($inputs) {
        $activation = 0;
        if (count($inputs)!=$this->numInputs) return null;
        for ($i=0; $i<$this->numInputs;$i++) {
            $activation += $this->weights[$i] * $inputs[$i];
        }
        // Substract the bias
        $activation -= $this->weights[$this->numInputs];
        
        return $activation;
    }
}
?>
