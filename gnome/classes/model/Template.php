<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace gnome\classes\model;

use gnome\classes\DBConnection as DBConnection;
use gnome\classes\Paginate as Paginate;

/**
 * Description of template
 *
 * @author PeaceParty
 */
class Template extends Paginate {

    
        
    private $db = null;
    public $total_pages;
    
    public $table = 'email_template';
    public $pageTitle = 'Mailing Templates';
    
    public $email_template_id = '';
    public $name = '';
    public $description_html = '';
    public $create_date = '';
    public $updated_by = ''; 
    public $created_by = '';


    public $tableCols = [
        'email_template_id',
        'name',
        'description_html',
        'create_date',
        'updated_by',  
        'created_by'
    ];

    //put your code here
    public function __construct() {
        $this->db = DBConnection::instance();
    }

    public function save($isDelete = false) {

        if (filter_input(INPUT_POST, 'action') == 'edit') {
            $this->update();
        } elseif (filter_input(INPUT_POST, 'action') == 'add') {
            $this->add();      
        }
    }

    public function add() {
        
        $query = "INSERT INTO $this->table 
        (   
            name,
            description_html,
            updated_by,  
            created_by
        )
        VALUES
        (
            :name, 
            :description_html, 
            :updated_by,
            :created_by
        );";

        $pdoc = $this->db->dbc->prepare($query);

        $bind = [
            ':name' => $_POST['name'],
            ':description_html' => $_POST['description_html'],
            // ':create_date' => date("Y-m-d H:i:s", strtotime($_POST['event_date'])),
            ':updated_by' => $_SESSION['username'],
            ':created_by' => $_SESSION['username']
        ];

echo  $this->db->showquery($query, $bind); 

        $pdoc->execute($bind);

        $insertId = $this->db->lastInsertId();

        $_SESSION['actionResponse'] = $_POST['name'] . ' Has Been Created';
        header("Location: ./templates.php?lang=$lang");
        exit();
    }

    public function update() {

        $query = "UPDATE $this->table SET 
                    name = :name, 
                    description_html = :description_html, 
                    updated_by = :updated_by 
                    WHERE email_template_id = :email_template_id";

        $pdoc = $this->db->dbc->prepare($query);

        $bind = [
            ':name' => $_POST['name'],
            ':description_html' => $_POST['description_html'],
            ':updated_by' => $_SESSION['username'],
            ':email_template_id' => $_POST['email_template_id']
        ];

        $pdoc->execute($bind);

        $_SESSION['actionResponse'] = 'Edit Complete!';
        header("Location: ./templates.php?lang=$lang");
        exit();
    }

    public function delete() {
        // deletes
    }

    public function get($id = null) {
        $query = "SELECT 
            `email_template_id`,
            `name`,
            `description_html`,
            `created_date`,
            `updated_by`,
            `created_by`
         FROM `figu-az`.`email_template`;
         WHERE email_template_id = :email_template_id;";

        $pdoc = $this->db->dbc->prepare($query);

        $pdoc->execute([':email_template_id' => filter_input(INPUT_GET, 'id')]);

        return $pdoc->fetchAll();
    }

    public function getAll() {
        
        $lang = lang();
        $page = page();
        $limit = (int) limit();
        $starting_limit = ($page-1)*$limit;
        
        $query = "SELECT count(*) FROM email_template";

        $s = $this->db->dbc->query($query);
        $total_results = $s->fetchColumn();
        $this->total_pages = floor($total_results / $limit);
        $startLimit = (int) ($page - 1) * $limit;

        $sql = "SELECT email_template_id, name 
                FROM $this->table 
                ORDER BY name DESC  
                LIMIT $startLimit, $limit ";

        $pdoc = $this->db->dbc->prepare($sql);

        $pdoc->execute();

        return $pdoc->fetchAll();
    }

}