<?php

namespace Javfres\HCluster;

class DistanceMatrixItem {

    private $index = null;
    private $name = null;

    public function __construct($index, $name = null){
        
        $this->index = $index;
        $this->name = $name ? $name : $index;
        
    }

    public function getIndex(){
        return $this->index;
    }

    public function __toString(){
        return "$this->name";
    }     



}