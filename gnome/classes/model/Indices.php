<?php

namespace gnome\classes\model;

use gnome\classes\DBConnection as DBConnection;
use gnome\classes\Paginator as Paginator; 
use gnome\classes\model\User as User;


class Indices extends DBConnection {

    protected $table = 'indices';

    function getIndices() {

        $condition  =  "";
        $count = $this->getCount();

        $sql = "SELECT indices_id,
                name,
                description_html,
                highlight_color,
                text_color,
                created_by,
                CONCAT(U.name_first, ' ', U.name_last) as ownerName
                FROM {$this->table} I INNER JOIN
                user U on I.created_by = U.username 
                WHERE 1 ".$condition." ORDER BY name ASC";
       

       $pages = new Paginator($count,1);

       // $sql .= $this->paginate();
        $limit  = ' LIMIT '.$pages->limit_start.','.$pages->limit_end;
        $response = $this->getQuery($sql.$limit)->fetchAll();
        
        return [$response, $pages];

    }

    function fetchIndices() {

        $condition  =  "";

        $sql = "SELECT indices_id,
                name,
                created_by,
                CONCAT(U.name_first, ' ', U.name_last) as ownerName
                FROM {$this->table} I INNER JOIN
                user U on I.created_by = U.username 
                WHERE 1 ".$condition." ORDER BY name ASC";
       
        $response = $this->getQuery($sql)->fetchAll();
        
        return $response;

    }

    function getIndex($id) {
        $sql = "SELECT indices_id,
                name,
                description_html,
                highlight_color,
                text_color
                FROM {$this->table}  
                WHERE indices_id = :indices_id";

        $pdoc = $this->dbc->prepare($sql);

        $pdoc->execute([':indices_id' => $id]);

        return $pdoc->fetchAll()[0];
    }

    function getIndexColumns($id, $col='name' ) {
        $sql = "SELECT $col         
                FROM {$this->table}  
                WHERE indices_id = :indices_id";

        $pdoc = $this->dbc->prepare($sql);

        $pdoc->execute([':indices_id' => $id]);

        return $pdoc->fetchAll()[0];
    }

    function getMetaFields($indices_id) {
        $sql = "SELECT * FROM indices_keyword_meta 
                WHERE is_deleted=0 AND indices_id = :indices_id";

        $pdoc = $this->dbc->prepare($sql);

        $pdoc->execute([':indices_id' => $indices_id]);

        return $pdoc->fetchAll();
    }

    function getOptionalFields($id) {
        $sql = "SELECT * FROM indices_optional_field 
                WHERE is_deleted=0 AND indices_id = :indices_id";

        $pdoc = $this->dbc->prepare($sql);

        $pdoc->execute([':indices_id' => $id]);

        return $pdoc->fetchAll();
    }

    function getOptionalFieldsWithAnswer($id, $publication_id) {

        $sql = "SELECT distinct
                    I.indices_optional_field_id,        
                    I.optional_field,
                    IFNULL((SELECT PIO.optional_field_value FROM publication_indices_optional_field_link AS PIO 
                    WHERE PIO.indices_optional_field_id=I.indices_optional_field_id 
                    AND publication_id = :publication_id ), '0') as optional_field_value
                FROM indices_optional_field I 
                LEFT JOIN publication_indices_optional_field_link PL 
                ON PL.indices_optional_field_id=I.indices_optional_field_id 
                WHERE I.is_deleted = 0 AND I.indices_id = :indices_id";

        $pdoc = $this->dbc->prepare($sql);

        $arrayBind = [
            ':indices_id' => $id, 
            ':publication_id' => $publication_id
        ];

        //  echo $this->showquery($sql, $arrayBind );
        //  die();


        $pdoc->execute($arrayBind);



        return $pdoc->fetchAll();
    }
    
    function getIndexLinks($id) {

        $sql = "SELECT pui.publication_index_id,
                    pui.publication_id,
                    pui.indices_id,
                    pui.index_group,
                    pui.source,
                    pui.source_location,
                    pui.source_catagory,
                    pui.conditions_addressed,
                    pui.causes,
                    pui.symptoms,
                    pui.treatment,
                    pui.summary,
                    pui.notes,
                    pui.groupings
                FROM publication_index pui 
                WHERE pui.indices_id = :indices_id";


        $pdoc = $this->dbc->prepare($sql);

        $pdoc->execute([':indices_id' => $id]);

        return $pdoc->fetchAll();
    }
    
    function addIndex() {

        $sql = "INSERT INTO {$this->table}
                    ( name,
                    description_html,
                    highlight_color,
                    text_color,
                    created_by)
                VALUES
                    (:name,
                    :description_html,
                    :highlight_color,
                    :text_color,
                    :created_by )";    
                  

                $pdoc = $this->dbc->prepare($sql);

                $arrayBind = [
                    ':name' => filter_input(INPUT_POST, 'name'),
                    ':description_html' => (null !== filter_input(INPUT_POST, 'description_html'))? filter_input(INPUT_POST, 'description_html'): '',
                    ':highlight_color' => (null !== filter_input(INPUT_POST, 'highlight_color'))? filter_input(INPUT_POST, 'highlight_color'): '',
                    ':text_color' => (null !== filter_input(INPUT_POST, 'text_color'))? filter_input(INPUT_POST, 'text_color'): '' ,             
                    ':created_by' => $_SESSION['username']
                ];


                try {
                    $pdoc->execute($arrayBind);
                    $this->msg('A new index has been added!');
                    $indicesId = $this->dbc->lastInsertId();
                    $User = new User;
                    $userId = $User->getUserId($_SESSION['username']);
                    // $this->addIndexPermission($indicesId, $userId, 1);
                    $this->addIndexUser($indicesId, $userId, 1);
                    return $indicesId;                  
                } catch (\PDOException $e) {
                    die(3333333);
                   echo $this->msg('Error :'.$e->getMessage());
                }

    }


    function saveIndexPermission($indicesId, $userId, $isOwner=0, $canRead=1, $canWrite=1, $canAdmin=0) {

        $sql = "INSERT INTO indices_permission
        ( 
            indices_id,
            user_id, 
            is_owner, 
            can_read, 
            can_write,
            can_admin
        ) VALUES (
            :indices_id,
            :user_id, 
            :is_owner, 
            :can_read, 
            :can_write,
            :can_admin
        ) ON DUPLICATE KEY UPDATE 
           can_read = :can_read, 
           can_write = :can_write,
           can_admin = :can_admin
        ";

        $pdoc = $this->dbc->prepare($sql);

        $arrayBind = [
            ':indices_id' => $indicesId,
            ':user_id' => $userId,
            ':is_owner' => $isOwner,
            ':can_read' => $canRead,
            ':can_write' => $canWrite,
            ':can_admin' => $canAdmin          
        ];

        //  echo $this->showquery($sql, $arrayBind );
        //  die();

        $pdoc->execute($arrayBind);
    }

    // DELETE PERMISSIONS
    function removeIndexPermission($indicesId, $userId) {
        $sql = " DELETE FROM indices_permission 
                 WHERE indices_id = :indices_id 
                 AND user_id = :user_id";

        $pdoc = $this->dbc->prepare($sql);

        $arrayBind = [
            ':indices_id' => $indicesId, 
            ':user_id' => $userId
        ];

        $pdoc->execute($arrayBind);
    }

    // PERMISSIONS ADMIN
    function getIndexAdminUsers($indicesId) {
        $sql = " SELECT IP.indices_id, 
                    U.*,
                    IP.is_owner,
                    IP.can_admin,
                    IP.can_read,
                    IP.can_write
                FROM indices_permission AS IP
                INNER JOIN user AS U ON IP.user_id=U.user_id 
                WHERE 
                    can_admin = '1' AND
                    indices_id = :indices_id 
                ";

        $pdoc = $this->dbc->prepare($sql);

        $pdoc->execute([':indices_id' => $indicesId]);

        return $pdoc->fetchAll();
    }

    // PERMISSIONS
    function getIndexUsers($indicesId) {
        $sql = " SELECT IP.indices_id, 
                    U.*,
                    IP.is_owner,
                    IP.can_read,
                    IP.can_write
                FROM indices_permission AS IP
                INNER JOIN user AS U ON IP.user_id=U.user_id 
                WHERE can_admin = '0' 
                AND indices_id = :indices_id ";

        $pdoc = $this->dbc->prepare($sql);

        $pdoc->execute([':indices_id' => $indicesId]);

        return $pdoc->fetchAll();
    }

    // PERMISSIONS ADD
    function addIndexUser($indicesId, $userId, $is_owner = 0) {
        $sql = " INSERT INTO indices_permission (
                    indices_id, 
                    user_id,
                    is_owner
                ) VALUES (
                    :indices_id ,
                    :user_id ,
                    :is_owner
                )";
 
        $pdoc = $this->dbc->prepare($sql);

        $arrayBind = [
            ':indices_id' => $indicesId, 
            ':user_id' => $userId,
            ':is_owner' => $is_owner
        ];

        $pdoc->execute($arrayBind);

        return 'success';
        // return $pdoc->fetchAll();
    }

    // Gets the users full name 
    function getIndexOwner($indicesId) {
        $sql = " SELECT CONCAT(U.name_first, ' ', U.name_last) as ownerName, 
                        U.user_id as ownerId,
                        U.username as userName
                 FROM user U 
                 INNER JOIN indices I on I.created_by = U.username
                 WHERE indices_id = :indices_id
                ";

        $pdoc = $this->dbc->prepare($sql);

        $pdoc->execute([':indices_id' => $indicesId]);

        return $pdoc->fetch();
    } 

    // LIST USER THAT DO NOT HAVE PERMISSION
    function getAvailableUsers($indicesId) {
        $sql = " SELECT U.* FROM user U 
                 WHERE user_id NOT IN (
                    SELECT user_id FROM indices_permission WHERE indices_id = :indices_id
                )";

        $pdoc = $this->dbc->prepare($sql);

        $pdoc->execute([':indices_id' => $indicesId]);

        return $pdoc->fetchAll();
    }    

    function canUserAccess( $indicesId, $username ) {
        $sql = " SELECT count(*) AS count, IP.* 
            FROM indices_permission AS IP
            INNER JOIN user AS U ON IP.user_id = U.user_id 
            WHERE IP.indices_id = :indices_id 
            AND ( U.username = :username )";
            //removed -- OR IP.is_owner = '1'

        $pdoc = $this->dbc->prepare($sql);

        $paramArr = [
            ':indices_id' => $indicesId,
            ':username' => $username
        ];
        //  echo $this->showquery($sql, $paramArr );
        //  die();
        $pdoc->execute($paramArr);

        return $pdoc->fetch();
    }

    function updateIndexOptionalField($indices_id, $optional_field) {
        $sql = "UPDATE indices_optional_field SET 
                optional_field = :value
                WHERE indices_id = :indices_id";

        $pdoc = $this->dbc->prepare($sql);

        $arrayBind = [
            ':indices_id' => $indices_id,
            ':optional_field' => (null !== filter_input(INPUT_POST, 'optional_field'))? filter_input(INPUT_POST, 'optional_field'): '',            
        ];

        $pdoc->execute($arrayBind);
//                 echo $this->showquery($sql, $arrayBind );
    }

    function deleteMeta($indices_keyword_meta_id) {
        $sql = "UPDATE indices_keyword_meta SET 
                is_deleted = '1'
                WHERE indices_keyword_meta_id = :indices_keyword_meta_id";

        $pdoc = $this->dbc->prepare($sql);

        $arrayBind = [
            ':indices_keyword_meta_id' => $indices_keyword_meta_id         
        ];
        // echo $this->showquery($sql, $arrayBind );
        
        $pdoc->execute($arrayBind);
    }

    function deleteIndexOptionalField($indices_optional_field_id) {
        $sql = "UPDATE indices_optional_field SET 
                is_deleted = '1'
                WHERE indices_optional_field_id = :indices_optional_field_id";

        $pdoc = $this->dbc->prepare($sql);

        $arrayBind = [
            ':indices_optional_field_id' => $indices_optional_field_id         
        ];
        // echo $this->showquery($sql, $arrayBind );
        
        $pdoc->execute($arrayBind);
//                
    }

    function addMetaField($indices_id, $value) {

        $sql = "INSERT INTO indices_keyword_meta
                    ( 
                    indices_id,
                    meta
                   )
                VALUES
                    (:indices_id,
                    :meta);";

                $pdoc = $this->dbc->prepare($sql);
                
                $pdoc->bindValue(':indices_id', $indices_id);
                $pdoc->bindValue(':meta', $value);
                $pdoc->execute();

                return $this->dbc->lastInsertId();
    }

    function addIndexOptionalField($indices_id, $value) {

        $sql = "INSERT INTO indices_optional_field
                    ( 
                    indices_id,
                    optional_field
                   )
                VALUES
                    (:id,
                    :booleanQuestion);";

                $pdoc = $this->dbc->prepare($sql);
                
                $pdoc->bindValue(':id', $indices_id);
                $pdoc->bindValue(':booleanQuestion', $value);
                $pdoc->execute();

                return $this->dbc->lastInsertId();
    }

    function makeIndex($id) {
        
    }

    function deleteIndex($id) {
        
    }

    function updateIndex() {

        $sql = "UPDATE {$this->table} SET 
                    name = :name, 
                    description_html = :description_html, 
                    highlight_color = :highlight_color, 
                    text_color = :text_color 
                    WHERE indices_id = :indices_id";
        
        $pdoc = $this->dbc->prepare($sql);

        $bindArr = [
            ':name' => filter_input(INPUT_POST, 'name'),
            ':description_html' => filter_input(INPUT_POST, 'description_html'),
            ':highlight_color' => filter_input(INPUT_POST, 'highlight_color'),
            ':text_color' => filter_input(INPUT_POST, 'text_color'),
            ':indices_id' => filter_input(INPUT_POST, 'indices_id')
        ];

        try {
            $pdoc->execute($bindArr);
            $this->msg(filter_input(INPUT_POST, 'name'). ' has been updated');
            return filter_input(INPUT_POST, 'indices_id');
        } catch (\PDOException $e) {
            $this->msg('Error : '.$e->getMessage());
        }

    }

    function getIndicesWithkeywords($keywords) {

        $keywords = explode(',', $keywords);

        for($i = 0; $i<count($keywords); $i) {
            trim($keywords[$i]);
        }

        $keywords = implode(', ', $keywords);
        
        $sql = " SELECT PK.publication_keyword_id, PK.keyword FROM publication_keyword PK WHERE PK.keyword IN ($keywords) ";

        $pdoc = $this->dbc->prepare($sql);

        $pdoc->execute([':indices_id' => $indicesId]);

        return $pdoc->fetchAll();

    }
}

?>