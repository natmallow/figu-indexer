<?php

namespace gnome\classes\model;

use gnome\classes\DBConnection as DBConnection;
use gnome\classes\service\PublicationParserService as PublicationParserService;

class Publication extends DBConnection
{

    protected $table = 'publications';

    // function __construct(){
    //     $this->table = 'publications';
    // }


    function getPublications($publicationTypeId)
    {
        $sql = "SELECT publication_id,
            date,
            publication_type_id,
            is_ready,
            title,
            notes
        FROM {$this->table} 
        WHERE publication_type_id = :publication_type_id
        ORDER BY LENGTH(publication_id), publication_id ";

        $pdoc = $this->dbc->prepare($sql);
        $pdoc->execute([':publication_type_id' => $publicationTypeId]);

        $response = $pdoc->fetchAll();

        return $response;
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
            notes,
            publication_source
        FROM {$this->table}  
        WHERE publication_id = :publication_id";

        $pdoc = $this->dbc->prepare($sql);

        $pdoc->execute([':publication_id' => $id]);

        return $pdoc->fetch(\PDO::FETCH_ASSOC);
    }

    function addPublication()
    {

        $insertId = filter_input(INPUT_POST, 'publication_id');
        $insertTypeId = filter_input(INPUT_POST, 'publication_type_id');

        // run check if publication exists
        if (isset($this->getPublication($insertId)["publication_id"])) {
            http_response_code(208);
            return $insertId;
        }

        $publicationTypeInfo = (object) $this->getPublicationType($insertTypeId);
        // check if a valid type was sent in
        if (!isset($publicationTypeInfo->name)) {
            http_response_code(400);
            return $insertId;
        }

        $sql = "INSERT INTO {$this->table}
                    (publication_id,
                    publication_type_id,
                    german,
                    english,
                    english_name,
                    german_name,
                    author,
                    date,
                    raw_html,
                    title,
                    notes,
                    publication_source,
                    is_ready
                    )
                VALUES
                    (:publication_id,
                    :publication_type_id,
                    :german,
                    :english,
                    :english_name,
                    :german_name,
                    :author,
                    :date,
                    :raw_html,
                    :title,
                    :notes,
                    :publication_source,
                    :is_ready
                     );";

        $pdoc = $this->dbc->prepare($sql);

        $arrayBind = [
            ':publication_id' => strtoupper($insertId),
            ':publication_type_id' => $insertTypeId,
            ':german' => (null !== filter_input(INPUT_POST, 'german')) ? filter_input(INPUT_POST, 'german') : '',
            ':english' => (null !== filter_input(INPUT_POST, 'english')) ? filter_input(INPUT_POST, 'english') : '',
            ':english_name' => (null !== filter_input(INPUT_POST, 'english_name')) ? filter_input(INPUT_POST, 'english_name') : '',
            ':german_name' => (null !== filter_input(INPUT_POST, 'german_name')) ? filter_input(INPUT_POST, 'german_name') : '',
            ':author' => (null !== filter_input(INPUT_POST, 'author')) ? filter_input(INPUT_POST, 'author') : '',
            ':date' => (null !== filter_input(INPUT_POST, 'date')) ? filter_input(INPUT_POST, 'date') : date("Y-m-d"),
            ':raw_html' => filter_input(INPUT_POST, 'raw_html'),
            ':title' => (null !== filter_input(INPUT_POST, 'title')) ? filter_input(INPUT_POST, 'title') : '',
            ':notes' => filter_input(INPUT_POST, 'notes'),
            ':publication_source' => filter_input(INPUT_POST, 'publication_source'),
            ':is_ready' => filter_input(INPUT_POST, 'is_ready'),
        ];

        $pdoc->execute($arrayBind);

        return [$insertId, $insertTypeId];
    }

    function makePublication($id)
    {
    }

    function deletePublication($id)
    {
    }

    function updatePublication()
    {

        $isReady  = filter_input(INPUT_POST, 'is_ready');

        $sql = "UPDATE {$this->table} SET 
                    notes = :notes, 
                    publication_source = :publication_source,
                    raw_html = :raw_html, 
                    is_ready = :is_ready,
                    updated_by = :updated_by 
                    WHERE publication_id = :publication_id";

        $pdoc = $this->dbc->prepare($sql);

        $sqlBind = [
            ':notes' => filter_input(INPUT_POST, 'notes'),
            ':publication_source' => filter_input(INPUT_POST, 'publication_source'),
            ':raw_html' => filter_input(INPUT_POST, 'raw_html'),
            //            ':Publication_date' => date("Y-m-d H:i:s", strtotime($post['Publication_date'])),
            ':updated_by' => isset($_SESSION['username']) ? $_SESSION['username'] : 'none',
            ':publication_id' => filter_input(INPUT_POST, 'publication_id'),
            ':is_ready' => filter_input(INPUT_POST, 'is_ready')
        ];


        //       echo $this->showquery($sql, $sqlBind);
        // die();
        $pdoc->execute($sqlBind);

        $insertId = filter_input(INPUT_POST, 'publication_id');

        $pdoc = null;

        // if is ready is set to 1 then we parse the text into there seprate table
        if ($isReady == "1") {
            $parser = new PublicationParserService();
            $parser->parseTextFromTable($insertId);
        }
        
        return $insertId;
    }

    function getPublicationTypes()
    {
        // select the avaible items for drop down
        $sql = "SELECT publication_type_id,
                name,
                abbreviation
                FROM publication_type;";

        $pdoc = $this->dbc->prepare($sql);
        $pdoc->execute();
        return $pdoc->fetchAll();
    }

    /**
     * get publication Type by id
     * @publicationTypeId 
     */
    function getPublicationType($publicationTypeId)
    {
        // select the avaible items for drop down
        $sql = "SELECT 
                publication_type_id,
                name,
                abbreviation
                FROM publication_type
                WHERE publication_type_id = :publication_type_id";

        $pdoc = $this->dbc->prepare($sql);

        $arrayBind = [':publication_type_id' => $publicationTypeId];

        $pdoc->execute($arrayBind);

        return $pdoc->fetch(\PDO::FETCH_ASSOC);
    }

    function addPublicationType($abbr, $pubName)
    {

        $count = $this->getCountPDO('publication_type', ['abbreviation' => $abbr, 'name' => $pubName]);

        if ($count > 0) {
            // Entry already exists
            $_SESSION['actionResponse'] = 'ERROR: A publication type with the same name or abbreviation already exists.';
            return false;
        }

        // select the avaible items for drop down
        $sql = "INSERT INTO publication_type (name, abbreviation) 
                VALUES (:name, :abbreviation)";

        $pdoc = $this->dbc->prepare($sql);

        $arrayBind = [
            ':name' => $abbr,
            ':abbreviation' => $pubName
        ];


        try {
            $pdoc->execute($arrayBind);
            $_SESSION['actionResponse'] = $pubName . ' Has Been Added!';
            return true;
        } catch (\PDOException $e) {
            $this->msg('Error : ' . $e->getMessage());
            $_SESSION['actionResponse'] = 'ERROR' . $pubName . ' Not Has Been Added!';
            return false;
        }
    }


}
