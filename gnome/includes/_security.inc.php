<?php
namespace gnome\includes;

@session_start();

class Security{
    
    public function isLoggedIn() {
        if ($_SESSION['loggedIn'] != true) {
            header("Location: ./../gnome/login");
            exit();
        }
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

    public function hasAccess() {

    }

 
}


$Security = new Security();
$Security->isLoggedIn(); // ->roles()->hasAccess();