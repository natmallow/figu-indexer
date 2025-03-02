<?php

namespace gnome\classes\model;

use gnome\classes\DBConnection as DBConnection;
use gnome\classes\model\interface\Factory as Factory;

class Ajax extends DBConnection implements Factory {

    public static function factory () {
        $object = get_called_class();
        return new $object();
    }

    /**
    * toggler for articles
    * changes booleans via ajax
    */
    function toggleArticles( $column, $id_article, $msg = '' ) {

        $sql = "UPDATE articles SET $column = IF ($column, 0, 1) WHERE id_articles = :id";
        $pdoc = $this->dbc->prepare( $sql );
        $paramArr = [ ':id' => $id_article ]; 
        // echo $this->showquery( $sql, $paramArr );
        // exit();
        $pdoc->execute( $paramArr );

        // confirmation response msg
        $sql = 'SELECT title FROM articles WHERE id_articles= :id';
        $pdoc = $this->dbc->prepare( $sql );
        $pdoc->execute( [
            ':id'  =>  $id_article
        ] );

        $title = $pdoc->fetch()[0];
        //  array( 'response' => sprintf( $msg, $title ) );
        header( 'Content-Type: application/json' );
        echo json_encode( array( 'response' => sprintf( $msg, $title ) ) );
        exit;
    }

}

?>