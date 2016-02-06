<?php
include_once("Neuron.php");
include_once("NeuronLayer.php");
include_once("NeuralNet.php");

Class MineSweeper {
    public $fieldmaxx;
    public $fieldmaxy;
    public $posx;
    public $posy;
    public $lookatx;
    public $lookaty;
    public $brain;
    public $ltrackspeed;
    public $rtrackspeed;
    public $rotation;
    public $fitness;
    public $closestmineindex;
    
    public function MineSweeper($fieldmaxx, $fieldmaxy) {
        $this->fieldmaxx = $fieldmaxx;
        $this->fieldmaxy = $fieldmaxy;
        
        // Random initial position
        $this->posx = round(lcg_value() * $fieldmaxx);
        $this->posy = round(lcg_value() * $fieldmaxy);
        
        // Random initial rotation
        //$this->rotation = lcg_value() * pi() * 2;
        $this->rotation = 0; // looking right, in angle radius
        $this->lookatx = 1; // looking right, in x = cos and y = sin
        $this->lookaty = 0;
        $this->fitness = 0;
        $this->ltrackspeed = 0.16;
        $this->rtrackspeed = 0.16;
        
        // Initialize brain = neuralnet
        // 4 inputs, 2 outputs, 1 layer, 6 neurons per layer
        // Inputs are 2 coord closest mine, 2 coord current direction of sweeper
        $this->brain = new NeuralNet(4, 2, 1, 6);
    }
    
    public function reset() {
        // Random initial position
        $this->posx = lcg_value() * $fieldmaxx;
        $this->posy = lcg_value() * $fieldmaxy;
        $this->lookatx = 0;
        $this->lookaty = 0;
        
        // Random initial rotation
        $this->rotation = lcg_value() * pi();
        
        $this->fitness = 0;
    }
    
    public function update($mines) {
        $inputs = array();
        $this->getClosestMine($mines);
        $closestmine = $mines[$this->closestmineindex];
        
        //echo "Closest mine = ".$this->closestmineindex." at position ".$closestmine[0].":".$closestmine[1]."\n";
        
        $inputs[] = $closestmine[0];
        $inputs[] = $closestmine[1];
        $inputs[] = $this->lookatx;
        $inputs[] = $this->lookaty;
        
        $outputs = $this->brain->update($inputs);
        
        /*echo "Inputs\n";
        print_r($inputs);
        echo "Outputs\n";
        print_r($outputs);*/
        
        $this->ltrackspeed = $outputs[0];  // between 0 and 1 by sigmoid definition
        $this->rtrackspeed = $outputs[1];
        
        $rotforce = ($this->ltrackspeed - $this->rtrackspeed) * pi(); // rotforce between -1 and 1 -> between -pi and pi in radius
        //echo "rotforce=".$rotforce."\n";
        
        $this->rotation = $rotforce;
        //echo "rotation=".$this->rotation."\n";
        
        $speed = $this->ltrackspeed + $this->rtrackspeed;
        //echo "speed=".$speed."\n";
        
        $this->lookatx = -sin($this->rotation);
        $this->lookaty = cos($this->rotation);
        //echo "lookat=".$this->lookatx.":".$this->lookaty."\n";
        
        //$this->posx += $this->lookatx * $speed * $this->posx;
        //$this->posy += $this->lookaty * $speed * $this->posy;
        $this->posx += $this->lookatx * $speed;
        $this->posy += $this->lookaty * $speed;
        $this->posx = round($this->posx);
        $this->posy = round($this->posy);
        
        if ($this->posx > $this->fieldmaxx) $this->posx = 0;
        if ($this->posx < 0) $this->posx = $this->fieldmaxx;
        if ($this->posy > $this->fieldmaxy) $this->posy = 0;
        if ($this->posy < 0) $this->posy = $this->fieldmaxy;
        
    }
    
    public function getClosestMine($mines) {
        $closestsofar = 99999999;
        for ($i=0; $i<count($mines);$i++) {
            $mine = $mines[$i]; // this is an array of X & Y
            $dist = sqrt(pow($mine[0] - $this->posx,2) + pow($mine[1] - $this->posy,2));
            if ($dist<$closestsofar) {
                $closestsofar = $dist;
                $this->closestmineindex = $i;
            }
        }
    }
    
    public function checkForMine($mines, $size) {
        $closestmine = $mines[$this->closestmineindex];
        $dist = sqrt(pow($closestmine[0] - $this->posx,2) + pow($closestmine[1] - $this->posy,2));
        if ($dist < ($size + 5)) {
            return $this->closestmineindex;
        }
        
        return -1;
    }
}    
?>