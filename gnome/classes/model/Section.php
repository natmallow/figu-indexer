<?php

namespace gnome\classes\model;

use gnome\classes\DBConnection as DBConnection;
use gnome\classes\model\interface\Factory as Factory;

class Section extends DBConnection implements Factory {

    public static function factory () {
        $object = get_called_class();
        return new $object();        
    }

    function getSection( $id_sections, $lang = 'en' ) {
        $sql = "SELECT s.*, sb.image_description, sb.description, sb.summary 
                FROM sections s
                RIGHT JOIN sections_body sb ON (s.id_sections = sb.id_sections AND sb.language = :lang) 
                WHERE s.id_sections = :id_sections AND s.is_active = 1";

        $pdoc = $this->dbc->prepare( $sql );

        $pdoc->execute( [ ':id_sections' => $id_sections, ':lang' => $lang ] );

        return $pdoc->fetch();
    }

    function getSectionAndBody( $id_sections, $lang = 'en' ) {
        $sql = "SELECT s.*, s.id_sections AS primary_id_sections, sb.* 
                FROM sections s 
                LEFT JOIN sections_body sb ON (s.id_sections = sb.id_sections AND sb.language = :lang)
                WHERE s.id_sections= :id";
  
        $pdoc = $this->dbc->prepare($sql);
        $pdoc->execute([':lang' => $lang, ':id' => $_GET['id']]);
        return $pdoc->fetch();        
    }

    function getSections($lang = 'en') {
        $sql = "SELECT * FROM sections 
                WHERE is_active = 1 
                ORDER BY -sort_value DESC";
        return $this->getQuery( $sql );
    }

    function getSectionsAndBody($lang = 'en') {
        $sql = "SELECT s.*, sb.name AS top_name  
                FROM sections s 
                LEFT JOIN sections_body sb ON (s.id_sections = sb.id_sections AND sb.language = :lang);";
  
        $pdoc = $this->dbc->prepare($sql);
        
        $arrayBind = [':lang' => $lang];

        $pdoc->execute($arrayBind);
        
//  echo $this->showquery($sql, $arrayBind);
//  die();

        return $pdoc->fetchAll();
    }

    function getSectionsWithChildArticles() {
        $idArticles =  (!empty($_GET['id'])) ? $_GET['id'] : '';

        $sql = "SELECT DISTINCT s.name, s.id_sections,
            (SELECT IFNULL( 
                (SELECT 1 
                    FROM articles a 
                    INNER JOIN link_articles_sections l ON a.id_articles = l.id_articles  
                    WHERE l.id_sections = s.id_sections AND l.id_articles = :id) , 0
                )
            ) AS is_selected  
            FROM sections s LEFT JOIN
            link_articles_sections l on l.id_sections = s.id_sections;";
    
        $pdoc = $this->dbc->prepare($sql);

        $arrayBind = [':id' =>  $idArticles];

        $pdoc->execute($arrayBind);

        return $pdoc->fetchAll();
    }

    function updateSection($lang = 'en') {
        
        $sql = "UPDATE sections SET 
                is_active = :is_active, 
                image = :image,
                id_parent = :id_parent
                WHERE id_sections = :id_sections";

        $pdoc = $this->dbc->prepare($sql);

        $pdoc->execute([
            ':is_active' =>  isset($_POST['is_active']) ? 1 : 0,
            ':image' =>  $_POST['image'],
            ':id_parent' =>  $_POST['id_parent'],
            ':id_sections'  =>  $_POST['id_sections']
        ]);

        $sql = "INSERT INTO sections_body(
                id_sections, 
                language, 
                name, 
                description, 
                summary, 
                image_description
            ) VALUES 
            (:id_sections, :language, :name, :description, :summary, :image_description) 
            ON DUPLICATE KEY UPDATE 
                name = :name,
                description = :description,
                summary = :summary,
                image_description = :image_description";

        $pdoc = $this->dbc->prepare($sql);

        $pdoc->execute([
            ':name' =>  $_POST['name'],
            ':language' =>  $_POST['lang'],
            ':description' =>  $_POST['description'],
            ':summary' =>  $_POST['summary'],
            ':image_description' =>  $_POST['image_description'],
            ':id_sections'  =>  $_POST['id_sections']
        ]);

    }

    function addSection(){
        $sql = "INSERT INTO sections
        (
            name,
            is_active,
            id_parent,
            image
        ) VALUES (
            :name,
            :is_active,
            :id_parent,
            :image)";

        $pdoc = $this->dbc->prepare($sql);

        $pdoc->execute([
            ':name' =>  $_POST['name'],
            ':is_active' =>  isset($_POST['is_active']) ? 1 : 0,
            ':id_parent' =>  $_POST['id_parent'],
            ':image' =>  $_POST['image']
        ]);

        $sql = "INSERT INTO sections_body(
            id_sections, 
            language, 
            name, 
            description, 
            summary, 
            image_description) 
        VALUES 
           (LAST_INSERT_ID(), :language, :name, :description, :summary, :image_description) 
        ON DUPLICATE KEY UPDATE 
            name = :name,
            description = :description,
            summary = :summary,
            image_description = :image_description;";

        $pdoc = $this->dbc->prepare($sql);

        $pdoc->execute([
            ':language' =>  $_POST['lang'],
            ':name' =>  $_POST['name'],
            ':description' =>  $_POST['description'],
            ':summary' =>  $_POST['summary'],
            ':image_description' =>  $_POST['image_description']
        ]);

    }
}

?>