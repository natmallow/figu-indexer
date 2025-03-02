<?php

$SECURITY->isLoggedIn();

use gnome\classes\model\PublicationIndex as PublicationIndex;
use gnome\classes\model\Publication as Publication;
use gnome\classes\model\Indices as Indices;
use gnome\classes\model\Keyword as Keyword;
use gnome\classes\model\PublicationIndexCache;

$lang = lang();

$PublicationIndex = new PublicationIndex();
$Publication = new Publication();
$Indices = new Indices();
$Keyword = new Keyword();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $input = json_decode(file_get_contents('php://input'), true);

    if (is_null($input)) {
        // Handle form-encoded data
        $action = filter_input(INPUT_POST, 'action');
        $indices_id = filter_input(INPUT_POST, 'indices_id');
        $publication_id = filter_input(INPUT_POST, 'publication_id');
        $publicationIndexId = filter_input(INPUT_POST, 'publication_index_id');
        $publicationKeywordId = filter_input(INPUT_POST, 'publication_keyword_id');
        $indicesKeywordMetaId = filter_input(INPUT_POST, 'indices_keyword_meta_id');
        $newKeyWord = filter_input(INPUT_POST, 'newKeyWord');
        $tracks = fnSortUniqueTracks(filter_input(INPUT_POST, 'tracks'));
        $summary = filter_input(INPUT_POST, 'summary');
        $notes = filter_input(INPUT_POST, 'notes');
        $keyWords = filter_input(INPUT_POST, 'keyWords');
        $publicationStatus = filter_input(INPUT_POST, 'publicationStatus');
        $optionalFieldsArr = filter_input(INPUT_POST, 'optionalFieldsArr');
        $keywordId = filter_input(INPUT_POST, 'keyword_id');
    } else {
        // Handle JSON data
        $action = $input['action'] ?? null;
        $indices_id = $input['indices_id'] ?? null;
        $publication_id = $input['publication_id'] ?? null;
        $publicationIndexId = $input['publication_index_id'] ?? null;
        $publicationKeywordId = $input['publication_keyword_id'] ?? null;
        $indicesKeywordMetaId = $input['indices_keyword_meta_id'] ?? null;
        $newKeyWord = $input['newKeyWord'] ?? null;
        $tracks = fnSortUniqueTracks($input['tracks'] ?? null);
        $summary = $input['summary'] ?? null;
        $notes = $input['notes'] ?? null;
        $keyWords = $input['keyWords'] ?? null;
        $publicationStatus = $input['publicationStatus'] ?? null;
        $optionalFieldsArr = $input['optionalFieldsArr'] ?? null;
        $keywordId = $input['keyword_id'] ?? null;
    }

    if ($action == 'addKeyword') {

        $rtnArr = [];

        $keyWordsCheck = explode(',', $newKeyWord);

        // filter out the words on ignore list
        $keyWords = $Keyword->checkKeywords($keyWordsCheck);

        for ($i = 0; $i < count($keyWords); $i++) {
            $keyWordId = $PublicationIndex->linkSaveKeywords($publicationIndexId, $keyWords[$i])[0];
            $keyWord = trim($keyWords[$i]);
            array_push($rtnArr, array("id" => "$keyWordId", "value" => "$keyWord", "metas" => []));
        }

        $keywordRtnArr['publication_index_id'] = $publicationIndexId;
        $keywordRtnArr['keywords'] = $rtnArr;
        $keywordRtnArr['success'] = 1;
        echo json_encode($keywordRtnArr);
    } elseif ($action == 'removeKeyword') {


        $PublicationIndex->unlinkKeywordAndMeta($publicationIndexId, $keywordId);

        $keywordRtnArr['keyword_id'] = $keywordId;
        $keywordRtnArr['success'] = 1;
        echo json_encode($keywordRtnArr);
    } elseif ($action == 'addKeywordMeta') {

        $PublicationIndex->addKeywordMeta($publicationIndexId, $publicationKeywordId, $indicesKeywordMetaId);
        echo json_encode(
            array(
                "action" => "addMeta",
                "keyword_id" => "$publicationKeywordId",
                "meta_id" => "$indicesKeywordMetaId"
            )
        );
    } elseif ($action == 'removeKeywordMeta') {


        $PublicationIndex->removeKeywordMeta($publicationIndexId, $publicationKeywordId, $indicesKeywordMetaId);
        echo json_encode(
            array(
                "action" => "removeMeta",
                "keyword_id" => "$publicationKeywordId",
                "meta_id" => "$indicesKeywordMetaId"
            )
        );
    } elseif ($action == 'addPublication') {

        list($id, $pub_type) = $Publication->addPublication();
        echo json_encode(
            array(
                "publication_id" => "$id",
                "pub_type" => "$pub_type",
            )
        );
    } elseif ($action == 'update-publication') {
        // Process the data as if it came from JSON
        $publication_index_id = $PublicationIndex->saveIndexPublication($indices_id, $publication_id, $publicationStatus, $notes, $tracks, $summary);
        $PublicationIndex->saveOptionalQuestionsAnswers($optionalFieldsArr);

        // update publication_index_cache
        $PubIndexCache = new PublicationIndexCache();
        $PubIndexCache->savePublicationIndexCache($indices_id, $publication_id);

        echo json_encode(array('success' => 1, 'tracks' => $tracks));
    }

    exit();
}
