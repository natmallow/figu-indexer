<?php
namespace gnome\classes\model;

use gnome\classes\DBConnection as DBConnection;
use gnome\classes\model\interface\Factory as Factory;

class Article extends DBConnection implements Factory {

    public static function factory () {
        $object = get_called_class();
        return new $object();        
    }

    function getArticles($id_sections, $lang = 'en') {
        $sql = "SELECT a.title, a.id_articles, a.is_external_only, a.link_external
                FROM articles a 
                INNER JOIN link_articles_sections l ON a.id_articles = l.id_articles	
                RIGHT JOIN sections_body sb ON (l.id_sections = sb.id_sections AND sb.language = :lang)
                WHERE l.id_sections = :id_sections 
                AND a.is_published = '1' 
                AND a.is_deleted = '0'";

        $pdoc = $this->dbc->prepare($sql);

        $pdoc->execute([':id_sections' => $id_sections, ':lang' => $lang]);

        return $pdoc->fetchAll();
    }

    function getSectionArticles($lang = 'en') {
        $sql = "SELECT a.*, ab.title as top_title, IFNULL(s.name, 'Unassigned') as name  FROM articles AS a 
                LEFT JOIN link_articles_sections AS l ON a.id_articles = l.id_articles 
                LEFT JOIN sections AS s ON l.id_sections  = s.id_sections 
                LEFT JOIN articles_body AS ab ON (a.id_articles = ab.id_articles AND ab.language = :lang) 
                WHERE a.is_deleted = '0' 
                ORDER BY s.id_sections";

        $pdoc = $this->dbc->prepare($sql);

        $pdoc->execute([':lang' => $lang]);

        return $pdoc->fetchAll();
    }

    function getSideBarArticles($lang = 'en') {
        $sql = "SELECT ab.title, a.image, a.id_articles, a.is_external_only, a.link_external
                FROM articles AS a RIGHT JOIN articles_body AS ab ON  a.id_articles = ab.id_articles
                WHERE is_published = 1 
                AND is_on_sidebar = 1 
                AND is_deleted = '0'
                AND ab.language = :lang";

        $pdoc = $this->dbc->prepare($sql);

        $pdoc->execute([':lang' => $lang]);

        return $pdoc->fetchAll();
    }

    function getArticleFull($id_articles, $lang = 'en'){
        $sql = "SELECT a.*, a.id_articles AS id_articles_primary, a.title AS primary_title, 
                DATE_FORMAT(a.original_publication_date, '%Y-%m-%dT%H:%i') AS original_publication_date, 
                ab.*, ab.title AS top_title FROM articles AS a 
                LEFT JOIN articles_body AS ab ON (a.id_articles = ab.id_articles AND ab.language = :lang) 
                WHERE a.is_deleted = '0' 
                AND a.id_articles = :id_articles;";
    
    
        $pdoc = $this->dbc->prepare($sql);
    
        $pdoc->execute([':lang' => $lang, ':id_articles' =>  $id_articles]);
    
        return  $pdoc->fetchAll();
    

    }

    function getArticle($id_articles, $lang = 'en') {
        $sql = "SELECT a.*, ab.* FROM articles a 
		        RIGHT JOIN articles_body ab ON (a.id_articles = ab.id_articles AND ab.language = :lang ) 
		        WHERE a.id_articles = :id_articles AND is_published = '1' AND is_deleted = '0';";

        $pdoc = $this->dbc->prepare($sql);

        $pdoc->execute([':id_articles' => $id_articles, ':lang' => $lang]);

        return $pdoc->fetch();
    }
    
    function updateLinkTable( $insertId, $sections) {

        // remove all links
        $sql = "DELETE FROM link_articles_sections WHERE id_articles = :insertId;";
        $pdoc = $this->dbc->prepare($sql);
        $pdoc->execute([':insertId' => $insertId]);


        // link articles to sections
        if (!empty($_POST['sections'])) {

            // add all 
            $sql = "INSERT INTO link_articles_sections
                    (id_sections, id_articles)
                    VALUES (:id_sections, :id_articles)";

            $pdoc = $this->dbc->prepare($sql);

            foreach ($_POST['sections'] as $k) {
                $pdoc->execute([':id_sections' => $k, ':id_articles' => $insertId]);
            }

        }
    }

    function updateArticleAndBody($lang) {
        $sql = "UPDATE articles SET ";

        $sql .= (isset($_POST['primary_title'])) ? " title = :title, " : "";

        $sql .= "image = :image, 
                 is_published = :is_published, 
                 is_external_only = :is_external_only,
                 link_external = :link_external, 
                 author = :author, 
                 original_publication_date = :original_publication_date, 
                 updated_by = :updated_by 
                 WHERE id_articles= :id";

        $pdoc = $this->dbc->prepare($sql);

        $arrayBind = [
            ':image' =>  $_POST['image'],
            ':is_published' =>  isset($_POST['is_published']) ? 1 : 0,
            ':is_external_only' =>  isset($_POST['is_external_only']) ? 1 : 0,
            ':link_external' =>  $_POST['link_external'],
            ':author' =>  $_POST['author'],
            ':original_publication_date' =>  date("Y-m-d H:i:s", strtotime($_POST['original_publication_date'])),
            ':updated_by' =>  $_SESSION['username'],
            ':id'  =>  $_POST['id_articles']
        ];

        if (isset($_POST['primary_title'])) {
            $arrayBind[':title'] = $_POST['title'];
        }

//         echo $this->showquery($sql, $arrayBind);
//    die();

        $pdoc->execute($arrayBind);

        // insert or update body

        $sql = "INSERT INTO articles_body (
                id_articles, language, 
                title, content_html, 
                summary, image_description,
                link_download_internal
            ) VALUES (
                :id_articles, :language, 
                :title, :content_html, 
                :summary, :image_description,
                :link_download_internal
            ) ON DUPLICATE KEY UPDATE 
                title = :title,
                content_html = :content_html,
                summary = :summary,
                image_description = :image_description,
                link_download_internal = :link_download_internal; ";

        $pdoc = $this->dbc->prepare($sql);

        $arrayBind = [
            ':title' => $_POST['title'],
            ':content_html' =>  $_POST['content_html'],
            ':summary' =>  $_POST['summary'],
            ':image_description' =>  $_POST['image_description'],
            ':link_download_internal' =>  $_POST['link_download_internal'],
            ':id_articles'  =>  $_POST['id_articles'],
            ':language' => $lang
        ];

        $pdoc->execute($arrayBind);
//         echo $this->showquery($sql, $arrayBind);
//    die();
    }

    function addArticleAndBody($lang) {
        $sql = "INSERT INTO articles
        (
            `title`, 
            `image`, `is_published`,
            `is_external_only`,
            `link_external`, `author`, `original_publication_date`, 
            `updated_by`, `created_by`
        )
        VALUES
        (
            :title, 
            :image, :is_published,        
            :is_external_only,
            :link_external, :author, :original_publication_date,
            :updated_by, :created_by
        );";

        $pdoc = $this->dbc->prepare($sql);

        $arrayBind = [
            ':title' =>  $_POST['title'],
            ':image' =>  $_POST['image'],
            ':is_published' =>  isset($_POST['is_published']) ? 1 : 0,
            ':is_external_only' =>  isset($_POST['is_external_only']) ? 1 : 0,
            ':link_external' =>  $_POST['link_external'],
            ':author' =>  $_POST['author'],
            ':original_publication_date' =>  date("Y-m-d H:i:s", strtotime($_POST['original_publication_date'])),
            ':updated_by' =>  $_SESSION['username'],
            ':created_by' =>  $_SESSION['username']
        ];

        $pdoc->execute($arrayBind);

        $insertId = $this->lastInsertId();

        $sql = "INSERT INTO articles_body (
            id_articles, language, 
            title, content_html, 
            summary, image_description,
            link_download_internal
        ) VALUES (
            :id_articles, :language, 
            :title, :content_html, 
            :summary, :image_description,
            :link_download_internal
        ) ON DUPLICATE KEY UPDATE 
            title = :title,
            content_html = :content_html,
            summary = :summary,
            image_description = :image_description,
            link_download_internal = :link_download_internal; ";

        $pdoc = $this->dbc->prepare($sql);

        $arrayBind = [
            ':title' => $_POST['title'],
            ':content_html' =>  $_POST['content_html'],
            ':summary' =>  $_POST['summary'],
            ':image_description' =>  $_POST['image_description'],
            ':id'  =>  $insertId,
            ':language' => $lang,
            ':link_download_internal' =>  $_POST['link_download_internal']
        ];

        $pdoc->execute($arrayBind);
    }
}

?>