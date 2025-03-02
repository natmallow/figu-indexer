<?php

namespace gnome\classes;

/**
 * Description of messageresource
 *
 * @author PeaceParty
 */
class MessageResource {

    //put your code here
    protected static $instance = null;
    
    
    private $vp = [
        'pwdrst' => 'Password_Reset',
        'reqAcs' => 'Request_Access',
        'sgroup' => 'Study_Group_Invite',
        'meeting' => 'Organizational_Group_Meeting_Invite',
        'smeeting' => 'Spiritual_Meeting_Invite',
        'encryptHash' => 'moodybluz'
    ];

    public static function instance() {
        if (is_null(self::$instance)) {
            self::$instance = new MessageResource();
        }
        return self::$instance;
    }

    public function getMsg($string) {
        if (array_key_exists($string, $this->vp)) {
            return $this->vp[$string];
        } else {
            return 'NOT SET';
        }
    }

}
