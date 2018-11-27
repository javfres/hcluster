<?php

/**
 * This example uses custom item and distance classes 
 * with random items
 */

 $NUM_SAMPLES = 23;

 //
 // Declare the needed classes
 //
 require('vendor/autoload.php');
 use Javfres\HCluster\HierarchicalClustering;
 use Javfres\HCluster\DistanceMatrixBuilder;
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
    $items[] = new SampleItem( $names[$j] . " $n", $j, rand(0,500)/100, rand(0,50)/100 );
}



//
// Create a instance of HierarchicalClustering with the list of items,
// the distance object and the linkage method
//
$hc = new HierarchicalClustering(
    $items, new SampleDistance(),
    HierarchicalClustering::LINKAGE_AVERAGE
);


//
// Print the items and the distance matrix (debug)
//
$hc->printItems();
$hc->printDistanceMatrix();


//
// Execute the hierarchical clustering 
//
$hc->run();

//
// List the cluster groups at different depths of the dendrogram
// Take a single one with $hc->groupsAtDepth()
//
$hc->printGroupsAtDepths([0.8,0.6,0.4,0.2]);

//
// Cut the dendrogram into groups of items
// The default parameters are: cut($max_groups=4, $min_depth=0.25)
//
$groups = $hc->cut();
JH::log($groups);
$hc->printGroups($groups);

//
// Print the dendrogram
//
$hc->printDendrogram();




