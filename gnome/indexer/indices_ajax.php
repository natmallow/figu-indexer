<?php

$SECURITY->isLoggedIn();

use gnome\classes\model\Indices;
use gnome\classes\model\Keyword;
use gnome\classes\service\IndicesKeywordService;

$Indices = new Indices();
$IndicesKeywordService = new IndicesKeywordService();
$Keyword = new Keyword();

if ( $_SERVER[ 'REQUEST_METHOD' ] == 'POST' ) {

    if ( filter_input( INPUT_POST, 'action' ) == 'add-option' ) {
        $id = filter_input( INPUT_POST, 'indices_id' );
        $value = filter_input( INPUT_POST, 'optional_field' );
        //return $Indices->addIndexOptionalField( $id, $value );
        echo $Indices->addIndexOptionalField( $id, $value );
        exit();
    } elseif ( filter_input( INPUT_POST, 'action' ) == 'add-meta' ) {
        $id = filter_input( INPUT_POST, 'indices_id' );
        $value = filter_input( INPUT_POST, 'meta' );
        echo $Indices->addMetaField( $id, $value );
        exit();
    } elseif ( filter_input( INPUT_POST, 'action' ) == 'save-permission' ) {
        $indices_id = filter_input( INPUT_POST, 'indices_id' );
        $user_id = filter_input( INPUT_POST, 'user_id' );
        $is_owner = 1;
        $can_read = 1;
        $can_write = 1;
        $can_admin = filter_input( INPUT_POST, 'can_admin' );

        $Indices->saveIndexPermission( $indices_id, $user_id, $is_owner, $can_read, $can_write, $can_admin);
        echo json_encode(
            array(
                'success' => '1'
            )
        );
        exit();

    } elseif ( filter_input( INPUT_POST, 'action' ) == 'remove-permission' ) {
        $indices_id = filter_input( INPUT_POST, 'indices_id' );
        $user_id = filter_input( INPUT_POST, 'user_id' );
        $Indices->removeIndexPermission( $indices_id, $user_id );
        echo json_encode(
            array(
                'success' => '1'
            )
        );
        exit();

    } elseif ( filter_input( INPUT_POST, 'action' ) == 'delete-meta' ) {
        $id = filter_input( INPUT_POST, 'indices_keyword_meta_id' );
        $Indices->deleteMeta( $id );
        echo json_encode(
            array(
                'publication_id'=>"$id",
            )
        );

        exit();
    } elseif ( filter_input( INPUT_POST, 'action' ) == 'edit' ) {
        // return $Indices->updateIndexOptionalField( $id );
    } elseif ( filter_input( INPUT_POST, 'action' ) == 'link-keyword' ) {
                
        $rtnArr = [];

        // remove empty
        $words = array_filter(explode(',', filter_input(INPUT_POST, 'keywords')));

        // filter out the words on ignore list
        $keyWords = $Keyword->checkKeywords($words);

        $indices_id = filter_input(INPUT_POST, 'indices_id');


        for ($i = 0; $i < count($keyWords); $i++) {
            $keyWordId = $IndicesKeywordService->linkKeywordToIndex($indices_id, $keyWords[$i])[0];
            $keyWord = trim($keyWords[$i]);
            array_push($rtnArr, array("id" => "$keyWordId", "value" => "$keyWord", "metas" => []));
        }

        $keywordRtnArr['indices_id'] = $indices_id;
        $keywordRtnArr['keywords'] = $rtnArr;
        $keywordRtnArr['success'] = 1;
        // var_dump($keywordRtnArr);
        echo json_encode($keywordRtnArr);

    } elseif ( filter_input( INPUT_POST, 'action' ) == 'unlink-keyword' ) {
        // return $Indices->updateIndexOptionalField( $id );
        $indicesId = filter_input(INPUT_POST, 'indicesId');
        $keyWordId = filter_input(INPUT_POST, 'keyWordId');

        $IndicesKeywordService->unlinkKeywordToIndex($indicesId, $keyWordId);
        $keywordRtnArr['success'] = 1;
        echo json_encode($keywordRtnArr);

    } elseif ( filter_input( INPUT_POST, 'action' ) == 'delete-index' ) {
        // return $Indices->updateIndexOptionalField( $id );
        $indicesId = filter_input(INPUT_POST, 'indicesId');

        $Indices->deleteMeta( $id );
        
        $keywordRtnArr['success'] = 1;
        echo json_encode($keywordRtnArr);

    } elseif ( filter_input( INPUT_POST, 'action' ) == 'delete-option' ) {
        $id = filter_input( INPUT_POST, 'indices_optional_field_id' );
        $Indices->deleteIndexOptionalField( $id );
        echo $id ;
        exit();
    }
}