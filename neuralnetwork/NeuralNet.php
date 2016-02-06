<?php
class NeuralNet {
    public $numInputs = 0;
    public $numOutputs = 0;
    public $numHiddenLayers = 0;
    public $numNeuronsPerHiddenLayer = 0;
    public $neuronlayers = array();
    
    public function NeuralNet($nInputs, $nOutputs, $nLayers, $nNeuronsPerLayer) {
        $this->numInputs = $nInputs;
        $this->numOutputs = $nOutputs;
        $this->numHiddenLayers = $nLayers;
        $this->numNeuronsPerHiddenLayer = $nNeuronsPerLayer;
        
        // Create the layers
        if ($nLayers>0) {
            // First layer has nInputs inputs and nNeuronsPerLayer neurons
            $this->neuronlayers[] = new NeuronLayer($nInputs, $nNeuronsPerLayer);
            // The other layers has nNeuronsPerLayer inputs
            for ($i=1; $i<$nLayers; $i++) {
                $this->neuronlayers[] = new NeuronLayer($nNeuronsPerLayer, $nNeuronsPerLayer);
            }
            // The last layer has nNeuronsPerLayer inputs and nOutputs neurons
            $this->neuronlayers[] = new NeuronLayer($nNeuronsPerLayer, $nOutputs);
        } else {
            // No hidden layers. Just create the outputs
            $this->neuronlayers[] = new NeuronLayer($nInputs, $nOutputs);
        }
    }
    
    public function update($inputs) {
        if (count($inputs)!=$this->numInputs) return array();
        $outputs = null;
        for ($i=0;$i<$this->numHiddenLayers+1;$i++) {
            $layer = $this->neuronlayers[$i];
            if ($i==0) {
                $outputs = $layer->process($inputs);
            } else {
                $outputs = $layer->process($outputs);
            }
        }
        
        return $outputs;
    }
    
    public function getNumberOfWeights() {
        $weights = 0;
        foreach ($this->neuronlayers as $key => $layer) {
            foreach ($layer->neurons as $key => $neuron) {
                $weights += $neuron->numInputs;
            }
        }
        return $weights;
    }

    public function getWeights() {
        $weights = array();
        foreach ($this->neuronlayers as $key => $layer) {
            foreach ($layer->neurons as $key => $neuron) {
                for ($i=0;$i<$neuron->numInputs;$i++) {
                    $weights[] = $neuron->weights[$i];
                }
            }
        }
        return $weights;
    }

    public function putWeights($weights) {
        $pos = 0;
        foreach ($this->neuronlayers as $key => $layer) {
            foreach ($layer->neurons as $key => $neuron) {
                for ($i=0;$i<$neuron->numInputs;$i++) {
                    $neuron->weights[$i] = $weights[$pos];
                    $pos++;
                }
            }
        }
    }
}
?>