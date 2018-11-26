<?php

require('vendor/autoload.php');

require(__DIR__ . '/WikiItem.php');
require(__DIR__ . '/WikiDistance.php');


use Javfres\HCluster\HierarchicalClustering;
use Javfres\HCluster\Utils;

Utils::title("Clustering");


$items = [
    new WikiItem('a'),
    new WikiItem('b'),
    new WikiItem('c'),
    new WikiItem('d'),
    new WikiItem('e'),
];



Utils::title("Items");
foreach($items as $it){
    error_log("* " . $it );
}



$hc = new HierarchicalClustering($items, new WikiDistance(), HierarchicalClustering::LINKAGE_AVERAGE);
//$hc->debug = true;
$root = $hc->run();
Utils::title("Groups?");

foreach([0.8,0.6,0.4,0.2] as $depth){
    $num = $root->groupsAtDepth($depth);
    error_log("* Groups at $depth: $num");
}


$groups = $root->cut();
Utils::title("Groups");
error_log("Num groups " . count($groups));
foreach($groups as $i => $group){
    $cb = Utils::groupcolor($i);
    $ce = Utils:: groupcolor();
    error_log($cb . "Group $i, items " . count($group) );
    foreach($group as $j){
        $id = $items[$j]->id;
        error_log("* $id");
    }
    error_log($ce);
}


$root->print();

