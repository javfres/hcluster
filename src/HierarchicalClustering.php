<?php

namespace Javfres\HCluster;

/**
 * The main class of the library that perform the clustering
 */
class HierarchicalClustering {

    const LINKAGE_SINGLE   = 'single';
    const LINKAGE_COMPLETE = 'complete';
    const LINKAGE_AVERAGE  = 'average';

    private $items;
    private $distance;
    private $linkage;
    private $clusters;
    private $root = null;

    /**
     * Constructor
     */
    public function __construct($items, $distance, $linkage){

        $this->items = $items;
        $this->distance = $distance;
        $this->linkage = $linkage;
        
    }


    //
    // Debug functions
    //

    public function printItems(){

        Utils::title("Items");
        foreach($this->items as $it){
            error_log("* " . $it );
        }

    }

    public function printDistanceMatrix($decimals = 2){

        //
        // Build the matrix (to know the max width)
        //
        $WIDTH_INT = 0;
        $matrix = [];
        foreach($this->items as $i => $iti){
            $row = [];
            foreach($this->items as $j => $itj){
                $dist = $i===$j ? 0 : $this->distance->dist($this->items[$i],$this->items[$j]);
                $row[] = $dist;
                $str = number_format($dist,0,"","");
                $WIDTH_INT = max($WIDTH_INT, strlen($str));
            }
            $matrix[] = $row;
        }


        $WIDTH = $WIDTH_INT + $decimals + 3;


        Utils::title("Distance matrix");
        $line = str_repeat(" ", $WIDTH);
        foreach($this->items as $i => $it){
            $line .= str_pad($i,$WIDTH, " ", STR_PAD_LEFT);
        }
        error_log($line);
        foreach($this->items as $i => $iti){
            $line = str_pad($i,$WIDTH);
            foreach($this->items as $j => $itj){
                $dist = $matrix[$i][$j];
                $str = number_format($dist,$decimals,".","");
                $line .= str_pad($str ,$WIDTH, " ", STR_PAD_LEFT);
            }
            error_log($line);
        }

    }


    public function printGroups(&$groups){
  
        Utils::title("Groups");
        error_log("Num groups " . count($groups));
        foreach($groups as $i => $group){
            $cb = Utils::groupcolor($i);
            $ce = Utils:: groupcolor();
            error_log($cb . "Group $i, items " . count($group) );
            foreach($group as $j){
                $id = $this->items[$j];
                error_log("* $id");
            }
            error_log($ce);
        }

    
    }

    public function printGroupsAtDepths($depths){
        Utils::title("Groups at depths");

        foreach($depths as $depth){
            $num = $this->groupsAtDepth($depth);
            error_log("* Groups at $depth: $num");
        }
    
    }


    //
    // 
    //
    public function groupsAtDepth($depth){
        return $this->root->groupsAtDepth($depth);
    }

    public function cut($max_groups=4, $min_depth=0.25){
        return $this->root->cut($max_groups, $min_depth);
    }
    public function printDendrogram(){
        return $this->root->printDendrogram();
    }

    






    public function getItem($i){
        return $this->items[$i];
    }


    public function run(){

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
    
            $this->merge($merges);

            $i++;
        }

        $this->root = $this->clusters[0];

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