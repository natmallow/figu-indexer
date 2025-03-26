<?php
namespace gnome\classes;

use gnome\classes\model\Indices;

@session_start();

class Security extends DBConnection {

    // Hold the class instance.
    protected static $instance = null;

    public $userPremissions;

    // The constructor is private
    // to prevent initiation with outer code.
    private function __construct()
    {
        parent::__construct();
        // The expensive process (e.g.,db connection) goes here.
    }

    public static function getInstance()
    {
        if (self::$instance == null)
        {
            self::$instance = new Security();
        }

        return self::$instance;
    }


    public function isLoggedIn() {
         if (@$_SESSION['loggedIn'] != true) {
            header("Location: /gnome/login");
            die();
        }
        return $this;
    }

    public function isSuperAdmin() {
        if (in_array('super_admin', json_decode($_SESSION['roles']))) {
            return true;
        } 
    }

    public function indexPermission($indexId) {
             
        $Indices = new Indices();
        $username = $_SESSION['username'];

        if (in_array('admin', json_decode($_SESSION['roles']))) {
            return;
        } 

        //  var_dump($Indices->canUserAccess($indexId, $username));
        //  die();
        $this->userPremissions = $Indices->canUserAccess($indexId, $username);
       
        if (trim(strtolower($Indices->getIndexOwner($indexId)['userName'])) == trim(strtolower($username))) {
            return;
        }

        // if the count is zero there is no permssion for the user
        if (!$this->userPremissions['count']) {
            $_SESSION['actionResponse'] = "You do not have access.";
            header("Location: /gnome/indexer/indices.php");
            exit();
        };

        return $this;
    }

    /**
     * userPremissions obj
     *  { count => 0|1, 
     *    indices_id => int(), 
     *    user_id => int(), 
     *    is_owner => 0|1, 
     *    can_read => 0|1, 
     *    can_write => 0|1, 
     *    can_admin => 0|1 }
     */
    public function hasRightAccess($neededAccess, $failResponse = "You do not have access.", $location = "/gnome/indexer/indices.php") {
        // var_dump($this->userPremissions[$neededAccess]);
        // die();
        if ($this->userPremissions[$neededAccess] == '0') {
            $_SESSION['actionResponse'] = $failResponse;
            header("Location: $location");
            exit();
        };
        return $this;
    }

    public function roles($acceptedRoles=[]) {
        $length = count($acceptedRoles);
        // this is the main permission for the whole site
        if (in_array('admin', json_decode($_SESSION['roles']))) {
            return true;
        } 
        
        for($i=0;$i<$length;$i++){
            if (in_array($acceptedRoles[$i], json_decode($_SESSION['roles']))) {
                return true;
            }
        }

        return false;
    }  

    // to do
    public function hasAccess() {
        // return $this;
    }

}

// $SECURITY = Security::getInstance();
// $SECURITY->isLoggedIn();