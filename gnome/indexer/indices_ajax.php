<?php

$SECURITY->isLoggedIn();

use gnome\classes\model\Indices;
use gnome\classes\model\Keyword;
use gnome\classes\service\IndicesKeywordService;
use gnome\classes\model\AjaxResponseHandler;

$Indices = new Indices();
$Keyword = new Keyword();
$IndicesKeywordService = new IndicesKeywordService();


// Only proceed if we received a POST request
if ( $_SERVER[ 'REQUEST_METHOD' ] == 'POST' ) {

    // 1) Attempt to read JSON input
    $rawJson = file_get_contents('php://input');
    $decodedJson = json_decode($rawJson, true);


    // 2) If JSON is valid, use it; otherwise use $_POST
    if (json_last_error() === JSON_ERROR_NONE && is_array($decodedJson)) {
        $postData = $decodedJson;  // We have valid JSON
    } else {
        $postData = $_POST; // Fallback to form-encoded
    }

    // 3) Extract the shared fields
    // If something doesn't exist in $postData, use null
    $action                     = $postData['action']                    ?? null;
    $indices_id                 = $postData['indices_id']                ?? null;    
    $user_id                    = $postData['user_id']                   ?? null;    
    $can_admin                  = $postData['can_admin']                 ?? null;
    $indicesKeywordMetaId       = $postData['indices_keyword_meta_id']   ?? null;
    $keyWords                   = $postData['keywords']                  ?? null;
    $keywordId                  = $postData['keyword_id']                ?? null;
    $indicesId                  = $postData['indicesId']                 ?? null;
    $indices_optional_field_id  = $postData['indices_optional_field_id'] ?? null;
    $optional_field             = $postData['optional_field']            ?? null;
    $metaValue                  = $postData['meta']                      ?? null;

    // (Optional) For debugging, build an array to output
    $keywordRtnArr = [
        'method'   => $_SERVER['REQUEST_METHOD'],
        'action'   => $action,
        'postData' => $postData,
        'success'  => 0
    ];
    
    // set response type here
    header('Content-Type: application/json; charset=utf-8');

    if ( $action === 'add-option' ) {

        $AjaxHandler = new AjaxResponseHandler($Indices);
        echo $AjaxHandler->runAjax("addIndexOptionalField", [$indices_id, $optional_field])->getResponse();
        exit();

    } elseif ( $action === 'add-meta' ) {

        $AjaxHandler = new AjaxResponseHandler($Indices);
        echo $AjaxHandler->runAjax("addMetaField", [$indices_id, $metaValue])->getResponse();
        exit();

    } elseif ( $action === 'save-permission' ) {

        $AjaxHandler = new AjaxResponseHandler($Indices);
        $is_owner = 1;
        $can_read = 1;
        $can_write = 1;

        // $Indices->saveIndexPermission( $indices_id, $user_id, $is_owner, $can_read, $can_write, $can_admin);
        echo $AjaxHandler->runAjax("saveIndexPermission", [$indices_id, $user_id, $is_owner, $can_read, $can_write, $can_admin])->getResponse();
        exit();

    } elseif ( $action === 'remove-permission' ) {

        $AjaxHandler = new AjaxResponseHandler($Indices);
        echo $AjaxHandler->runAjax("removeIndexPermission", [$indices_id, $user_id])->getResponse();
        exit();

    } elseif ( $action === 'delete-meta' ) {

        $AjaxHandler = new AjaxResponseHandler($Indices);
        $response =  $AjaxHandler->runAjax("deleteMeta", [$indicesKeywordMetaId])->getResponse() ;
        echo $response;
        exit();

    } elseif ( $action === 'edit' ) {
        // return $Indices->updateIndexOptionalField( $id );
    } elseif ( $action === 'link-keyword' ) {
                
        $_rtnArr = [];

        // remove empty
        $_words = array_filter(explode(',', $keyWords));

        // filter out the words on ignore list
        $_keyWords = $Keyword->checkKeywords($_words);

        for ($i = 0; $i < count($_keyWords); $i++) {
            $_keyWordId = $IndicesKeywordService->linkKeywordToIndex($indices_id, $_keyWords[$i])[0];
            $_keyWord = trim($_keyWords[$i]);
            array_push($_rtnArr, array("id" => "$_keyWordId", "value" => "$_keyWord", "metas" => []));
        }

        $keywordRtnArr['indices_id'] = $indices_id;
        $keywordRtnArr['keywords'] = $_rtnArr;
        $keywordRtnArr['success'] = 1;

        echo json_encode($keywordRtnArr);
        exit();

    } elseif ( $action === 'unlink-keyword' ) {
        
        $AjaxHandler = new AjaxResponseHandler($IndicesKeywordService);
        $response =  $AjaxHandler->runAjax("unlinkKeywordToIndex", [$indicesId, $keyWordId])->getResponse(true);
        echo $response;
        exit();

    } elseif ( $action === 'delete-index' ) {

        $AjaxHandler = new AjaxResponseHandler($Indices);
        $response =  $AjaxHandler->runAjax("deleteIndex", [$indicesId])->getResponse() ;
        echo $response;
        exit();

    } elseif ( $action === 'delete-option' ) {

        $AjaxHandler = new AjaxResponseHandler($Indices);
        $response =  $AjaxHandler->runAjax("deleteIndexOptionalField", [$indices_optional_field_id])->getResponse();
        echo $response;
        exit();
    }
}