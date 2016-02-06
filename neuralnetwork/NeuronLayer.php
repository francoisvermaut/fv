<?php
class NeuronLayer {
    public $numNeurons = 0;
    public $numInputs = 0;
    public $neurons = array();
    
    public function NeuronLayer($nInputs, $nNeurons) {
        $this->numNeurons = $nNeurons;
        $this->numInputs = $nInputs;
        for ($i=0; $i<$nNeurons; $i++) {
            $this->neurons[] = new Neuron($nInputs);
        }
    }
    
    public function process($inputs) {
        $outputs = array();
        if (count($inputs)!=$this->numInputs) return null;
        
        // For all neurons in this layer, calculate the output
        foreach ($this->neurons as $key => $neuron) {
            $outputs[$key] = $this->sigmoid($neuron->calculate($inputs));
        }
        
        return $outputs;
    }
    
    public function sigmoid($a) {
        return 1/(1+exp(-$a));
    }
}

?>