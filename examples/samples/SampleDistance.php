<?php

use Javfres\HCluster\DistanceInterface;

/**
 * A simple class that implements the DistanceInterface interface
 */
class SampleDistance implements DistanceInterface {

    /**
     * Calculate the distance between items $a and $b
     */
    public function dist(&$a, &$b){

        $d =
            abs($a->x - $b->x) * 10 +
            abs($a->y - $b->y)      + 
            abs($a->z - $b->z);

        return $d;
        
    }    

}