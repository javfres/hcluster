<?php


namespace Javfres\HCluster;


class ClusteringException extends \Exception {

    public function __construct($message = null) {    
        parent::__construct($message);
    }
}
