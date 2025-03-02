<?php

namespace gnome\classes\model;

use gnome\classes\DBConnection as DBConnection;

class IndicesLink extends DBConnection
{

    protected $table = 'indices_link';

    /**
     * Returns the name of the associated indexes to the parent
     */

    function getLinkedIndex($indicesGroupId)
    {
        // indices_group_id,
        $sql = "SELECT 
                    I.indices_id,
                    I.name as index_name
                FROM {$this->table} IG INNER JOIN
                    indices I 
                    ON I.indices_id = IG.indices_id 
                WHERE indices_group_id = :indices_id ORDER BY index_name ASC";

        try {
            $pdoc = $this->dbc->prepare($sql);

            $pdoc->execute([':indices_id' => $indicesGroupId]);

            return $pdoc->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            // Handle PDO exception
            error_log($e->getMessage());
        }
    }

    /**
     * Retrieves a list of all indices with their IDs and names, along with a flag indicating whether each index
     * is linked to the specified group. The 'is_linked' flag is 1 if the index is linked to the group identified
     * by the given $currentIndex, and 0 otherwise.
     *
     * @param mixed $currentIndex The ID of the group to check the linkage of indices against.
     * @return array An associative array where each element contains 'indices_id', 'index_name', and 'is_linked' for each index.
     */
    function getLinkedIndices($currentIndex)
    {
        // indices_group_id,
        $sql = "SELECT 
                    I.indices_id,
                    I.name as index_name,  
                    CASE 
                        WHEN (SELECT 
                            indices_id
                            FROM indices_link 
                            WHERE indices_group_id = :indices_id AND indices_id = I.indices_id
                        ) IS NOT NULL THEN 1
                        ELSE 0
                    END AS is_linked
                FROM
            indices I 
            WHERE I.indices_id != :indices_id 
            ORDER BY index_name ASC";

        $pdoc = $this->dbc->prepare($sql);

        $pdoc->execute([':indices_id' => $currentIndex]);

        return $pdoc->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * ajax run creates a link between indexes as a loose association
     */
    function linkIndices($currentIndex, $subIndex)
    {
        try {
            // Remove single quotes around placeholders
            $sql = "INSERT INTO {$this->table} (indices_group_id, indices_id) 
                    VALUES (:indices_group_id, :indices_id)";

            $pdoc = $this->dbc->prepare($sql);

            $arrayBind = [
                ':indices_group_id' =>  $currentIndex,
                ':indices_id' =>  $subIndex
            ];

            $pdoc->execute($arrayBind);
            return true;
        } catch (\PDOException $e) {
            // Handle error
            error_log($e->getMessage());
            // Log the error
            return false;
        }
    }

    /**
     * ajax removes links
     */
    function unlinkIndices($currentIndex, $subIndex)
    {
        try {
            $sql = "DELETE FROM {$this->table} 
                WHERE indices_group_id = :indices_group_id
                AND indices_id = :indices_id";

            $pdoc = $this->dbc->prepare($sql);

            $arrayBind = [
                ':indices_group_id' =>  $currentIndex,
                ':indices_id' =>  $subIndex
            ];

            $pdoc->execute($arrayBind);
            return true;
        } catch (\PDOException $e) {
            // Handle error
            error_log($e->getMessage());
            // Log the error
            return false;
        }
    }
}

// $newID = new IndicesLink();
// echo '<pre>';
// print_r( $newID->getLinkedIndices( 2 ) );
