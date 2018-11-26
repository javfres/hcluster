<?php

namespace Javfres\HCluster;

class DistanceCache implements DistanceInterface {

    private $distance;
    private $cache = [];
    private $num_calls = 0;
    private $num_cached = 0;
    private $symmetric = true;

    public function __construct(DistanceInterface $distance, $symmetric = true){

        $this->distance = $distance;
        $this->symmetric = $symmetric;

    }

    public function dist(&$a, &$b){

        $this->num_calls++;
        $hash = $this->getHash($a,$b);

        if(!isset($this->cache[$hash])){
            $this->cache[$hash] = $this->distance->dist($a,$b);  
        } else {
            $this->num_cached++;
        }

        return $this->cache[$hash];
    }    


    private function getHash(&$a, &$b){

        $id_a = spl_object_hash($a);
        $id_b = spl_object_hash($b);

        if($this->symmetric && strcmp($id_a, $id_b) > 0){
            return $id_b.$id_a;
        }

        return $id_a.$id_b;
    }

    public function printStats(){

        $hits = $this->num_cached / $this->num_calls;
        $hits = sprintf("%.2f%%", $hits * 100);

        error_log("Distance called $this->num_calls times, $this->num_cached where cached ($hits)");
    }


}

