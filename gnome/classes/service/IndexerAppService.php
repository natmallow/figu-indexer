<?php

namespace gnome\classes\service;

use gnome\classes\DBConnection as DBConnection;
use PDO;

// add Models Here as needed
use gnome\classes\model\PublicationIndexCache;
use gnome\classes\model\Indices;

class IndexerAppService extends DBConnection
{

    /**
     * @retuns json 
     */
    function publicationHighlight($indices_id, $publication_id)
    {
        $PubIndexCash = new PublicationIndexCache();
        $response = $PubIndexCash->getPublicationIndexCache($indices_id, $publication_id);
        return $response;
    }

    // the tracking cache should be called only on indexer save
    function getTracksValuesFromPublication($indices_id, $publication_id)
    {

        try {
            // Assuming $this->dbc is already a PDO instance
            $this->dbc->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
            // SQL to create temporary table
            $sql = "CREATE TEMPORARY TABLE temp_publication (
                        temp_publication_id VARCHAR(10),
                        temp_raw_html MEDIUMTEXT
                    )";
        
            // Execute the query
            $this->dbc->exec($sql);

        } catch (\PDOException $e) {
            var_dump( "Error creating temporary table: " . $e->getMessage());
            return;
        }



        try {
            $this->dbc->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
            // Insert data into temp publication table
            $sql = "INSERT INTO temp_publication (temp_publication_id, temp_raw_html)
                    SELECT publication_id, raw_html FROM publications WHERE publication_id = :publication_id LIMIT 1";
        
            $pdoc = $this->dbc->prepare($sql);
            $pdoc->execute([':publication_id' => $publication_id]);
        
            // // Select and var_dump for troubleshooting
            // $sql = "SELECT * FROM temp_publication";
            // $pdoc = $this->dbc->prepare($sql);
            // $pdoc->execute();
            // $row = $pdoc->fetch(PDO::FETCH_ASSOC);
            // var_dump($row);
        } catch (\PDOException $e) {
            var_dump("Error: " . $e->getMessage());
        }



        // Get a list of tracks - assuming it returns a comma-separated string
        $sql = 'SELECT tracks FROM publication_index WHERE publication_id = :publication_id AND indices_id = :indices_id LIMIT 1';

        $pdoc = $this->dbc->prepare($sql);
        $pdoc->execute([
            ':indices_id' => $indices_id,
            ':publication_id' => $publication_id
        ]);
        $row = $pdoc->fetch(PDO::FETCH_ASSOC);
        $ids = explode(',', $row['tracks']);
        // Assuming 'tracks' contains comma-separated IDs

        $columnAlias = [];
        foreach ($ids as $id) {
            $id = trim($id);
            // TODO remove hardcoded
            
            $idEn = $id.'en';
            $idDe = $id.'de';
            // Trim to remove any extra whitespace
            $columnAlias[] = "SUBSTRING_INDEX(SUBSTRING_INDEX(temp_raw_html, '<track-span id=\"$idEn\" class=\"eNum\">', -1), '</track-span>', 1) AS `extracted_text_$idEn`";
            $columnAlias[] = "SUBSTRING_INDEX(SUBSTRING_INDEX(temp_raw_html, '<track-span id=\"$idDe\" class=\"eNum\">', -1), '</track-span>', 1) AS `extracted_text_$idDe`";
        }

        try {
            $this->dbc->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $sql = 'SELECT ' . implode(', ', $columnAlias) . ' FROM temp_publication WHERE temp_publication_id = :publication_id';
            $pdoc = $this->dbc->prepare($sql);
            $pdoc->execute([':publication_id' => $publication_id]);
            $result = $pdoc->fetch(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            var_dump("Error: " . $e->getMessage());
        }


        $tracks = [];
        foreach ($result as $key => $value) {
            if (preg_match('/extracted_text_(.*)/', $key, $matches)) {
                $tracks[] = [
                    'track' => $matches[1],
                    'value' => $value
                ];
            }
        }

        // get indices infomation
        // get publication information
        $trackInfo = [
            'indices_id' => $indices_id,
            'publication_id' => $publication_id,   
            'publication_name' =>  'TODO',
            'publication_description' => 'TODO',
            'publication_link' => 'TODO'         
        ];

        $response = [
            'track_info' => $trackInfo,
            'track_values' => $tracks
        ];

        // $sql = 'DROP TEMPORARY TABLE IF EXISTS temp_publication';
        // $pdoc = $this->dbc->prepare($sql);
        // $pdoc->execute();
// var_dump(666666666);
// var_dump($response);
// return;
        return json_encode($response);
    }

    // should extend pagination
    function getAvailableIndices(){
        $Indices = new Indices();
        $response = $Indices->fetchIndices();
        return $response;
    } 

    // should extend pagination
    function searchAvailableIndices(){
        $Indices = new Indices();
        $response = $Indices->fetchIndices();
        return $response;
    } 

}
