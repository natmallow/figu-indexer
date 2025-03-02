<?php

namespace gnome\classes\model;

use gnome\classes\DBConnection as DBConnection;
use gnome\classes\Paginator as Paginator;

use PDO;

class Keyword extends DBConnection {

    protected $table = 'publication_keyword';
    protected $tableIgnore = 'publication_keyword_ignore';

    /**
    * $keywords array string[]
    * loops through a keywords array and checks if the words are on ignore list
    * returns the words NOT in the ignore db table
    * */
    function checkKeywords( $keywords ) {

        $sql = "SELECT * FROM publication_keyword_ignore
                WHERE keyword_ignore = :keyword_ignore";

        $pdoc = $this->dbc->prepare( $sql );

        $stringArr = [];

        foreach ( $keywords as $value ) {
            $paramArr = [
                ':keyword_ignore' => $value,
            ];

            $pdoc->execute( $paramArr );
            // var_dump( $pdoc->fetchColumn() );
            if ( $pdoc->fetchColumn() == 0 ) {
                array_push( $stringArr, $value );
            }
        }

        return $stringArr;
    }

    /**
    * @args $keyWordsStr comma sepretated wordsstring
    * @return $keywordIds[] int
    */
    function saveKeywordsGetIds( $keyWordsStr ) {

        $sql = "INSERT INTO publication_keyword (keyword) 
                VALUES(:keyword) 
                ON DUPLICATE KEY UPDATE 
                publication_keyword_id=LAST_INSERT_ID(publication_keyword_id)";

        $pdoc = $this->dbc->prepare( $sql );
        // creates array from string
        $keyWords = explode( ',', $keyWordsStr );

        $keywordIds = [];

        foreach ( $keyWords as $value ) {
            if ( trim( $value ) != '' ) {
                $paramArr = [
                    ':keyword' => trim( addslashes( $value ) ) // escape string "
                ];
                // run query 
                $pdoc->execute( $paramArr );
                //push to 
                $keywordIds[] = $this->dbc->lastInsertId();
            }
        }
        return $keywordIds;
    }

    // letter is already scrubbed
    function getIgnoreKeywords( $letter = 'a' ) {
        // like keywords
        $condition  =  " keyword_ignore LIKE '{$letter}%'";

        $count = $this->getCount('publication_keyword_ignore', $condition);

        $sql = "SELECT publication_keyword_ignore_id AS keyword_id, keyword_ignore AS keyword
                FROM publication_keyword_ignore
                WHERE ".$condition." ORDER BY keyword_ignore ASC";

                $pages = new Paginator( $count, 1 );

                // $sql .= $this->paginate();
                $limit  = ' LIMIT '.$pages->limit_start.','.$pages->limit_end;
            
            $response = $this->getQuery( $sql.$limit )->fetchAll();


        return [ $response, $pages ];

    }

    function getUsedKeywords( $letter = 'a' ) {

        // like keywords
        $condition  =  " keyword LIKE '{$letter}%'";

        $count = $this->getCount('publication_keyword', $condition);

        $sql = "SELECT publication_keyword_id AS keyword_id, keyword
                FROM publication_keyword 
                WHERE ".$condition." ORDER BY keyword ASC";

                $pages = new Paginator( $count, 1 );

                // $sql .= $this->paginate();
                $limit  = ' LIMIT '.$pages->limit_start.','.$pages->limit_end;
            
            $response = $this->getQuery( $sql.$limit )->fetchAll();

            
        // die($sql.$limit);

        return [ $response, $pages ];

    }



}

                ?>