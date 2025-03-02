<?php

namespace gnome\classes\model;

use gnome\classes\DBConnection as DBConnection;
use PDO;
// add file Here as needed
use gnome\classes\service\IndexerAppService;

class PublicationIndexCache extends DBConnection
{

    /**
     * writes cache to DB called after save tracks on publicationIndex
     */

    function savePublicationIndexCache($indices_id, $publication_id)
    {
        $appService = new IndexerAppService();

        // returns json string
        $responseJson = $appService->getTracksValuesFromPublication($indices_id, $publication_id);

        try {
            $this->dbc->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            // add to cache table
            $sql = "INSERT INTO publication_index_cache (
                    indices_id, publication_id, 
                    track_value_json
                ) VALUES (
                    :indices_id, :publication_id, 
                    :track_value_json 
                ) ON DUPLICATE KEY UPDATE 
                    track_value_json = :track_value_json ";

            $pdoc = $this->dbc->prepare($sql);

            $paramArr = [
                ':indices_id' => $indices_id,
                ':publication_id' => $publication_id,
                ':track_value_json' => $responseJson
            ];

            $pdoc->execute($paramArr);
        } catch (\PDOException $e) {
            var_dump('Error: ' . $e->getMessage());
        }
    }

    /**
     * @retuns json
     */

    function getPublicationIndexCache($indices_id, $publication_id)
    {

        // search the DB
        // return empty

        $sql = "SELECT * FROM publication_index_cache 
                WHERE indices_id = :indices_id 
                AND publication_id = :publication_id 
                LIMIT 1";

        $pdoc = $this->dbc->prepare($sql);

        $paramArr = [
            ':indices_id' => $indices_id,
            ':publication_id' => $publication_id
        ];

        // var_dump($this->showquery($sql,$paramArr));

        $pdoc->execute($paramArr);
        return $pdoc->fetch(PDO::FETCH_ASSOC);
    }
}
