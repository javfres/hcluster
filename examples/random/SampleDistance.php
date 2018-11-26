<?php

use Javfres\HCluster\DistanceInterface;

class SampleDistance implements DistanceInterface {

    public function dist(&$a, &$b){

        $d =
            abs($a->x - $b->x) * 10 +
            abs($a->y - $b->y)      + 
            abs($a->z - $b->z);

        return $d;
        
    }    

}