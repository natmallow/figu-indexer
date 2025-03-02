<?php

namespace gnome\classes\model;

use gnome\classes\DBConnection as DBConnection;
use gnome\classes\MessageResource as MessageResource;
use PDO;

class User extends DBConnection
{

    private $table = 'user';
    private $encryptHash;

    public function __construct()
    {
        parent::__construct();
        $this->encryptHash = MessageResource::instance()->getMsg('encryptHash');
    }

    

    public function addUser()
    {

        $sql = "INSERT INTO $this->table 
        (
            username,
            email, 
            password,
            name_first,  
            name_last, 
            phone, 
            is_locked, 
            is_reset_password,
            is_activated,
            reason
         ) VALUES (
            :username,
            :email, 
            AES_ENCRYPT(:password, '$this->encryptHash'),
            :name_first,  
            :name_last, 
            :phone, 
            :is_locked, 
            :is_reset_password,
            :is_activated,
            :reason
        )";

        $pdoc = $this->dbc->prepare($sql);

        $paramArr = [
            ':username' =>  $_POST['username'],
            ':email' =>  $_POST['email'],
            ':password' =>  $_POST['password'],
            ':name_first' =>  $_POST['name_first'],
            ':name_last' =>  $_POST['name_last'],
            ':phone' =>  $_POST['phone'],
            ':is_locked' =>  isset($_POST['is_locked']) ? '1' : '0',
            ':is_reset_password' =>  '1',
            ':is_activated' =>  isset($_POST['is_activated']) ? '1' : '0',
            ':reason' =>  $_POST['reason']
        ];

        $pdoc->execute($paramArr);

        $_SESSION['actionResponse'] = $_POST['name_first'] . ' has been saved!';
    }

    public function updateUserRoles()
    {

        // remove all roles
        $sql = "DELETE FROM link_user_role WHERE username = :username";
        $pdoc = $this->dbc->prepare($sql);
        $paramArr = [
            ':username' => $_POST['username']
        ];
        $pdoc->execute($paramArr);

        // add roles back
        $sql = "INSERT INTO link_user_role( 
            username,
            role_id
        ) VALUES (
            :username,
            :role_id
        )";
        $pdoc = $this->dbc->prepare($sql);

        $roles = $_POST['roles'];

        for ($i = 0; $i < count($roles); $i++) {
            $pdoc->execute([
                ':username' => $_POST['username'],
                ':role_id' => $roles[$i]
            ]);
        }
    }

    public function updateUser()
    {

        $sql = "UPDATE $this->table SET 
                email = :email, 
                password = AES_ENCRYPT(:password, '$this->encryptHash'),
                name_first = :name_first, 
                name_last = :name_last,
                phone = :phone,
                is_locked = :is_locked,
                is_reset_password = :is_reset_password,
                is_activated = :is_activated
            WHERE username = :username";

        $pdoc = $this->dbc->prepare($sql);

        $paramArr = [
            ':email' =>  $_POST['email'],
            ':password' =>  $_POST['password'],
            ':name_first' =>  $_POST['name_first'],
            ':name_last' =>  $_POST['name_last'],
            ':phone' =>  $_POST['phone'],
            ':is_locked' =>  isset($_POST['is_locked']) ? 1 : 0,
            ':is_reset_password' =>  isset($_POST['is_reset_password']) ? 1 : 0,
            ':username' => $_POST['username'],
            ':is_activated' =>  isset($_POST['is_activated']) ? '1' : '0'
        ];
        //  echo $this->showquery( $sql, $paramArr );
        //  exit();
        $pdoc->execute($paramArr);

        $_SESSION['actionResponse'] = 'Changes saved!';
    }

    public function checkUser($username, $email)
    {
        $sql = "SELECT * FROM  $this->table
            WHERE username = :username 
            OR email = :email
        ";

        $pdoc = $this->dbc->prepare($sql);

        $paramArr = [
            ':username' => $username,
            ':email' => $email
        ];
        // echo $this->showquery( $sql, $paramArr );
        // exit();
        $pdoc->execute($paramArr);

        return $pdoc->rowCount();
    }

    public function userLogin($username, $email, $password)
    {

        $sql = "SELECT 
                    U.username,
                    U.email,
                    AES_DECRYPT(U.password,'$this->encryptHash') AS password,
                    U.create_time,
                    U.name_first,
                    U.name_last,
                    U.phone,
                    U.attempts,
                    U.is_locked,
                    U.is_reset_password,
                    U.security_key,
                    U.is_activated,
                    U.reason,
                    JSON_ARRAYAGG(R.role_name) AS roles
                FROM
                    user U
                        LEFT JOIN
                    link_user_role LR ON U.username = LR.username
                        LEFT JOIN
                    role R ON LR.role_id = R.role_id
                WHERE U.username = :username 
                AND U.email = :email
                AND U.password = AES_ENCRYPT(:password,'$this->encryptHash')                
                ";

        $pdoc = $this->dbc->prepare($sql);

        $paramArr = [
            ':username' => $username,
            ':email' => $email,
            ':password' => $password
        ];
        // echo $this->showquery( $sql, $paramArr );
        // exit();
        $pdoc->execute($paramArr);

        return ($pdoc->rowCount() != 0) ? (object) $pdoc->fetch(PDO::FETCH_ASSOC) : null;
    }

    public function getUserLoginAttempts($username)
    {
        $sql = "SELECT * FROM $this->table 
                WHERE username = :username";

        $pdoc = $this->dbc->prepare($sql);

        $paramArr = [':username' => $username];

        $pdoc->execute($paramArr);

        return $pdoc;
    }

    public function getUsers($col = null)
    {
        if (is_null($col)) {
            $sql = "SELECT * FROM  $this->table";
        } else {
            $cols = implode(",", $col);
            $sql = "SELECT $cols FROM  $this->table";
        }


        $pdoc = $this->dbc->prepare($sql);

        $pdoc->execute();

        return $pdoc->fetchAll();
    }

    // specific naming to avoid confusion
    public function getUserById($id)
    {
        $sql = "SELECT 
                    U.username,
                    U.email,
                    AES_DECRYPT(U.password,'$this->encryptHash') AS password,
                    U.create_time,
                    U.name_first,
                    U.name_last,
                    U.phone,
                    U.attempts,
                    U.is_locked,
                    U.is_reset_password,
                    U.security_key,
                    U.is_activated,
                    U.reason,
                    JSON_ARRAYAGG(R.role_name) AS roles
                    FROM
                        user U 
                        LEFT JOIN link_user_role LR ON U.username = LR.username
                        LEFT JOIN role R ON LR.role_id = R.role_id
                WHERE user_id = :user_id";

        $pdoc = $this->dbc->prepare($sql);

        $paramArr = [':user_id' => $id];

        $pdoc->execute($paramArr);

        return $pdoc->fetch();
    }

    //returns all user information 
    public function getUserByUsername($username)
    {
        $sql = "SELECT 
            U.username,
            U.email,
            AES_DECRYPT(U.password,'$this->encryptHash') AS password,
            U.create_time,
            U.name_first,
            U.name_last,
            U.phone,
            U.attempts,
            U.is_locked,
            U.is_reset_password,
            U.security_key,
            U.is_activated,
            U.reason,
            JSON_ARRAYAGG(R.role_name) AS roles
                FROM
                    user U 
                    LEFT JOIN link_user_role LR ON U.username = LR.username
                    LEFT JOIN role R ON LR.role_id = R.role_id
                WHERE U.username = :username
        ";

        $pdoc = $this->dbc->prepare($sql);

        $paramArr = [':username' => $username];

        $pdoc->execute($paramArr);

        return $pdoc->fetch();
    }

    public function getUserId($username)
    {
        $sql = "SELECT user_id FROM
                user
                WHERE username = :username";

        $pdoc = $this->dbc->prepare($sql);

        $paramArr = [':username' => $username];

        $pdoc->execute($paramArr);

        return $pdoc->fetch()['user_id'];
    }

    public function lockUser($username)
    {

        $sql = "UPDATE $this->table SET 
                is_locked = '1'
                WHERE username = :username";

        $pdoc = $this->dbc->prepare($sql);

        $pdoc->execute([':username' => $username]);

        return;
    }

    public function updateUserPassword($username, $password)
    {

        $sql = "UPDATE $this->table SET 
                password = AES_ENCRYPT(:password, '$this->encryptHash'),
                is_reset_password = '0'
                WHERE username= :username";

        $pdoc = $this->dbc->prepare($sql);

        $paramArr = [
            ':password' => $password,
            ':username' => $username
        ];
        // echo $this->showquery( $sql, $paramArr );
        // exit();
        $pdoc->execute($paramArr);

        return;
    }

    public function getUserByEmail($email)
    {
        $sql = "SELECT * FROM user WHERE email = :email";
        $pdoc = $this->dbc->prepare($sql);
        $pdoc->execute([':email' => $email]);
        return $pdoc->fetch();
    }
}
