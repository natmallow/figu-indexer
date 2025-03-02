<?php

// crontab -l
// MAILTO=""
// SHELL="/bin/bash"
// * * * * * /usr/local/bin/php /home/figuadqo/public_html/gnome/classes/service/cron/IndicesKeywordServiceCron.php


// test local/bin/php
// http://dev.figuarizona.org/gnome/classes/service/cron/IndicesKeywordServiceCron.php


namespace gnome\classes\service\cron;

try {
    // Perform some task
    // Attempt to include the file
    if (!file_exists('/home/figuadqo/public_html/autoload.php')) {
        throw new \Exception("File not found: /home/figuadqo/public_html/autoload.php");
    }
    require_once '/home/figuadqo/public_html/autoload.php';
    echo "Successfully included /home/figuadqo/public_html/autoload.php\n";
} catch (\Exception $ex) {
    // Jump to this part if an exception occurred
    // Log the exception message
    echo "Exception occurred: " . $ex->getMessage() . "\n";

    // Attempt to include the alternative file
    if (!file_exists('C:/xampp/htdocs/figuarizona/autoload.php')) {
        echo "Alternative file not found: C:/xampp/htdocs/figuarizona/autoload.php\n";
    } else {
        require_once 'C:/xampp/htdocs/figuarizona/autoload.php';
        echo "Successfully included C:/xampp/htdocs/figuarizona/autoload.php\n";
    }
}

use gnome\classes\DBConnection;
use gnome\classes\service\PublicationParserService;
use gnome\classes\model\PublicationIndex;
use gnome\classes\model\PublicationIndexCache;
use gnome\classes\service\Chat;
use gnome\classes\service\IndicesKeywordService;

class IndicesKeywordServiceCron extends DBConnection
{
    private $logFile;
    private $lockFile; // Path to the lock file

    private $needsWorkKey = 'Review needed'; //review needed
    private $finishedNoRef = 'Finished no ref found';

    function __construct()
    {
        ignore_user_abort(true);
        set_time_limit(0);

        $environment = $_SERVER['HTTP_ENVIRONMENT'] ?? 'prod';
        if ($environment == 'dev') {
            $this->logFile = '/xampp/htdocs/figuarizona/gnome/classes/service/cron/logfile.txt';
            $this->lockFile = '/xampp/htdocs/figuarizona/gnome/classes/service/cron/lockfile';
        } else {
            $this->logFile = '/home/figuadqo/public_html/gnome/classes/service/cron/logfile.txt';
            $this->lockFile = '/home/figuadqo/public_html/gnome/classes/service/cron/lockfile';
        }

        parent::__construct();

        $this->checkAndSetLock();
    }

    private function checkAndSetLock()
    {
        if (file_exists($this->lockFile)) {
            // Lock file exists, so another instance is running
            echo "Another instance is already running.\n";
            exit;
        }

        // Create a lock file to signal that the process is running
        file_put_contents($this->lockFile, "locked");
    }

    function __destruct()
    {
        // Ensure the lock file is removed when the script finishes
        if (file_exists($this->lockFile)) {
            unlink($this->lockFile);
        }
    }

    public function updateLog($indices_id, $publication_id, $statusKey)
    {
        $currentDateTime = date('Y-m-d H:i:s');
        $logData = [
            'indices_id' => $indices_id,
            'publication_id' => $publication_id,
            'statusKey' => $statusKey,
            'timestamp' => $currentDateTime,
        ];
        $logMessage = json_encode($logData) . "\n";

        if (file_exists($this->logFile) && filesize($this->logFile) > 5 * 1024 * 1024) {
            unlink($this->logFile);
        }

        // $chat = Chat::getInstance();
        // $chat->broadcastMessage($logMessage);

        file_put_contents($this->logFile, $logMessage, FILE_APPEND);
    }

    public function getChunkFromSearchStatus()
    {
        while (true) {  // Infinite loop, will run until there are no more results
            $results = $this->fetchSearchStatusChunks();
            if (empty($results)) {
                echo "No more results to process.\n";
                break; // Exit loop if no more results
            }

            $uniquePublicationIds = $this->extractUniquePublicationIds($results);
            $this->processPublications($uniquePublicationIds);
            $this->searchAndUpdateKeywords($results);

            // Optionally, you can implement a mechanism to sleep for a few seconds if needed
            // to reduce CPU usage in a continuously running loop:
            sleep(1);  // Sleep for 1 second before next iteration
        }
    }

    private function fetchSearchStatusChunks()
    {
        $sql = "SELECT im.*, pk.keyword, pi.publication_index_id
                FROM indicies_master_keyword_publication_status im 
                JOIN publication_keyword pk ON im.publication_keyword_id = pk.publication_keyword_id
                JOIN publication_index pi ON im.publication_id = pi.publication_id AND im.indices_id = pi.indices_id
                WHERE im.search_complete = 0 
                LIMIT 100"; // Limit the number of records to fetch at a time

        $stmt = $this->dbc->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    private function extractUniquePublicationIds($results)
    {
        return array_unique(array_column($results, 'publication_id'));
    }

    private function processPublications($publicationIds)
    {
        foreach ($publicationIds as $publication_id) {
            $sql = "SELECT EXISTS(SELECT 1 FROM publication_text_content WHERE publication_id = :publication_id) AS publication_exists";
            $stmt = $this->dbc->prepare($sql);
            $stmt->bindParam(':publication_id', $publication_id, \PDO::PARAM_STR);

            if ($stmt->execute()) {
                $result = $stmt->fetch(\PDO::FETCH_ASSOC);

                if ($result && !$result['publication_exists']) {
                    $publicationParserService = new PublicationParserService();
                    $publicationParserService->parseTextFromTable($publication_id);
                }
            } else {
                // Handle query execution error
                error_log('Query execution failed: ' . implode(' ', $stmt->errorInfo()));
            }
        }
    }

    private function searchAndUpdateKeywords($results)
    {
        foreach ($results as $row) {
            $count = $this->processKeywordSearch($row);
            $this->updateSearchStatus($row, $count);
            $this->updatePublicationStatus($row['publication_id'], $row['indices_id']);

            // If the keyword is found, update the publication_indices_keyword_meta_link table
            if ($count > 0) {
                $this->updateKeywordMetaLink($row);
            }
        }
    }

    private function processKeywordSearch($row)
    {
        $keyword = $row['keyword'];
        $publication_id = $row['publication_id'];

        // Add double quotes around the keyword for exact match in full-text search
        $keywordExactMatch = '"' . $keyword . '"';

        // Use word boundaries with REGEXP
        $termLike = '\\b' . $keyword . '\\b';  // Use word boundaries with REGEXP

        $sql = "SELECT COUNT(*) AS count 
                FROM publication_text_content 
                WHERE (MATCH(english_text, german_text) AGAINST(:keywordExactMatch IN BOOLEAN MODE) OR
                      english_text REGEXP :termLike OR 
                      german_text REGEXP :termLike) AND
                      publication_id = :publication_id";

        $stmt = $this->dbc->prepare($sql);
        $stmt->bindParam(':keywordExactMatch', $keywordExactMatch);
        $stmt->bindValue(':termLike', $termLike);
        $stmt->bindParam(':publication_id', $publication_id, \PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchColumn();
    }

    private function updateSearchStatus($row, $count)
    {
        $sql = "UPDATE indicies_master_keyword_publication_status 
                SET search_complete = 1, is_word_found = :is_word_found 
                WHERE indices_id = :indices_id AND
                      publication_keyword_id = :publication_keyword_id AND
                      publication_id = :publication_id";

        $stmt = $this->dbc->prepare($sql);
        $stmt->bindParam(':is_word_found', $count, \PDO::PARAM_INT);
        $stmt->bindParam(':indices_id', $row['indices_id'], \PDO::PARAM_INT);
        $stmt->bindParam(':publication_keyword_id', $row['publication_keyword_id'], \PDO::PARAM_INT);
        $stmt->bindParam(':publication_id', $row['publication_id'], \PDO::PARAM_STR);
        $stmt->execute();
    }

    private function updateKeywordMetaLink($row)
    {
        if (!isset($row['publication_index_id'])) {
            error_log('publication_index_id is missing for keyword ' . $row['keyword']);
            return;
        }

        $sql = "INSERT INTO publication_indices_keyword_meta_link (publication_index_id, publication_keyword_id, indices_keyword_meta_id) 
                VALUES (:publication_index_id, :publication_keyword_id, 0)
                ON DUPLICATE KEY UPDATE publication_index_id = VALUES(publication_index_id)";

        $stmt = $this->dbc->prepare($sql);
        $stmt->bindParam(':publication_index_id', $row['publication_index_id'], \PDO::PARAM_INT);
        $stmt->bindParam(':publication_keyword_id', $row['publication_keyword_id'], \PDO::PARAM_INT);
        $stmt->execute();
    }

    private function updatePublicationStatus($publication_id, $indices_id)
    {
        $sql = "SELECT COUNT(*) AS has_keyword
                FROM indicies_master_keyword_publication_status
                WHERE is_word_found > 0 AND
                      publication_id = :publication_id AND
                      indices_id = :indices_id";

        $stmt = $this->dbc->prepare($sql);
        $stmt->bindParam(':publication_id', $publication_id, \PDO::PARAM_STR);
        $stmt->bindParam(':indices_id', $indices_id, \PDO::PARAM_INT);
        $stmt->execute();
        $has_keyword = $stmt->fetchColumn();

        $publicationIndex = new PublicationIndex();
        $statusKey = $has_keyword > 0 ? $this->needsWorkKey : $this->finishedNoRef;
        $publicationIndex->updateIndexPublicationStatus($indices_id, $publication_id, $statusKey);
        $this->updateLog($indices_id, $publication_id, $statusKey);
    }

    function setUpSearchFromQueue()
    {
        $sql = "SELECT * FROM publication_keyword_search_queue WHERE search_started_on IS NULL AND search_completed_on IS NULL LIMIT 1";
        $stmt = $this->dbc->prepare($sql);
        $stmt->execute();
        $queueItem = $stmt->fetch(\PDO::FETCH_ASSOC);
        // file_put_contents('background_task_log.txt', "Immediate response sent at: " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);

       error_log(json_encode($queueItem) . "\n");
       error_log("setUpSearchFromQueue response sent at: ------------ " . date('Y-m-d H:i:s') . "\n");

        if ($queueItem) {
            $queue_id = $queueItem['id'];
            $indices_id = $queueItem['indices_id'];
            $publication_ids = json_decode($queueItem['publication_ids'], true);

            // set up search in 
            $IndicesKeywordService = new IndicesKeywordService();            
            $IndicesKeywordService->masterKeywordSearch($indices_id, $publication_ids);

            // Update the search_started_on to indicate processing has started
            $sql = "UPDATE publication_keyword_search_queue SET search_started_on = NOW() WHERE id = :queue_id";
            $stmt = $this->dbc->prepare($sql);
            $stmt->bindParam(':queue_id', $queue_id);
            $stmt->execute();

            $PublicationIndex = new PublicationIndex();
            
            $publication_ids_cnt = count($publication_ids);
        
            for ($i = 0; $i < $publication_ids_cnt; $i++ ) {
                
                // Process the data as if it came from JSON
                $publication_index_id = $PublicationIndex->addMissingIndexPublication($indices_id, $publication_ids[$i]);
                error_log("inside the loop setUpSearchFromQueue response sent at: " . date('Y-m-d H:i:s') . " publication_index_id => ".$publication_index_id."\n");
                // update publication_index_cache
                $PubIndexCache = new PublicationIndexCache();
                $PubIndexCache->savePublicationIndexCache($indices_id, $publication_ids[$i]);
            }
            

            // Mark the search as completed
            $sql = "UPDATE publication_keyword_search_queue SET search_completed_on = NOW() WHERE id = :queue_id";
            $stmt = $this->dbc->prepare($sql);
            $stmt->bindParam(':queue_id', $queue_id);
            $stmt->execute();
        }

        return $this;
    }
}

$indicesKeywordServiceCron = new IndicesKeywordServiceCron();
$indicesKeywordServiceCron->setUpSearchFromQueue()->getChunkFromSearchStatus();
