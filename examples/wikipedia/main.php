<?php

/**
 * This example uses the values from the Wikipedia page
 * https://en.wikipedia.org/wiki/Hierarchical_clustering
 */


 //
 // Declare the needed classes
 //
require('vendor/autoload.php');
use Javfres\HCluster\HierarchicalClustering;
use Javfres\HCluster\DistanceMatrixBuilder;
use Javfres\HCluster\JH;


//
// The distance matrix
//
$distance_matrix = [
    [0, 17,21,31,23],
    [17, 0,30,34,21],
    [21,30, 0,28,39],
    [31,34,28, 0,43],
    [23,21,39,43, 0],
];

//
// Name of the items (optional, it's just for printing)
//
$item_names = ['a', 'b', 'c', 'd', 'e'];

//
// The DistanceMatrixBuilder, creates an array of items and a distance object.
// We can manually create the items and the distance object from a custom class. 
//
$items_distance = DistanceMatrixBuilder::create($distance_matrix, $item_names);

//
// Create a instance of HierarchicalClustering with the list of items,
// the distance object and the linkage method (see the wikipedia article)
//
$hc = new HierarchicalClustering(
    $items_distance->items,
    $items_distance->distance,
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
