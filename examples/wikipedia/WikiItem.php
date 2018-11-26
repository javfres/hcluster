<?php

class WikiItem  {

    public $id;

    public function __construct($id){
        $this->id = $id;
    }

    public function __toString(){
        return "$this->id";
    }     

}