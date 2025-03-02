<?php

namespace gnome\model;

use gnome\classes\DBConnection as DBConnection;

class Event extends DBConnection {

    function getEvents($lang = 'en') {
        
    }

    function getEvent($id) {
        $sql = "SELECT 
        `email_template_id`,
        `name`,
        `description_html`,
        `created_date`,
        `updated_by`,
        `created_by`
         FROM `figu-az`.`event`;
         WHERE email_template_id = :email_template_id;";

        $pdoc = $this->dbc->prepare($sql);

        $pdoc->execute([':email_template_id' => filter_input(INPUT_GET, 'id')]);

        return $pdoc->fetchAll();
    }

    function createEvent($isMakeTemplate = false) {
        
    }

    function makeEventTemplate($id) {
        
    }

    function deleteEvent($id) {
        
    }

    function updateEvent($post, $isMakeTemplate = false) {

        $sql = "UPDATE event SET 
                    name = :name, 
                    description_html = :description_html, 
                    updated_by = :updated_by 
                    WHERE email_template_id = :id";
        /** @disregard [OPTIONAL CODE] [OPTIONAL DESCRIPTION] */
        $pdoc = $database->dbc->prepare($sql);

        $sqlBind = [
            ':name' => $post['name'],
            ':description_html' => $post['description_html'],
//            ':event_date' => date("Y-m-d H:i:s", strtotime($post['event_date'])),
            ':updated_by' => $_SESSION['username'],
            ':id' => $post['event_id']
        ];

        $pdoc->execute($sqlBind);
    }

}

?>