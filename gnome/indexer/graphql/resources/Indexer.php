<?php

namespace gnome\indexer\api\resources;

// this is the indexer API
class Indexer {

    public static function factory () {
        $object = get_called_class();
        return new $object();        
    }

    function getIndicies($name = '') {
        // Your logic for handling publication requests
        echo "This is a test";
        // You might include other PHP files or write the logic here directly
    }

}


