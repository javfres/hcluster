<?php

require('vendor/autoload.php');

require(__DIR__ . '/SampleItem.php');
require(__DIR__ . '/SampleDistance.php');


use Javfres\HCluster\HierarchicalClustering;
use Javfres\HCluster\Utils;



Utils::title("Clustering");

// Two groups
$itemsX = [
    new SampleItem('Tomate A',     10, 10, 4),
    new SampleItem('Tomate B',     10, 20, 4),
    new SampleItem('Tomate C',     10, 10, 5),
    new SampleItem('Patata 1',     50, 30, 1),
    new SampleItem('Patata 2',     50, 20, 1),
    new SampleItem('Patata 3',     50, 30, 4),
    new SampleItem('Patata 4',     50, 20, 4),
];

// Only 3???
$itemsX = [
    new SampleItem('Tomate',       30, 1, 4),
    new SampleItem('Tomate',       30, 1, 7),
    new SampleItem('Tomate',       30, 1, 8),
    new SampleItem('Cherry 2',     30, 10, 5),
    new SampleItem('Cherry 2',     30, 10, 3),

    new SampleItem('Manzana 1',  20, 3, 7),
    new SampleItem('Manzana 2',  20, 1, 10),

    new SampleItem('Arroz',     40, 10, 60),

];

// 4 types
$itemsX = [
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

// 4 types + very different -> 2 groups
$itemsX = [
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
    new SampleItem('Raspado Ojos',    -120, 2, 1),
];


// Small number of items
$itemsX = [
    new SampleItem('Platano',     -10, 3, 1),
    new SampleItem('Tomate',       10, 1, 4),
    new SampleItem('Patata',     20, 3, 1),
    new SampleItem('Melon',     30, 3, 1),
];


// Very Similiar
$itemsC = [
    new SampleItem('Tomate',     3, 1, 4),
    new SampleItem('Pimiento',   4, 1, 4),
    new SampleItem('Patata 1',   2, 1, 4),
    new SampleItem('Manzana 1',  1, 1, 7),
    new SampleItem('Manzana 2',  1, 4, 7),
    new SampleItem('Manzana 3a', 1, 1, 2),
    new SampleItem('Manzana 3b', 1, 1, 7),
    new SampleItem('Manzana 3c', 1, 1, 4),
    new SampleItem('Patata 2',   2, 1, 2),
    new SampleItem('Patata 3',   2, 2, 1),
];


// Random
$items = [];
$names = ['broccoli','corn','cucumber','lettuce','pumpkin','tomato', 'onion', 'carrot', 'banana'];
$names_num = array_map(function($a){ return 0; },$names);


for($i=0; $i<20; $i++){
    $j = rand(0, count($names)-1);
    $n = ++$names_num[$j];
    $items[] = new SampleItem( $names[$j] . " $n", $j*10, rand(0,50), rand(0,50) );
}



Utils::title("Items");
foreach($items as $it){
    error_log("* " . $i );
}



$hc = new HierarchicalClustering($items, new SampleDistance(), HierarchicalClustering::LINKAGE_AVERAGE);
//$hc->debug = true;
$root = $hc->run();
Utils::title("Groups?");

foreach(range(0.9,0.01,-0.1) as $depth){
    $num = $root->groupsAtDepth($depth);
    error_log("* Groups at $depth: $num");
}


$groups = $root->cut(5,0.1);
Utils::title("Groups");
error_log("Num groups " . count($groups));
foreach($groups as $i => $group){
    $cb = Utils::groupcolor($i);
    $ce = Utils::groupcolor();
    error_log($cb . "Group $i, items " . count($group) );
    foreach($group as $j){
        $id = $items[$j]->id;
        error_log("* $id");
    }
    error_log($ce);
}


$root->print();
