<?php

srand(100);

/**
 * This example uses the DistanceCache class
 */

 $NUM_SAMPLES = 10;

 //
 // Declare the needed classes
 //
 require('vendor/autoload.php');
 use Javfres\HCluster\HierarchicalClustering;
 use Javfres\HCluster\DistanceMatrixBuilder;
 use Javfres\HCluster\DistanceCache;
 use Javfres\HCluster\JH;
 
require(__DIR__ . '/SampleItem.php');
require(__DIR__ . '/SampleDistance.php');


//
// Create a random list of items
//
$items = [];
$names = ['broccoli','corn','cucumber','lettuce','pumpkin','tomato', 'onion', 'carrot', 'banana'];
$names_num = array_map(function($a){ return 0; },$names);

for($i=0; $i<$NUM_SAMPLES; $i++){
    $j = rand(0, count($names)-1);
    $n = ++$names_num[$j];
    $items[] = new SampleItem( $names[$j] . " $n", $j*10, rand(0,50), rand(0,50) );
}

//
// Create the cached distance
//
$distance = new DistanceCache(new SampleDistance(),true);

//
// Create a instance of HierarchicalClustering with the list of items,
// the distance object and the linkage method
//
$hc = new HierarchicalClustering(
    $items, $distance,
    HierarchicalClustering::LINKAGE_AVERAGE
);

//
// Execute the hierarchical clustering 
//
$hc->run();

//
// Output
//
error_log("Clustering with $NUM_SAMPLES items");
$distance->printStats();

