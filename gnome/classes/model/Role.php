<?php

namespace gnome\classes\model;

use gnome\classes\DBConnection as DBConnection;
use gnome\classes\MessageResource as MessageResource;
use PDO;

class Role extends DBConnection {

    private $table = 'role';
    private $encryptHash;

    public function __construct() {
        parent::__construct();
        $this->encryptHash = MessageResource::instance()->getMsg( 'encryptHash' );

    }

    function getRoles() {
        $sql = "SELECT * FROM  $this->table";

        $pdoc = $this->dbc->prepare( $sql );

        $paramArr = [];
        // echo $this->showquery( $sql, $paramArr );
        // exit();
        $pdoc->execute();

        return $pdoc->fetchAll();
    }

 

}

?>