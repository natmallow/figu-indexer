<?php

$SECURITY->isLoggedIn();

use gnome\classes\model\AjaxResponseHandler;
use gnome\classes\model\Indices;
use gnome\classes\model\Keyword;
use gnome\classes\model\PublicationIndex;
use gnome\classes\service\IndicesKeywordService;

$PublicationIndex = new PublicationIndex();
$IndicesKeywordService = new IndicesKeywordService();



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
        } elseif ($data['action'] == "run-master-keyword-search") {
            // searches can only be run on a single index source.
            // step 1: check to see if there are any  
            $indices_id = filter_var($data['indices_id'] ?? null, FILTER_VALIDATE_INT);
            $publication_ids = filter_var($data['publication_ids'] ?? null, FILTER_REQUIRE_ARRAY);
            // check to see if there is already a search in progress
// var_dump($publication_ids);
            
            // $AjaxHandler = new AjaxResponseHandler($IndicesKeywordService);
            // echo $AjaxHandler->run("masterKeywordSearch", [$indices_id, $publication_ids]);
            // exit();
        }elseif ($data['action'] == "run-master-keyword-search-sp") {
            header('Content-Type: text/plain');
            // test
            $stopper = 0;
            while ($stopper < 5) {
                $stopper ++;
                echo json_encode(['stopper' => $stopper]). "\n";;
                ob_flush();
                flush();
                // $response = ['status' => 'success', 'message' => $message ?: 'Operation successful.', 'data' => $data];
                sleep(1);
            }
            exit();
        }
    }
}
