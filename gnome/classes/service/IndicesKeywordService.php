<?php

namespace gnome\classes\service;

use gnome\classes\DBConnection as DBConnection;
use gnome\classes\model\Keyword;
use PDO;

class IndicesKeywordService extends DBConnection
{

    // from the index we link the master keyword here
    public function linkKeywordToIndex($indexId, $words)
    {
        // add the keyword
        $Keyword = new Keyword();
        $keywordIds = $Keyword->saveKeywordsGetIds($words);

        // associate keyword id with index id
        $this->addKeywordLinks($indexId, $keywordIds);

        return $keywordIds;
    }

    private function addKeywordLinks($indexId, $keywordIds = [])
    {
        $sql = "INSERT IGNORE INTO indices_master_list_keyword_link (publication_keyword_id, indices_id)
                VALUES (:publication_keyword_id, :indices_id)";

        $stmt = $this->dbc->prepare($sql);

        foreach ($keywordIds as $keywordId) {
            $params = [
                ':publication_keyword_id' => $keywordId,
                ':indices_id' => $indexId
            ];
            $stmt->execute($params);
        }
    }

    public function unlinkKeywordToIndex($indices_id, $keywordId)
    {

        $sql = "DELETE FROM indices_master_list_keyword_link WHERE 
            indices_id = :indices_id AND 
            publication_keyword_id = :publication_keyword_id ";

        $stmt = $this->dbc->prepare($sql);

        $params = [
            ':indices_id' => $indices_id,
            ':publication_keyword_id' => $keywordId
        ];
        // echo $this->showquery( $sql, $params );
        // exit();

        $stmt->execute($params);
    }

    /**
     * Returns array of master word list associated with indices
     */
    public function getIndicesMasterKeywords($indices_id, $onlyIds = false)
    {

        $selectBlock = " pk.publication_keyword_id as id, pk.keyword as value ";

        if ($onlyIds) {
            $selectBlock = " pk.publication_keyword_id ";
        }

        $sql = "SELECT $selectBlock FROM publication_keyword pk
                INNER JOIN indices_master_list_keyword_link mlk ON mlk.publication_keyword_id = pk.publication_keyword_id 
                WHERE mlk.indices_id = :indices_id";

        $stmt = $this->dbc->prepare($sql);

        $params = [
            ':indices_id' => $indices_id
        ];

        // echo $this->showquery( $sql, $params );
        // exit();

        $stmt->execute($params);
        // keep assoc org $stmt->fetchAll(PDO::FETCH_OBJ);
        $response = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($onlyIds) {
            // Convert the associative array to a simple array of IDs
            return array_column($response, 'publication_keyword_id');
        }

        return $response;
    }

    /**
     * @indices_id int
     * @publication_ids json string 
     */
    function queMasterKeywordSearch($indices_id, $publication_ids)
    {

        $sql = "INSERT INTO publication_keyword_search_queue (indices_id, publication_ids) 
                VALUES (:indices_id, :publication_ids)
                ON DUPLICATE KEY UPDATE
                publication_ids = :publication_ids, 
                search_started_on =  NULL, 
                search_completed_on = NULL";
   

        $stmt = $this->dbc->prepare($sql);
        $stmt->bindParam(':indices_id', $indices_id);
        $stmt->bindParam(':publication_ids', $publication_ids);
        $stmt->execute();

        // Get the ID of the newly inserted row
        return ['queue_id'=> $this->dbc->lastInsertId()];

    }

    public function getMySQLTime()
    {

        // SQL query to get the current time from the MySQL server
        $sql = "SELECT NOW() AS 'current_time'";

        $stmt = $this->dbc->prepare($sql);

        $stmt->execute();

        // Fetch the result
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        // Get the current time from the result
        return $result['current_time'];
    }


    // master keyword search this stages the search
    function masterKeywordSearch($indices_id, $publications)
    {
        // first set up an array to search
        // get the keywords from the indices
        // $isSearchInProgress = $this->getIsSearchInprogress($indices_id);

        // if ($isSearchInProgress) {
        //     return [
        //         'success' => false,  // Indicate the operation "failed" in the sense that a search is still in progress
        //         'message' => 'Currently there is a search in progress. Please try again later.'
        //     ];
        // }

        // return a list of the keyword Ids
        $publication_keyword_ids = $this->getIndicesMasterKeywords($indices_id, true);

        // continue
        $stageComplete = $this->stageWordSearchBatched($indices_id, $publications, $publication_keyword_ids);

        // insert into indicies_master_keyword_publication_status ()

        if ($stageComplete) {
            return [
                'success' => true,
                'message' => 'Seach has been added to que and will begin shortly'
            ];
        }
    }



    function stageWordSearchBatched($indices_id, $publication_ids = [], $publication_keyword_ids = [], $batchSize = 100)
    {

        // Check if there is data to process
        if (empty($publication_ids) || empty($publication_keyword_ids)) {
            return;  // No data to process
        }

        $this->dbc->beginTransaction();  // Start the transaction

        $values = [];
        $params = [];
        $count = 0;
        $totalParams = 0;  // To keep unique parameter names
        foreach ($publication_ids as $pubId) {
            foreach ($publication_keyword_ids as $pubKeyId) {
                // Create parameterized values for insertion
                $values[] = "(:indices_id_{$totalParams}, :pubKeyId_{$totalParams}, :pubId_{$totalParams}, 0, 0)";
                $params[":indices_id_{$totalParams}"] = $indices_id;
                $params[":pubKeyId_{$totalParams}"] = $pubKeyId;
                $params[":pubId_{$totalParams}"] = $pubId;
                $totalParams++;
                $count++;

                // Execute in batches
                if ($count % $batchSize == 0) {
                    $sql = "INSERT INTO indicies_master_keyword_publication_status
                            (indices_id, publication_keyword_id, publication_id, search_complete, is_word_found)
                            VALUES " . implode(', ', $values) . "
                            ON DUPLICATE KEY UPDATE
                            search_complete = 0,
                            is_word_found = 0";
                    $stmt = $this->dbc->prepare($sql);

                    // echo $this->showquery( $sql, $params );
                    // exit();

                    $stmt->execute($params);
                    $this->dbc->commit();  // Commit the current batch

                    // Clear parameters and values for the next batch
                    $values = [];
                    $params = [];
                    $this->dbc->beginTransaction();  // Start a new transaction for the next batch
                }
            }
        }

        // Check if there's an incomplete batch left to commit
        if (!empty($values)) {
            $sql = "INSERT INTO indicies_master_keyword_publication_status
                    (indices_id, publication_keyword_id, publication_id, search_complete, is_word_found)
                    VALUES " . implode(', ', $values) . "
                    ON DUPLICATE KEY UPDATE
                    search_complete = 0,
                    is_word_found = 0";
            $stmt = $this->dbc->prepare($sql);

            // echo $this->showquery( $sql, $params );
            // exit();

            $stmt->execute($params);

            $this->dbc->commit();  // Commit the last batch
        }

        // items have been staged
        return true;
    }


    /**
     * @return boolean
     */
    function getIsSearchInProgress($indices_id)
    {
        $sql = "SELECT count(*) AS count
                FROM indicies_master_keyword_publication_status
                WHERE indices_id = :indices_id AND search_complete = 0";

        $stmt = $this->dbc->prepare($sql);
        $params = [
            ':indices_id' => $indices_id
        ];

        $stmt->execute($params);
        $response = $stmt->fetch(PDO::FETCH_ASSOC);

        return (int)$response['count'] > 0;
    }

    /**
     * @return boolean
     */
    function updateSearchProgress($indices_id, $publication_keyword_id, $publication_id, $word_count)
    {
        $sql = "UPDATE indicies_master_keyword_publication_status
                SET search_complete = 1,
                is_word_found = :word_count
                WHERE indices_id = :indices_id 
                AND publication_keyword_id = :publication_keyword_id
                AND publication_id = :publication_id
                ";

        $stmt = $this->dbc->prepare($sql);
        $params = [
            ':is_word_found' => $word_count,
            ':indices_id' => $indices_id,
            ':publication_keyword_id' => $publication_keyword_id,
            ':publication_id' => $publication_id
        ];

        $stmt->execute($params);

        return true;
    }

    /**
     * @return value last_checked, publication_id, search_status, and status 
     */
    function masterKeywordSearchStatus($indices_id, $last_checked = null)
    {

        $isSearchInProgress = $this->getIsSearchInprogress($indices_id);

        if (!$isSearchInProgress) {
            return [
                'success' => true,  // Indicate the operation "failed" in the sense that a search is still in progress
                'message' => 'No search in progress',
                'data' => ['last_checked' => $this->getMySQLTime()]
            ];
        }

        // Convert last_checked to server's timezone (Berlin) if necessary

        $last_checked_server_time = is_null($last_checked) ? $this->getMySQLTime() : $last_checked;

        error_log("Indices ID: $indices_id, Last Checked (Mysql Time): $last_checked_server_time");

        $sql = "
            SELECT publication_id
            FROM indicies_master_keyword_publication_status
            WHERE indices_id = :indices_id 
            AND last_updated > :last_updated
            GROUP BY publication_id
            HAVING COUNT(CASE WHEN search_complete = 0 THEN 1 END) = 0";

        $stmt = $this->dbc->prepare($sql);
        $params = [
            ':indices_id' => $indices_id,
            ':last_updated' => $last_checked_server_time
        ];

        $stmt->execute($params);

        $completed_publications = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $completed_publications[] = $row['publication_id'];
        }

        error_log("Completed Publications: " . json_encode($completed_publications));

        if (empty($completed_publications)) {
            return json_encode([]);
        }

        $placeholders = implode(',', array_fill(0, count($completed_publications), '?'));

        $sql = "
            SELECT publication_id, search_complete, is_word_found, last_updated
            FROM indicies_master_keyword_publication_status
            WHERE indices_id = ?
            AND publication_id IN ($placeholders)
            AND last_updated > ?";

        $stmt = $this->dbc->prepare($sql);
        $params = array_merge([$indices_id], $completed_publications, [$last_checked_server_time]);
        $stmt->execute($params);

        $statuses = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $statuses[] = $row;
        }

        error_log("Statuses: " . json_encode($statuses));

        return json_encode($statuses);
    }
}
