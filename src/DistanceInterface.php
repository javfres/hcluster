<?php

namespace Javfres\HCluster;

interface DistanceInterface  {

    /**
     * Calculate the distance between items $a and $b
     */
    public function dist(&$a, &$b);     

}