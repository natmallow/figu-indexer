<?php
namespace gnome;

@session_start();

use gnome\classes\DBConnection;
use gnome\classes\MessageResource;
use PDO;

class Door {

    public $rs = null;

    public function init($securityId) {
        $database = new DBConnection();

        $query = "SELECT ua.*, DATE_FORMAT(ua.date_to_expire, '%Y-%m-%dT%H:%i') AS expiry, "
               . "u.name_first, u.name_last, u.phone, "
               . "AES_DECRYPT(u.password, 'moodybluz') AS password "
               . "FROM user_callback AS ua "
               . "INNER JOIN user AS u ON u.email = ua.email "
               . "WHERE ua.security_id = :security_id";

        $pdoConn = $database->dbc->prepare($query);
        $pdoConn->execute([':security_id' => $securityId]);

        $count = $pdoConn->rowCount();
        if ($count) {
            $this->rs = $pdoConn->fetch(PDO::FETCH_ASSOC);
            return $this;
        } else {
            die('REQUEST WAS NOT FOUND!');
        }
    }
    
    public function isValid() {
        $expiry = $this->rs['expiry'];
        $date_to_expire = $this->rs['date_to_expire'];
        $today = date('Y-m-d H:i:s');
        if (strtotime($today) > strtotime($date_to_expire)) {
            die('REQUEST HAS EXPIRED!');
        }
        return $this;
    }

    public function callAction() {
        $action = $this->rs['action_name'];
        
        try {
            $this->$action();
        } catch (\Exception $exc) {
            echo $exc->getTraceAsString();
        }
    }

    /* Response object methods */
    private function Password_Reset() {
        $_SESSION['resetPassword'] = true;
        $_SESSION['password'] = $this->rs['password'];
        $_SESSION['email'] = $this->rs['email'];
        header("Location: http://{$_SERVER['SERVER_NAME']}/gnome/login");
        exit();
    }

    private function Study_Group_Invite() {
        $_SESSION['resetPassword'] = true;
        $_SESSION['password'] = $this->rs['password'];
        header("Location: http://{$_SERVER['SERVER_NAME']}/gnome/login");
        exit();
    }
    
    private function Organizational_Group_Meeting_Invite() {
        // Implementation...
    }
    
    private function Spiritual_Meeting_Invite() {
        // Implementation...
    }
    /* End of response object methods */
}


if ($_GET['security_id']) {
    $door = new Door();
    $door->init($_GET['security_id'])->isValid()->callAction();
} else {
    header("Location: /");
};










//echo '<pre>';
//var_dump($rs);
//
//
//die();
//echo $_GET['security_id'];
//echo '<br>'.$_GET['lang'];
$_SESSION['resetPassword'] = true;
$_SESSION['password'] = $rs['password'];
header("Location: http://{$_SERVER['SERVER_NAME']}/gnome/login");
exit();
?>

