<?php

namespace gnome\classes;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use gnome\classes\MessageResource as MessageResource;
use gnome\classes\DBConnection as DBConnection;


require_once(__DIR__ . '/../email/functions.php');

class Director extends DBConnection
{

    public $msg = null;

    public function __construct()
    {
        parent::__construct();
        $this->msg = MessageResource::instance();
    }

    //put your code here

    public function resetPasswordEmail($email, $fname)
    {
        $name = $this->msg->getMsg('pwdrst');
        $action = '';
        $expireDate = date('Y-m-d', strtotime(' + 7 days'));
        $from = 'no-reply@figuarizona.org';
        $subject = 'Figu Arizona Password Reset';
        $userName = $fname;


        list($link, $linkRaw) = $this->getEncodedRequest($name, $email, $action, $expireDate);
        $html = getPasswordEmail($link, $userName);

        $text = 'Copy and paste into url: \n' . $linkRaw;
        $emailcc = '';


        $this->_sendEmail($from, $email, $subject, $html, $text, $emailcc, $name);
    }

    public function emailRequestAccess($email, $fname)
    {
        $from = 'no-reply@figuarizona.org';
        $subject = 'Figu Arizona Access Request Recieved';
        $userName = $fname;
        $html = confirmRequestAccess($userName);
        $text = 'Access Requested: \n';
        $emailcc = 'info@figuarizona.org';
        $this->_sendEmail($from, $email, $subject, $html, $text, $emailcc);
    }

    public function setAttendMeeting($email)
    {
        $action = 'attendMeeting';
        $salt = 'pbox2';
        $expires = '';
    }

    public function setMeetingPresentation($param)
    {
        $action = 'attendMeeting';
        $salt = 'pbox2';
        $expires = '';
    }

    public function setNotAttendMeeting($param)
    {
        $action = 'attendMeeting';
        $salt = 'pbox2';
        $expires = '';
    }

    public function getEncodedRequest($name, $email, $action, $expireDate, $actionParams = '')
    {

        $sql = "INSERT INTO user_callback
        (
            security_id,
            email,
            action_name,
            action,
            date_to_expire
         ) VALUES (
            :security_id,
            :email,
            :action_name,
            :action,
            :date_to_expire
        ) ON DUPLICATE KEY UPDATE 
            security_id = :security_id,
            date_to_expire = :date_to_expire,
            action = :action
        ";

        $pdoc = $this->dbc->prepare($sql);

        $actionArr = [
            'email' => $email,
            'action' => $action,
            'actionParams' => $actionParams,
            'expireDate' => $expireDate
        ];

        $pdoc->execute([
            ':security_id' => $this->_generateSecurityString(),
            ':email' => $email,
            ':action_name' => $name,
            ':action' => json_encode($actionArr),
            ':date_to_expire' => $expireDate
        ]);

        $id =  $this->lastInsertId();
        //        var_dump( $id );
        //        die();

        $sql = "SELECT security_id 
                FROM user_callback 
                WHERE user_callback_id = :id";

        $pdoc =  $this->dbc->prepare($sql);

        $pdoc->execute([':id' => $id]);

        $row = $pdoc->fetch();

        if ($_SERVER['HTTP_ENVIRONMENT'] == 'dev') {
            return ["<a href='https://dev.figuarizona.org/door-$row[security_id]' >https://dev.figuarizona.org/door-$row[security_id]</a>", "https://dev.figuarizona.org/door-$row[security_id]"];
        }

        return ["<a href='https://www.figuarizona.org/door-$row[security_id]' >https://www.figuarizona.org/door-$row[security_id]</a>", "https://www.figuarizona.org/door-$row[security_id]"];
    }

    public function decodeRequest($param)
    {
        $action = 'attendMeeting';
        $salt = 'pbox2';
        $expires = '';
    }

    private function _generateSecurityString($strength = 49)
    {

        $permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

        $input_length = strlen($permitted_chars);
        $random_string = '';
        for ($i = 0; $i < $strength; $i++) {
            $random_character = $permitted_chars[mt_rand(0, $input_length - 1)];
            $random_string .= $random_character;
        }

        return $random_string;
    }

    private function _sendEmail($from, $to, $subject, $html, $text, $emailcc = '', $name = '')
    {
        $mail = new PHPMailer(true);

        if ($_SERVER['HTTP_ENVIRONMENT'] == 'dev') {
            // Local email settings
            include(__DIR__ . '/../includes/email.config.php');
        }

        try {
            $mail->setFrom($from, 'Figu Arizona');
            $mail->addAddress($to, $name);
            $mail->Subject = $subject;
            if ($emailcc) {
                $mail->addBCC($emailcc, 'Info');
            }
            $mail->isHTML(true);
            $mail->Body = $html;
            $mail->AltBody = $text;

            $mail->send();
        } catch (\PHPMailer\PHPMailer\Exception $e) {
            echo "Error setting sender: " . $e->errorMessage();
        } catch (\Exception $e) {
            echo "Error: " . $e->getMessage();
        }
    }
}

//$dbconfig = require_once( __DIR__ . '/../../includes/crystal/settings.config.php' );
//require_once( __DIR__ . '/../../includes/crystal/db.connect.php' );
//$database = new DBConnection();
//$s = new security( $database );
//$s->resetPasswordEmail( 'natdrip@gmail.com', 'Nathanael' );