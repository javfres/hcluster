<?php

require('vendor/autoload.php');

use Javfres\HCluster\HierarchicalClustering;
use Javfres\HCluster\Utils;
use Javfres\HCluster\DistanceMatrixBuilder;
use Javfres\HCluster\JH;


$distance_matrix = [
    [0, 17,21,31,23],
    [17, 0,30,34,21],
    [21,30, 0,28,39],
    [31,34,28, 0,43],
    [23,21,39,43, 0],
];

$item_names = ['a', 'b', 'c', 'd', 'e'];

$items_distance = DistanceMatrixBuilder::create($distance_matrix, $item_names);

$hc = new HierarchicalClustering(
    $items_distance->items,
    $items_distance->distance,
    HierarchicalClustering::LINKAGE_AVERAGE
);

$hc->printItems();

$hc->printDistanceMatrix();

$root = $hc->run();

// Take a single one with $root->groupsAtDepth()
$root->printGroupsAtDepths();

$groups = $root->cut();

JH::log($groups);

$hc->printGroups($groups);


$root->printDendrogram();
