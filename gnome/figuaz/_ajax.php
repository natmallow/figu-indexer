<?php

$SECURITY->isLoggedIn();

use gnome\classes\DBConnection as DBConnection;
use gnome\classes\MessageResource as MessageResource;

$dbconfig = require_once(__DIR__ . '/../includes/crystal/settings.config.php');          // Define db configuration arrays here

require_once(__DIR__ . '/../includes/crystal/db.connect.php');             // Include this file

$lang = empty($_GET['lang' ]) ? 'en' : $_GET['lang'];

function update($nameField, $column, $msg, $table)
{
    global $dbconfig;

    $database = new DBConnection();
    # code...
    $sqlUpdate = $database->dbc->prepare(

        "UPDATE $table SET $column = IF ($column, 0, 1) WHERE id_$table= :id;"
    );

    $sqlUpdate->execute([
        ':id'  =>  $_GET['id']
    ]);
    // echo $sqlUpdate->rowCount();
    $sqlUpdate = null;

    $sqlFetch = $database->dbc->prepare("SELECT $nameField FROM $table WHERE id_$table= :id");
    $sqlFetch->execute([':id' =>  $_GET['id']]);

    $fetch = $sqlFetch->fetchAll();

    // $_SESSION['actionResponse'] = sprintf($msg, $fetch[0][0]);
    $sqlFetch = null;
    $arr = array('response' => sprintf($msg, $fetch[0][0]));

    header('Content-Type: application/json');
    echo json_encode($arr);
    exit;
};



// single actions
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (!empty($_GET['id']) && !empty($_GET['action'])) {
        switch ($_GET['action']) {
            case 'remove':
                $repMsg = "The Article<br><strong>%s</strong> - Has been removed!";
                update('title', 'is_deleted', $repMsg, 'articles');
                break;
            case 'togglePublish':
                die('froggs');
                $repMsg = "The Article<br><strong>%s</strong> - Has been published!";
                update('title', 'is_published', $repMsg, 'articles');
                break;
            case 'toggletoSidebar':
                $repMsg = "The Article<br><strong>%s</strong> - Has been added to side bar published!";
                update('title', 'is_on_sidebar', $repMsg, 'articles');
                break;
            case 'toggletoHomepage':
                $repMsg = "The Section <strong>%s</strong> - Has been altered on homepage!";
                update('name', 'is_on_homepage', $repMsg, 'sections');
                break;
        }
    }
}
