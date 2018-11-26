<?php

class SampleItem  {

    public $id;
    public $x;
    public $y;
    public $z;

    public function __construct($id, $x, $y, $z){

        $this->id = $id;
        $this->x = $x;
        $this->y = $y;
        $this->z = $z;

    }

    public function __toString(){

        $id = str_pad($this->id,20);
        $x = str_pad($this->x, 2, " ", STR_PAD_LEFT);
        $y = str_pad($this->y, 2, " ", STR_PAD_LEFT);
        $z = str_pad($this->z, 2, " ", STR_PAD_LEFT);

        return "$id X:$x  Y:$y  Z:$z";
    }     

}