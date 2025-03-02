<?php

$SECURITY->isLoggedIn();

use gnome\classes\model\AjaxResponseHandler;
use gnome\classes\model\Indices;
use gnome\classes\model\Keyword;
use gnome\classes\model\PublicationIndex;
use gnome\classes\service\IndicesKeywordService;
use gnome\classes\model\PublicationIndexCache;
// for testing
use gnome\classes\service\cron\IndicesKeywordServiceCron;

$PublicationIndex = new PublicationIndex();
$IndicesKeywordService = new IndicesKeywordService();

$environment = $_SERVER['HTTP_ENVIRONMENT'] ?? 'prod';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Step 1: Get the raw POST data from php://input
    $rawData = file_get_contents("php://input");
    // Step 2: Decode the JSON data into an associative array
    $data = json_decode($rawData, true);


    if ($data && isset($data['action'])) {
        if ($data['action'] == "table-data") {
            $indices_id = filter_var($data['indices_id'] ?? null, FILTER_VALIDATE_INT);
            $pub_type = htmlspecialchars($data['pub_type'] ?? '');
            $AjaxHandler = new AjaxResponseHandler($PublicationIndex);
            echo $AjaxHandler->run("getIndexPublications", [$indices_id, $pub_type]);
            exit();
        } elseif ($data['action'] == "get-master-keywords") {
            $indices_id = filter_var($data['indices_id'] ?? null, FILTER_VALIDATE_INT);
            $AjaxHandler = new AjaxResponseHandler($IndicesKeywordService);
            echo $AjaxHandler->run("getIndicesMasterKeywords", [$indices_id]);
            exit();
        } elseif ($data['action'] == "run-master-keyword-search-sp") {
            // searches can only be run on a single index source.
            $indices_id = filter_var($data['indices_id'] ?? null, FILTER_VALIDATE_INT);
            $publication_ids = $data['publication_ids'];
            $AjaxHandler = new AjaxResponseHandler($IndicesKeywordService);
            echo $AjaxHandler->run("masterKeywordSearch", [$indices_id, $publication_ids]);
            exit();
        } elseif ($data['action'] == "run-master-keyword-search-status") {
            // searches can only be run on a single index source.
            $indices_id = filter_var($data['indices_id'] ?? null, FILTER_VALIDATE_INT);
            $last_checked = $data['last_checked'];

            $AjaxHandler = new AjaxResponseHandler($IndicesKeywordService);
            echo $AjaxHandler->run("masterKeywordSearchStatus", [$indices_id, $last_checked]);
            exit();
        } 
        
        elseif ($data['action'] == "run-master-keyword-search") {
            // Add the indices_id and publication_ids variables
            $indices_id = filter_var($data['indices_id'] ?? null, FILTER_VALIDATE_INT);
            $publication_ids = json_encode($data['publication_ids']); 

            // Send an immediate response to the client
            $AjaxHandler = new AjaxResponseHandler($IndicesKeywordService);
           
            echo $AjaxHandler->run("queMasterKeywordSearch", [$indices_id, $publication_ids]);
            // 
            if (ob_get_length()) {
                ob_flush();
                flush();
            }
            
// return;


            // Log immediate response
            file_put_contents('background_task_log.txt', "Immediate response sent at: " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
        
            // // Continue with the long-running process in the background
            // $publication_ids_cnt = count($publication_ids);
        
            // for ($i = 0; $i < $publication_ids_cnt; $i++ ) {
            //     // Process the data as if it came from JSON
            //     $publication_index_id = $PublicationIndex->addMissingIndexPublication($indices_id, $publication_ids[$i]);
                
            //     // update publication_index_cache
            //     $PubIndexCache = new PublicationIndexCache();
            //     $PubIndexCache->savePublicationIndexCache($indices_id, $publication_ids[$i]);
            // }
        
// $AjaxHandler->run("masterKeywordSearch", [$indices_id, $publication_ids]);
        
            // For Windows, consider logging the command's initiation and follow up manually for the process status.
            if ($environment == 'dev') {
                $command = 'start /B C:\xampp\php\php.exe C:\xampp\htdocs\figuarizona\gnome\classes\service\cron\IndicesKeywordServiceCron.php > C:\xampp\htdocs\figuarizona\gnome\classes\service\cron\output.txt 2>&1';
                exec($command, $output, $return_var);
                $pid = 'N/A'; // Windows does not easily provide a PID here
            } else {
                $command = 'nohup php /var/www/html/public_html/gnome/classes/service/cron\IndicesKeywordServiceCron.php > /dev/null 2>&1 & echo $!';
                exec($command, $output, $return_var);
                $pid = $output[0] ?? 'N/A'; // This should correctly capture the PID on Unix
            }
        }
        
    }
}
