<?php

namespace Javfres\HCluster;


class Cluster {

    private $clustering = null;
    private $items = null;
    private $length = null;
    private $group = null; // Group after being cut
    private $parent = null;

    public function __construct($clustering, $items, $length=0){

        $this->clustering = $clustering;

        if(!is_array($items)){
            $items = [$items];
        }
        $this->items = $items;
        $this->length = $length;

    }

    public function getLength(){
        return $this->length;
    }

    public function getItems(){
        return $this->items;
    }

    public function getAllItems(){

        $items = [];
        foreach($this->items as $it){

            if(is_numeric($it)){
                $items[] = $it;
            } else {
                $items = array_merge($items, $it->getAllItems());
            }
        }
        return $items;

    }

    public function strname(){

        $stritems = implode(" ",$this->getAllItems());

        if(strlen($stritems) > 20){
            $stritems = substr($stritems, 0, 17) . "...";
        }

        $length = number_format($this->length,2);
        $str = "Cluster [$stritems](L$length)";

        if($this->group!=null){
            $str .= "(G$this->group)";
        }

        return $str;

    }

    public function strnameleaf($idx=0){

        $stritems = implode(" ",$this->getAllItems());
        $str = "Leaf $stritems";

        if($this->group!==null){
            $str .= " (G$this->group)";
        }

        return $str;

    }


    public function setGroup($group){

        $this->group = $group;
        foreach($this->items as $it){
            if(is_numeric($it)) continue;
            $it->setGroup($group);
        }
    }

    public function getParent(){
        return $this->parent;
    }

    public function setParent($parent){
        $this->parent = $parent;
    }


    public function cut($max_groups, $min_depth){

        $max_length = $this->length;
        $queue = [$this];

        // While we have not reach the max groups
        while(count($queue) < $max_groups){


            // Sort the queue
            usort($queue, function($a, $b){
                return $a->getLength() < $b->getLength();
            });

            // Get the top element
            $top = $queue[0];

            // If the length of cut is bigger than the limit, 
            // cut and add the branches to the queue
            $length_per = $top->getLength()/$max_length;
            if($length_per > $min_depth){
                unset($queue[0]);
                $queue = array_values($queue);
                foreach($top->getItems() as $item){
                    $queue[] = $item;
                }
            } else {
                break;
            }

        } // while


        $groups = [];
        foreach($queue as $i => $group){
            $group->setGroup($i);
            $items = array_values($group->getAllItems());
            sort($items);
            $groups[] = $items;
        }

        return $groups;



    } // cut



    public function groupsAtDepth($depth){

        $max_length = $this->length;
        $queue = [$this];

        // While 
        while(true){

            // Sort the queue
            usort($queue, function($a, $b){
                return $a->getLength() < $b->getLength();
            });

            // Get the top element
            $top = $queue[0];

            // If the length of cut is bigger than the limit, 
            // add the branches to the queue
            $length_per = $top->getLength()/$max_length;
            if($length_per > $depth){
                unset($queue[0]);
                $queue = array_values($queue);
                foreach($top->getItems() as $item){
                    $queue[] = $item;
                }
            } else {
                break;
            }

        } // while

        return count($queue);

    } // groupsAtDepth










    public function debugStrDendrogram($breakline="\n", $usecolor=true, $level=0, $max_length=0, $length_scale=0, $parent=null){

        $res = '';

        if($level === 0){
            $res .= Utils::strtitle("Dendrogram");
            $max_length = $this->length;
            $length_scale = 99.99 / $this->length;
        }

        $spaces = $this->debugStrDendrogram_calculate_spaces($usecolor, $max_length, $length_scale, $level, $parent);


        // Color begin and end
        if($usecolor){
            $cb = Utils::groupcolor($this->group);   // Color begin
            $ce = Utils::groupcolor();               // Color end
        } else {
            $cb = ''; $ce='';
        }

        if(count($this->items)>1){
            $res .= $spaces . $cb . '┼'  . $this->strname() . $ce . $breakline;
        } else {
            $res .= $spaces . $cb . '┼' . $this->strnameleaf() . $ce . $breakline;
        }


        foreach($this->items as $item){

            if(!is_numeric($item)){
                $res .= $item->debugStrDendrogram($breakline, $usecolor, $level+1,$max_length,$length_scale,$this);
            }
        }

        return $res;


    } // printDendrogram


    private function debugStrDendrogram_calculate_spaces($usecolor, $max_length, $length_scale, $level, $parent){

        $spaces = '';

        $p = $parent;
        $l = $level;
        while($p !== null){

            $l--;

            $pp = $p->parent;
            if($pp === null) break;

            $num_spaces = floor(($pp->length-$p->length)*$length_scale);
            
            if($usecolor){
                $cb = Utils::groupcolor($pp->group);   // Color begin
                $ce = Utils::groupcolor();               // Color end
            } else {
                $cb = ''; $ce='';
            }

            $newspaces = $cb . '│' . $ce . str_repeat(" ", $num_spaces);

            $spaces = $newspaces . $spaces;  

            $p = $pp;

        }


        if($usecolor){
            $cb = Utils::groupcolor($parent ? $parent->group : null);   // Color begin
            $ce = Utils::groupcolor();    // Color end 
        } else {
            $cb = ''; $ce='';
        }
                              

        if($parent === null){
            $num_points = 0;
        } else {
            $num_points = floor(($parent->length-$this->length)*$length_scale);
            $spaces .= $cb . '├' . str_repeat("─", $num_points) .$ce;
        }

        return $spaces;

    }


    

    //
    // Debug functions
    //





}