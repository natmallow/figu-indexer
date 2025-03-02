<?php

namespace gnome\classes\model;

use gnome\classes\DBConnection as DBConnection;
use PDO;

use gnome\classes\model\Keyword;

class PublicationIndex extends DBConnection
{

    private $table = 'publication_index';

    protected $KeyWords;

    function __construct()
    {
        parent::__construct();
        $this->KeyWords = new Keyword();
    }


    /**
     * Decontaminates text by removing unwanted characters and ensuring proper encoding.
     *
     * @param string $text Text to be decontaminated.
     * @param bool $remove_tags Remove HTML tags.
     * @param bool $remove_line_breaks Remove line breaks and excess whitespace.
     * @param bool $remove_BOM Remove Byte Order Mark (BOM) if present.
     * @param bool $ensure_utf8_encoding Ensure text is UTF-8 encoded.
     * @param bool $ensure_quotes_are_properly_displayed Ensure quotes are displayed correctly.
     * @param bool $decode_html_entities Decode HTML entities.
     * @return string Decontaminated text.
     */
    function decontaminate_text(
        $text,
        $remove_tags = true,
        $remove_line_breaks = true,
        $remove_BOM = true,
        $ensure_utf8_encoding = true,
        $ensure_quotes_are_properly_displayed = true,
        $decode_html_entities = true
    ) {
        if ('' === $text || !is_string($text)) {
            return $text;
        }

        // Remove <script> and <style> tags entirely
        $text = preg_replace('@<(script|style)[^>]*?>.*?</\\1>@si', '', $text);
        $text = str_replace(']]>', ']]&gt;', $text);

        if ($remove_tags) {
            $text = strip_tags($text);
        }

        if ($remove_line_breaks) {
            $text = preg_replace('/[\r\n\t ]+/', ' ', $text);
            $text = trim($text);
        }

        if ($remove_BOM) {
            if (0 === strpos(bin2hex($text), 'efbbbf')) {
                $text = substr($text, 3);
            }
        }

        if ($ensure_utf8_encoding) {
            if (mb_detect_encoding($text, 'UTF-8', true) !== 'UTF-8') {
                $text = mb_convert_encoding($text, 'UTF-8');
            }
        }

        if ($ensure_quotes_are_properly_displayed) {
            $text = str_replace('&quot;', '"', $text);
        }

        if ($decode_html_entities) {
            $text = html_entity_decode($text);
        }

        return $text;
    }

    
    function getIndexPublication($index_id, $publication_id)
    {

        /**
         * work around for the following error Error Code: 1140.
         * In aggregated query without GROUP BY,
         * expression #1 of SELECT list contains nonaggregated
         * column 'singlestring.publication_index_status';
         *  this is incompatible with sql_mode = only_full_group_by
         */
        $sql = "SET sql_mode = ''";
        $stmt = $this->dbc->prepare($sql);
        $stmt->execute();

        /**
         * concat defaults to 1049kb this sets it to the max packet size allowed which is 250mb
         */
        $sql = "SET SESSION group_concat_max_len  = @@max_allowed_packet;";
        $stmt = $this->dbc->prepare($sql);
        $stmt->execute();


        // JSON_REMOVE( keyword, '$.metas[0]' ) AS kwords
        // JSON_REMOVE( keyword, CONCAT( '$.metas[', json_length( keyword->'$.metas' )-1, ']' ) ) AS kwords
        $sql = "SELECT 
            publication_index_status,
            publication_index_id,
            summary,
            notes,
            tracks,
            CONCAT('[', GROUP_CONCAT(keyword), ']') AS keywords
         FROM
            (SELECT 
                publication_index_status,
                publication_index_id,
                summary,
                notes,
                tracks,
                CONCAT('{\"id\":\"',keyword_id,'\",
                    \"locked\":', IF(m_publication_keyword_id IS NOT NULL, 'true', 'false'), ', 
                \"value\":\"',keyword, '\", \"metas\":[',GROUP_CONCAT( meta ),']}') AS keyword
            FROM
                (SELECT 
                    PI.publication_index_status AS publication_index_status,
                    PI.publication_index_id AS publication_index_id,
                    PI.summary AS summary,
                    PI.notes AS notes,
                    PI.tracks AS tracks,
                    PK.keyword AS keyword,
                    PK.publication_keyword_id AS keyword_id,
                    IMLKL.publication_keyword_id AS m_publication_keyword_id,
                    JSON_OBJECT('value',IKM.meta,'id',IKM.indices_keyword_meta_id) AS meta
                FROM
                    publication_index PI
                LEFT JOIN publication_indices_keyword_meta_link PML ON PI.publication_index_id = PML.publication_index_id
                LEFT JOIN publication_keyword PK ON PML.publication_keyword_id = PK.publication_keyword_id 
                LEFT JOIN indices_keyword_meta IKM ON PML.indices_keyword_meta_id = IKM.indices_keyword_meta_id
                LEFT JOIN indices_master_list_keyword_link IMLKL ON PK.publication_keyword_id = IMLKL.publication_keyword_id AND IMLKL.indices_id = PI.indices_id
                WHERE
                    PI.publication_id = :publication_id
                        AND PI.indices_id = :indices_id
                        ) keysMeta
                GROUP BY keyword
                ) 
            formatjson;";

        $stmt = $this->dbc->prepare($sql);

        $params = [':publication_id' => $publication_id, ':indices_id' => $index_id];
        // echo $this->showquery( $sql, $params );
        // exit();
        $stmt->execute($params);

        $rtnObj =  $stmt->fetch(PDO::FETCH_ASSOC);

        //  echo '<pre>';
        $jsonObj = [];
        $jsonObj = isset($rtnObj['keywords']) ? json_decode($rtnObj['keywords']) : [];

        //  print_r( $rtnObj['keywords'] );
        // $this->decontaminate_text($rtnObj['keywords']);
        // echo "<pre>";
        //  print_r( $rtnObj['keywords'] );
        //  $decoded_text = json_decode( $rtnObj['keywords'], true );

        //  echo json_last_error_msg() . ' - ' . json_last_error();

        //  die();


        for ($i = 0; $i < count($jsonObj); $i++) {

            for ($n = 0; $n < count($jsonObj[$i]->metas); $n++) {
                if (is_null($jsonObj[$i]->metas[$n]->id)) {
                    array_splice($jsonObj[$i]->metas, $n, 1);
                }
            }
        }

        $rtnObj['keywords'] = $jsonObj;
        //     echo '<pre>';
        // var_dump( $rtnObj  );
        // echo '</pre>';

        // die();
        return $rtnObj;
    }


    function getIndexPublications($id, $pub_type = null)
    {
        $pubInsert = is_null($pub_type) ? "" : "WHERE P.publication_type_id = :pub_type";

        // Corrected SQL query
        $sql = "SELECT P.publication_id, P.is_ready,
                IFNULL(
                    (
                        SELECT PUI.publication_index_status 
                        FROM publication_index AS PUI 
                        WHERE PUI.publication_id = P.publication_id
                        AND PUI.indices_id = :id
                    ), 'Not Started'
                ) AS indexing_status,
                CASE
                    WHEN EXISTS (
                        SELECT 1
                        FROM indicies_master_keyword_publication_status AS PSS 
                        WHERE PSS.publication_id = P.publication_id
                        AND PSS.indices_id = :id
                        AND PSS.search_complete = 0
                    ) THEN 'inprogress'
                    WHEN NOT EXISTS (
                        SELECT 1
                        FROM indicies_master_keyword_publication_status AS PSS 
                        WHERE PSS.publication_id = P.publication_id
                        AND PSS.indices_id = :id
                    ) THEN 'N/A'
                    ELSE 'completed'
                END AS keyword_search_status,
                GROUP_CONCAT(
                    CASE
                        WHEN PK.keyword IS NOT NULL THEN PK.keyword
                    END
                    ORDER BY PK.keyword SEPARATOR ', '
                ) AS keywords_found 
                FROM publications AS P
                LEFT JOIN indicies_master_keyword_publication_status AS PSS ON P.publication_id = PSS.publication_id AND PSS.indices_id = :id
                LEFT JOIN publication_keyword AS PK ON PK.publication_keyword_id = PSS.publication_keyword_id AND PSS.is_word_found = 1
                $pubInsert
                GROUP BY P.publication_id, P.is_ready
                ORDER BY LENGTH(P.publication_id), P.publication_id";

        $stmt = $this->dbc->prepare($sql);

        $params = [':id' => $id];

        if (!is_null($pub_type)) {
            $params[':pub_type'] = $pub_type;
        }

        $stmt->execute($params);

        $response = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $response;
    }


    function getIndexKeywordMeta($index_id)
    {
        $sql = 'SELECT * FROM indices_keyword_meta WHERE indices_id = :indices_id';

        $stmt = $this->dbc->prepare($sql);

        $params = [':indices_id' => $index_id];

        $stmt->execute($params);

        return $stmt->fetchAll();
    }


    function addKeywordMeta($publication_index_id, $publication_keyword_id, $indices_keyword_meta_id)
    {

        $sql = "INSERT INTO publication_indices_keyword_meta_link 
                (publication_index_id,
                publication_keyword_id,
                indices_keyword_meta_id)
                VALUES
                (:publication_index_id,
                :publication_keyword_id,
                :indices_keyword_meta_id)
                ";

        $params = [
            ':publication_index_id' => $publication_index_id,
            ':publication_keyword_id' => $publication_keyword_id,
            ':indices_keyword_meta_id' => $indices_keyword_meta_id
        ];

        $stmt = $this->dbc->prepare($sql);

        $stmt->execute($params);
    }


    function removeKeywordMeta($publication_index_id, $publication_keyword_id, $indices_keyword_meta_id)
    {

        $sql = "DELETE FROM publication_indices_keyword_meta_link
                WHERE publication_index_id = :publication_index_id AND
                publication_keyword_id = :publication_keyword_id AND
                indices_keyword_meta_id = :indices_keyword_meta_id
            ";

        $params = [
            ':publication_index_id' => $publication_index_id,
            ':publication_keyword_id' => $publication_keyword_id,
            ':indices_keyword_meta_id' => $indices_keyword_meta_id
        ];

        $stmt = $this->dbc->prepare($sql);
        //  echo $this->showquery( $sql, $params );
        //  exit();
        $stmt->execute($params);
    }


    //gets a single publication with the associated index
    function getIndexPublicationStatus($index_id, $publication_id)
    {
        $sql = "SELECT 
	            IFNULL(
                    (SELECT PIS.publication_index_status 	
	                 FROM publication_index AS PIS 
                     WHERE PIS.indices_id = :index_id 
                     AND PIS.publication_id = P.publication_id ),'Not Started') AS indexing_status
                FROM publications AS P WHERE publication_id = :publication_id";

        $stmt = $this->dbc->prepare($sql);

        $params = [':publication_id' => $publication_id, ':index_id' => $index_id];

        $stmt->execute($params);

        return $stmt->fetch();
    }


    function getPublicationStatusLookup()
    {

        $sql = 'SELECT * FROM publication_status_lookup';

        $stmt = $this->dbc->prepare($sql);

        $stmt->execute();

        return $stmt->fetchAll();
    }


    // saveStatus( $index_id, $publication_id, $indexing_status )
    function saveIndexPublication($indices_id, $publication_id, $publication_index_status, $notes, $tracks, $summary)
    {
        $sql = "INSERT INTO publication_index (indices_id, publication_id, publication_index_status, notes, tracks, summary) 
                VALUES (:indices_id, :publication_id, :publication_index_status, :notes, :tracks, :summary) 
                ON DUPLICATE KEY UPDATE
                publication_index_status = :publication_index_status,
                notes = :notes,
                tracks = :tracks,
                summary = :summary,
                publication_index_id=LAST_INSERT_ID(publication_index_id)
                ";

        $params = [
            ':indices_id' => $indices_id,
            ':publication_id' => $publication_id,
            ':publication_index_status' => $publication_index_status,
            ':notes' => $notes,
            ':tracks' => $tracks,
            ':summary' => $summary
        ];

        $stmt = $this->dbc->prepare($sql);
        // echo $this->showquery( $sql, $params );
        // exit();
        $stmt->execute($params);

        return $this->dbc->lastInsertId();
    }


    function addMissingIndexPublication($indices_id, $publication_id, $publication_index_status = 'Not Started', $notes='', $tracks='', $summary='')
    {
        try {
            // First, check if the record exists
            $checkSql = "SELECT 1 FROM publication_index WHERE indices_id = :indices_id AND publication_id = :publication_id";

            $checkStmt = $this->dbc->prepare($checkSql);
            $params = [':indices_id' => $indices_id, ':publication_id' => $publication_id];
            $checkStmt->execute($params);
            
            error_log("addMissingIndexPublication => ". $this->showquery( $checkSql, $params ). "\n");            
      // echo $this->showquery( $sql, $params );
        // exit();


            if (!$checkStmt->fetch()) {
                // If record does not exist, insert it
                $insertSql = "INSERT INTO publication_index (indices_id, publication_id, publication_index_status, notes, tracks, summary) 
                              VALUES (:indices_id, :publication_id, :publication_index_status, :notes, :tracks, :summary)";
    
                $insertStmt = $this->dbc->prepare($insertSql);
                $insertStmt->execute([
                    ':indices_id' => $indices_id,
                    ':publication_id' => $publication_id,
                    ':publication_index_status' => $publication_index_status,
                    ':notes' => $notes,
                    ':tracks' => $tracks,
                    ':summary' => $summary
                ]);
    
                return $this->dbc->lastInsertId();
            }

        } catch (\PDOException $e) {
            error_log('Error executing saveIndexPublication: ' . $e->getMessage());
            return null;
        }
    }


    function saveOptionalQuestionsAnswers($optionalFieldsArr)
    {

        if ($optionalFieldsArr == '') return;

        $fields = json_decode($optionalFieldsArr);

        $sql = "INSERT INTO publication_indices_optional_field_link (publication_id, indices_optional_field_id, optional_field_value)
                VALUES (:publication_id, :indices_optional_field_id, :optional_field_value)
                ON DUPLICATE KEY UPDATE
                optional_field_value = :optional_field_value
                ";
        $stmt = $this->dbc->prepare($sql);

        foreach ($fields as $name => $value) {
            $params = [
                ':publication_id' => $value->publication_id,
                ':indices_optional_field_id' => $value->indices_optional_field_id,
                ':optional_field_value' => $value->optional_field_value
            ];
            $stmt->execute($params);
        }
        // exit();
        // $publication_id,
        // $indices_optional_field_id,
        // $optional_field_value
    }


    function updateIndexPublicationStatus($indices_id, $publication_id, $publication_index_status)
    {
        $sql = "UPDATE publication_index SET 
                publication_index_status = :publication_index_status
                WHERE publication_id = :publication_id 
                AND indices_id = :indices_id";
    
        $params = [
            ':publication_id' => $publication_id,
            ':indices_id' => $indices_id,
            ':publication_index_status' => $publication_index_status
        ];
    
        $stmt = $this->dbc->prepare($sql);
        // echo $this->showquery( $sql, $params );
        // exit();
        $stmt->execute($params);
    
        return $this->dbc->lastInsertId();
    }


    function linkSaveKeywords($publication_index_id, $keyWordsStr)
    {
        // add to keywords table
        $wordLinks = $this->KeyWords->saveKeywordsGetIds($keyWordsStr);
        // add new links
        $this->addKeywordLinks($publication_index_id, $wordLinks);

        return $wordLinks;
    }


    private function addKeywordLinks($publication_index_id, $wordLinks = [])
    {
        $sql = "INSERT INTO publication_indices_keyword_meta_link (publication_index_id, publication_keyword_id)
                VALUES (:publication_index_id, :publication_keyword_id)";
        $stmt = $this->dbc->prepare($sql);
        foreach ($wordLinks as $value) {
            $params = [
                ':publication_index_id' => $publication_index_id,
                ':publication_keyword_id' => $value,
            ];
            $stmt->execute($params);
        }
    }


    /**
     * called from ajax
     * unlinks keword and the keywords meta from publication
     */
    function unlinkKeywordAndMeta($publication_index_id, $publication_keyword_id)
    {

        $params = [
            ':publication_index_id' => $publication_index_id,
            ':publication_keyword_id' => $publication_keyword_id
        ];

        $sql = "DELETE FROM publication_indices_keyword_meta_link WHERE 
                publication_index_id = :publication_index_id AND 
                publication_keyword_id = :publication_keyword_id 
                ";
        $stmt = $this->dbc->prepare($sql);

        $stmt->execute($params);
    }


    function getPublication($id)
    {
        $sql = "SELECT publication_id,
            german,
            english,
            english_name,
            german_name,
            author,
            date,
            raw_html,
            publication_type_id,
            is_ready,
            title,
            notes
        FROM {$this->table}  
        WHERE publication_id = :publication_id";

        $stmt = $this->dbc->prepare($sql);

        $stmt->execute([':publication_id' => $id]);

        return $stmt->fetchAll();
    }


    function makePublication($id)
    {
    }


    function deletePublication($id)
    {
    }


    function updatePublication()
    {

        $sql = "UPDATE {$this->table} SET 
                    notes = :notes, 
                    raw_html = :raw_html, 
                    updated_by = :updated_by 
                    WHERE publication_id = :publication_id";

        $stmt = $this->dbc->prepare($sql);

        $sqlBind = [
            ':notes' => filter_input(INPUT_POST, 'notes'),
            ':raw_html' => filter_input(INPUT_POST, 'raw_html'),
            //            ':Publication_date' => date( 'Y-m-d H:i:s', strtotime( $post[ 'Publication_date' ] ) ),
            ':updated_by' => isset($_SESSION['username']) ? $_SESSION['username'] : 'none',
            ':publication_id' => filter_input(INPUT_POST, 'publication_id')
        ];

        //       echo $this->showquery( $sql, $sqlBind );
        // die();
        $stmt->execute($sqlBind);

        $insertId = filter_input(INPUT_POST, 'publication_id');

        $stmt = null;

        return $insertId;
    }
}
