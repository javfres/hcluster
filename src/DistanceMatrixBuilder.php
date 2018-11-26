<?php

namespace Javfres\HCluster;

class DistanceMatrixBuilder  {

    public static function create($matrix, $names = null){

        if(!is_array($matrix)){
            throw new ClusteringException("Distance matrix is not an array");
        }

        if(is_null($names)){
            $names = range(1, count($matrix));
        }
        
        if(count($matrix) !== count($names)){
            throw new ClusteringException("Invalid number of item names"); 
        }

        //
        // Build the items
        //
        $items = [];
        foreach(array_values($names) as $i => $name){
            $items[] = new DistanceMatrixItem($i, $name);
        }

        //
        // Build the distance object
        //
        $distance = new DistanceMatrix($matrix);


        return (object) [
            "items"    => $items,
            "distance" => $distance
        ];

    }    

}