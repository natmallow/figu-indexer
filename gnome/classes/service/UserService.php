<?php

namespace gnome\classes\service;

use gnome\classes\model\User;

class UserService
{


    public function createUser($arg)
    {

        // add logic here 
        $userModel = new User();
        $userModel->addUser($arg);

    }

}