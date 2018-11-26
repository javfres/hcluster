<?php

use Javfres\HCluster\DistanceInterface;

class WikiDistance implements DistanceInterface {

    public function dist(&$a, &$b){


        $vala = ord($a->id) - ord('a'); 
        $valb = ord($b->id) - ord('a'); 


        $table = [
            [0, 17,21,31,23],
            [17, 0,30,34,21],
            [21,30, 0,28,39],
            [31,34,28, 0,43],
            [23,21,39,43, 0],
        ];

        return $table[$vala][$valb];


    }    

}