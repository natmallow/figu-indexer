<?php

$SECURITY->isLoggedIn();
use gnome\classes\DBConnection as DBConnection;
use gnome\classes\MessageResource as MessageResource;
$database = new DBConnection();

$lang = empty($_GET['lang' ]) ? 'en' : $_GET['lang'];

function update(DBConnection $db, $column, $msg, $val = null)
{


    # code...
    $sqlUpdate = (is_null($val)) ? $db->dbc->prepare(
        "UPDATE user SET $column = IF ($column, 0, 1) WHERE username = :username;"
    ) :
        $db->dbc->prepare(
            "UPDATE user SET $column = $val WHERE username = :username;"
        );

    $sqlUpdate->execute([
        ':username'  =>  $_GET['username']
    ]);

    $sqlUpdate = null;

    $sqlFetch = $db->dbc->prepare("SELECT username, $column FROM user WHERE username= :username");
    $sqlFetch->execute([':username' =>  $_GET['username']]);

    $fetch = $sqlFetch->fetchAll();

    $responMsg = sprintf($msg, $fetch[0][0]);
    $sqlFetch = null;
    //header("Location: ./users.php");
    $data = ['msg' => $responMsg, 'newValue' => $fetch[0][1]];
    header('Content-type: application/json');
    echo json_encode($data);
    exit;
};

function delete(DBConnection $db, $msg)
{

    # code...
    $sqlUpdate = $db->dbc->prepare(
        "DELETE FROM user WHERE username = :username;"
    );

    $sqlUpdate->execute([
        ':username'  =>  $_GET['username']
    ]);

    $sqlUpdate = null;


    $responMsg = sprintf($msg, $_GET['username']);

    //header("Location: ./users.php");
    $data = ['msg' => $responMsg];
    header('Content-type: application/json');
    echo json_encode($data);
    exit;
};

$username = '';
$email = '';
$password = '';
$name_first = '';
$name_last = '';
$phone = '';
$is_locked = '';
$is_reset_password = '';
$is_activated = '';


// single actions
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (!empty($_GET['username']) && !empty($_GET['action'])) {
        switch ($_GET['action']) {
            case 'activate':
                $repMsg = "The User <br><strong>%s</strong> - is activated!";
                update($database, 'is_activated', $repMsg, '1');
                break;
            case 'toggleLock':
                $repMsg = "The User <br><strong>%s</strong> - Lock edited!";
                update($database, 'is_locked', $repMsg);
                break;
            case 'resetPswd':
                $repMsg = "The User Password <br><strong>%s</strong> - has been reset!";
                update($database, 'is_reset_password', $repMsg, '1');
                break;
            case 'deleteUser':
                $repMsg = "The User <br><strong>%s</strong> - has been deleted!";
                delete($database, $repMsg);
                break;
        }
    }
}
