<?php

namespace Javfres\HCluster;


class HierarchicalClustering {

    const LINKAGE_SINGLE   = 'single';
    const LINKAGE_COMPLETE = 'complete';
    const LINKAGE_AVERAGE  = 'average';

    public $debug = false;

    private $items;
    private $distance;
    private $linkage;
    private $clusters;

    public function __construct($items, $distance, $linkage){

        $this->items = $items;
        $this->distance = $distance;
        $this->linkage = $linkage;
        

    }

    public function getItem($i){
        return $this->items[$i];
    }


    public function run(){

        if($this->debug){

            title("Hierarchical Clustering");
            error_log("* Linkage: " . $this->linkage);

            title("Items");
            foreach($this->items as $i => $it){
                $name = (string) $it;
                error_log("$i: $name");
            }

            title("Distance matrix");
            $line = str_repeat(" ", 5);
            foreach($this->items as $i => $it){
                $line .= str_pad($i,5);
            }
            error_log($line);
            foreach($this->items as $i => $iti){
                $line = str_pad($i,5);
                foreach($this->items as $j => $itj){
                    $dist = $this->distance->dist($this->items[$i],$this->items[$j]);
                    $line .= str_pad( $dist,5);
                }
                error_log($line);
            }


        } // debug

        //
        // Create the clusters
        //
        $this->clusters = [];
        foreach($this->items as $i => $item){
            $this->clusters[] = new Cluster($this, $i);
        }


        $i = 0;
        while(count($this->clusters) > 1){

            $merges = $this->getNextMerge();
            $cluster_a = $this->clusters[$merges[0]];
            $cluster_b = $this->clusters[$merges[1]];
    
            if($this->debug){
                title("Iteration $i"); 
                error_log("There are " . count($this->clusters) . " clusters" );
                error_log("Best merge is $cluster_a and $cluster_b");
            }

            $this->merge($merges);

            $i++;
        }

        $final = $this->clusters[0];

        if($this->debug){
            title("Final cluster");
            error_log($final);
        }

        return $final;


    } // run


    //
    //
    //
    private function merge($merges){

        // Take cluster a and b
        $a = $this->clusters[$merges[0]];
        unset( $this->clusters[$merges[0]]);
        $b = $this->clusters[$merges[1]];
        unset( $this->clusters[$merges[1]]);
        
        // reindex
        $this->clusters = array_values($this->clusters);

        // Merge the clusters
        $length = $merges[2]/2;
        $merged = new Cluster($this, [$a, $b], $length);
        $this->clusters[] = $merged;

        $a->setParent($merged);
        $b->setParent($merged);


        if($this->debug){
            error_log("Merging $a and $b into $merged");
        }

    }


    private function getNextMerge(){
    
        if($this->linkage === self::LINKAGE_SINGLE ){
            return $this->getNextMergeSingle();
        } else if($this->linkage === self::LINKAGE_COMPLETE ){
            return $this->getNextMergeComplete();
        } else if($this->linkage === self::LINKAGE_AVERAGE ){
            return $this->getNextMergeAverage();
        } 

    }


    //
    // This implements the https://en.wikipedia.org/wiki/Single-linkage_clustering
    //
    // It merges the minimum of the closests pairs
    private function getNextMergeSingle(){

        $best = PHP_INT_MAX;
        $best_a = null;
        $best_b = null;

        // Iterate over n^2 clusters
        foreach($this->clusters as $i => $cluster_a){
            foreach($this->clusters as $j => $cluster_b){

                if($j<=$i) continue;
    
                $items_a = $cluster_a->getAllItems();
                $items_b = $cluster_b->getAllItems();

                $min = PHP_INT_MAX;

                // Iterate over all the items to find the closest pair
                foreach($items_a as $idx_a){
                    foreach($items_b as $idx_b){

                        $item_a = $this->items[$idx_a];
                        $item_b = $this->items[$idx_b];

                        $d = $this->distance->dist($item_a, $item_b);

                        $min = min($min, $d);
                    }
                } // Items

                if($min < $best){
                    $best = $min;
                    $best_a = $i;
                    $best_b = $j;
                }

            }
    
        } // Clusters

        return [$best_a, $best_b, $best];

    } // getNextMergeSingle



    //
    // This implements the https://en.wikipedia.org/wiki/Complete-linkage_clustering
    //
    // It merges the minimum of the fastest pairs
    private function getNextMergeComplete(){

        $best = PHP_INT_MAX;
        $best_a = null;
        $best_b = null;

        // Iterate over n^2 clusters
        foreach($this->clusters as $i => $cluster_a){
            foreach($this->clusters as $j => $cluster_b){

                if($j<=$i) continue;
    
                $items_a = $cluster_a->getAllItems();
                $items_b = $cluster_b->getAllItems();

                $max = 0;

                // Iterate over all the items to find the closest pair
                foreach($items_a as $idx_a){
                    foreach($items_b as $idx_b){

                        $item_a = $this->items[$idx_a];
                        $item_b = $this->items[$idx_b];

                        $d = $this->distance->dist($item_a, $item_b);

                        $max = max($max, $d);
                    }
                } // Items

                if($max < $best){
                    $best = $max;
                    $best_a = $i;
                    $best_b = $j;
                }

            }
    
        } // Clusters

        return [$best_a, $best_b, $best];

    } // getNextMergeComplete



    //
    // This implements the https://en.wikipedia.org/wiki/UPGMA
    //
    // It merges the minimum of the average distance
    private function getNextMergeAverage(){

        $best = PHP_INT_MAX;
        $best_a = null;
        $best_b = null;

        // Iterate over n^2 clusters
        foreach($this->clusters as $i => $cluster_a){
            foreach($this->clusters as $j => $cluster_b){

                if($j<=$i) continue;
    
                $items_a = $cluster_a->getAllItems();
                $items_b = $cluster_b->getAllItems();

                $avg = 0;

                // Iterate over all the items to find the closest pair
                foreach($items_a as $idx_a){
                    foreach($items_b as $idx_b){

                        $item_a = $this->items[$idx_a];
                        $item_b = $this->items[$idx_b];

                        $avg += $this->distance->dist($item_a, $item_b);

                    }
                } // Items

                $avg /= (count($items_a)*count($items_b));

                if($avg < $best){
                    $best = $avg;
                    $best_a = $i;
                    $best_b = $j;
                }

            }
    
        } // Clusters

        return [$best_a, $best_b, $best];

    } // getNextMergeAverage



}