<?php

namespace Javfres\HCluster;

class DistanceMatrix implements DistanceInterface {

    private $matrix = [];

    public function __construct($matrix){
        
        if(!self::checkSquare($matrix)){
            throw new ClusteringException("The distance matrix is not square");
        }
        
        $this->matrix = $matrix;
    }

    public function dist(&$a, &$b){

        return $this->matrix[$a->getIndex()][$b->getIndex()];
    }    

    public static function checkSquare($matrix){

        if(!is_array($matrix)) return false;
        $size = count($matrix);

        foreach($matrix as $row){
            if(!is_array($row)) return false;
            if(count($row) !== $size) return false;
        }

        return true;
    }


}