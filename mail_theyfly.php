<?php

ini_set('max_execution_time', '0');

//use PHPMailer\PHPMailer\PHPMailer;
//use PHPMailer\PHPMailer\Exception;
//require './vendor/autoload.php';
//
//$mail = new PHPMailer(TRUE);
//
//$mail->setFrom('info@figuarizona.org', 'Az Figu');
//
//$mail->addAddress('natdrip@gmail.com', 'Nathanael');
//
//$mail->Subject = 'Force';
//
//$mail->Body = 'There is a great disturbance in the Force.';
//
//if (!$mail->send())
//{
//   echo $mail->ErrorInfo;
//}
//
//exit();
// //the subject
// $sub = "This is a test";
// //the message
// $msg = "This is a test";
// //recipient email here
// $rec = "natmallow@gmail.com";
// //send email
// mail($rec,$sub,$msg);


/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


function sendTheMail($name, $email) {
    $to = $email;

    $greeting = ($name == '') ? "Hello there," : "Hi $name,";

    $injectText = 'As official representitives of the so called intelligentsia';

    $subject = '(On behalf of Theyfly) How to communicate information without proselytizing.';

    $headers = "From: natmallow@gmail.com \r\n";
    
    $headers .= "Reply-To: pr@theyfly.com \r\n";
    //$headers .= "CC: susan@example.com\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

    $message = '<html><body>';
    $message .= '<div id=":2hh" class="a3s aiL "><div dir="ltr"><div>'.$greeting.'<br><br>The July 24 FIGU AZ meeting was very important and it was good to see everyone.<br><br>Norbert presented a very clear expression of the frustration that many people may feel if they contemplate reaching out to people about the COVID information from Billy Meier.<br><br>I will reiterate my suggestion for those who want to communicate information, without proselytizing, and leave the responsibility completely with the person to whom the information is presented:<br><br>How do you think this man knew all this information before everyone else?<br></div><div><br></div><div><a href="https://theyflyblog.com/2020/09/new-online-covid-19-test/" target="_blank" data-saferedirecturl="https://www.google.com/url?q=https://theyflyblog.com/2020/09/new-online-covid-19-test/&amp;source=gmail&amp;ust=1627866760226000&amp;usg=AFQjCNFj0d-l2fMxk1avVLcvTtBnl2EqeQ">New Online COVID Test</a>&nbsp;(<a href="https://theyflyblog.com/2020/09/new-online-covid-19-test/" target="_blank" data-saferedirecturl="https://www.google.com/url?q=https://theyflyblog.com/2020/09/new-online-covid-19-test/&amp;source=gmail&amp;ust=1627866760226000&amp;usg=AFQjCNFj0d-l2fMxk1avVLcvTtBnl2EqeQ">https://theyflyblog.com/<wbr>2020/09/new-online-covid-19-<wbr>test/</a>)<br></div><div><br></div>That\'s essentially it. It could be phrased a few different ways but that\'s really all that one would need to convey. It would be suitable to offer to people you know with whom you are discussing any aspects of the Meier contacts, or the COVID situation. The same holds true for any people you don\'t know, scientists, medical people, journalists, commentators, etc. <br><br>One doesn\'t have to argue, coerce, convince, etc.<br><br>Let me know if you have any questions.<br><br>Salome,<br><br>MH<br>michael@theyfly.com<div class="yj6qo"></div><div class="adL"><br></div></div><div class="adL">
</div></div>';
    $message .= '</body></html>';


    $success = mail($to, $subject, $message, $headers);

    if (!$success) {
            var_dump($success);
            echo error_get_last();
        } else {
            echo "It worked!";
        }
    }

$safeArr = ['dr', 'md'];
$arrMail = [

];


//$arrMail = [['Nathanael Mallow', 'natdrip@gmail.com'],['Michael Horn', 'pr@theyfly.com']];

function run() {
    global $arrMail;
    $count = count($arrMail);
    for ($i = 0; $i < $count; $i++) {
        $name = ucwords(strtolower(trim($arrMail[$i][0])));
        $email = trim($arrMail[$i][1]);


        if (!strpos($name, ' ')) {
            $name = $name;
        } elseif (strpos($name, ' ') < 3) {
            $pos = strpos($name, ' ', strpos($name, ' ') + strlen(' '));
            $name = substr($name, 0, $pos);
        } else {
            $name = substr($name, 0, strpos($name, ' '));
        }

        if ($name == '') {
            $name = ucwords(strtolower(trim($arrMail[$i][0])));
        }

        sendTheMail($name, $email);
        echo "$name and $email <br>";
        flush();

        sleep(4);
    }
}

run();
