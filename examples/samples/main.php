<?php

/**
 * This example uses custom item and distance classes
 */


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
// Our list of items
//
$items = [
    new SampleItem('Tomate',     30, 1, 4),
    new SampleItem('Pimiento',   40, 1, 4),
    new SampleItem('Patata 1',   20, 1, 4),
    new SampleItem('Manzana 1',  10, 1, 7),
    new SampleItem('Manzana 2',  10, 4, 7),
    new SampleItem('Manzana 3a', 10, 1, 2),
    new SampleItem('Manzana 3b', 10, 1, 7),
    new SampleItem('Manzana 3c', 10, 1, 4),
    new SampleItem('Patata 2',   20, 1, 2),
    new SampleItem('Patata 3',   20, 2, 1),
];



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
