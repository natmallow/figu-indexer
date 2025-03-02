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


function sendTheMail($name, $email){
    $to = $email;

    $greeting = ($name == '')? "Hello there," : "Dear $name,";

    $injectText = 'As official representitives of the so called intelligentsia';

    $subject = 'Infomation about SETI';

    $headers = "From: natmallow@gmail.com \r\n";
    //$headers .= "Reply-To: ". strip_tags($_POST['req-email']) . "\r\n";
    //$headers .= "CC: susan@example.com\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";


    $message = '<html><body>';
    $message .= '<div dir="ltr">';
    $message .= '<p style="margin:0in 0in 0.0001pt"><font face="tahoma, sans-serif"><span style="color:rgb(38,40,42)">Dear&nbsp;</span></font><span style="color:rgb(38,40,42);font-family:tahoma,sans-serif">'.$greeting.'&nbsp;</span></p>';
    $message .= '<p style="margin:0in 0in 0.0001pt"><span style="color:rgb(38,40,42)"><br></span></p>';
    $message .= '<p style="margin:0in 0in 0.0001pt"><span style="color:rgb(38,40,42)">'.$injectText.'</span><span style="color:rgb(38,40,42)">, we ask that you take a moment to read the following.</span></p>';
    $message .= '<p style="color:rgb(38,40,42);margin:0in 0in 0.0001pt"><br></p>';
    $message .= '<p style="margin:0in 0in 0.0001pt"><font face="tahoma, sans-serif"><span style="color:rgb(38,40,42)">The pentagon does not release information unless it has an ulterior motive. This is no different with their recent acknowledgement and release of "ufo" footage. What is their objective, what is their end goal, what&nbsp;</span></font>machinations lay in store<span style="color:rgb(38,40,42);font-family:tahoma,sans-serif">? Or better yet how can we help to steer&nbsp;their narrative to&nbsp;something positive.&nbsp;<br><br>It is simple, do not donate any money just sign this&nbsp;</span><a href="http://chng.it/QytzGGTh" rel="nofollow" style="color:rgb(5,99,193);font-family:tahoma,sans-serif" target="_blank" data-saferedirecturl="https://www.google.com/url?q=http://chng.it/QytzGGTh&amp;source=gmail&amp;ust=1623022512408000&amp;usg=AFQjCNFD6TddCvR0e6bHvNKAUg9cOjZ7XA"><b><span style="color:red">petition</span></b></a><span style="color:rgb(38,40,42);font-family:tahoma,sans-serif">&nbsp;and we will do the rest</span><span style="color:rgb(38,40,42);font-family:tahoma,sans-serif">.&nbsp;</span></p>';
    $message .= '<p style="margin:0in 0in 0.0001pt"><font face="tahoma, sans-serif"><br></font></p>';
    $message .= '<p style="margin:0in 0in 0.0001pt"><span style="color:rgb(38,40,42)"><font face="tahoma, sans-serif">About the petition:&nbsp;</font></span></p>';
    $message .= '<p style="margin:0in 0in 0.0001pt"><span style="color:rgb(38,40,42)"><font face="tahoma, sans-serif">They Fly&nbsp;Productions is on record over 10 years of having provided SETI with scientific evidence of extraterrestrial contacts, ongoing in Switzerland for 80 years.</font></span></p>';
    $message .= '<p style="color:rgb(38,40,42);margin:0in 0in 0.0001pt"><font face="tahoma, sans-serif">&nbsp;</font></p>';
    $message .= '<p style="color:rgb(38,40,42);margin:0in 0in 0.0001pt"><font face="tahoma, sans-serif">The voluminous, still irreproducible physical evidence provided by <b>Billy Meier</b> has been independently analyzed, authenticated and peer-reviewed by US astronauts, USAF OSI investigators, photographic and special effects experts, scientists and aerospace experts from&nbsp;<a href="https://www.prweb.com/releases/2014/05/prweb11867883.htm" rel="nofollow" style="color:rgb(5,99,193)" target="_blank" data-saferedirecturl="https://www.google.com/url?q=https://www.prweb.com/releases/2014/05/prweb11867883.htm&amp;source=gmail&amp;ust=1623022512408000&amp;usg=AFQjCNFImgegP9bibIEVd1JcknhdgjXkrw">NASA</a>, JPL, McDonnell Douglas, USGS, IBM, etc.</font></p>';
    $message .= '<p style="color:rgb(38,40,42);margin:0in 0in 0.0001pt"><font face="tahoma, sans-serif">&nbsp;</font></p>';
    $message .= '<p style="color:rgb(38,40,42);margin:0in 0in 0.0001pt"><font face="tahoma, sans-serif">An even higher standard of proof is contained in the hundreds of specific, error-free examples of previously unknown scientific information, a great deal of which pertains to astronomy, physics, medical, environmental, etc.&nbsp;</font></p>';
    $message .= '<p style="color:rgb(38,40,42);margin:0in 0in 0.0001pt"><font face="tahoma, sans-serif">&nbsp;</font></p>';
    $message .= '<p style="color:rgb(38,40,42);margin:0in 0in 0.0001pt"><font face="tahoma, sans-serif">Thank you for your valuable time, and I greatly look forward to hearing from you and providing additional substantiation for all claims referred to above.</font></p>';
    $message .= '<p style="color:rgb(38,40,42);margin:0in 0in 0.0001pt"><font face="tahoma, sans-serif"><br></font></p>';
    $message .= '<p style="margin:0in 0in 0.0001pt"><b><font face="tahoma, sans-serif">Do not donate any money for the petition. &nbsp; </font></b></p>';
    $message .= '<p style="color:rgb(38,40,42);margin:0in 0in 0.0001pt"><font face="tahoma, sans-serif"><br></font></p>';
    $message .= '<p style="color:rgb(38,40,42);margin:0in 0in 0.0001pt"><font face="tahoma, sans-serif">Thank you,&nbsp;</font></p>';
    $message .= '<p style="color:rgb(38,40,42);margin:0in 0in 0.0001pt"><font face="tahoma, sans-serif">Nathanael Mallow</font></p>';
    $message .= '<p style="color:rgb(38,40,42);margin:0in 0in 0.0001pt"><a href="mailto:natmallow@gmail.com" rel="nofollow" style="color:rgb(5,99,193)" target="_blank"><font face="tahoma, sans-serif">natmallow@gmail.com</font></a></p>';
    $message .= '<p style="color:rgb(38,40,42);margin:0in 0in 0.0001pt"><a href="mailto:pr@theyfly.com" rel="nofollow" style="color:rgb(5,99,193)" target="_blank"><font face="tahoma, sans-serif">pr@theyfly.com</font></a><br><br><b>cut and paste link</b>:&nbsp;<a href="https://www.change.org/p/united-nations-request-seti-to-reveal-the-billy-meier-ufo-contacts?recruiter=1140336243&amp;recruited_by_id=7d12b370-dc0b-11ea-907e-cfcc5004e73b&amp;utm_source=share_petition&amp;utm_medium=copylink&amp;utm_campaign=petition_dashboard" target="_blank" data-saferedirecturl="https://www.google.com/url?q=https://www.change.org/p/united-nations-request-seti-to-reveal-the-billy-meier-ufo-contacts?recruiter%3D1140336243%26recruited_by_id%3D7d12b370-dc0b-11ea-907e-cfcc5004e73b%26utm_source%3Dshare_petition%26utm_medium%3Dcopylink%26utm_campaign%3Dpetition_dashboard&amp;source=gmail&amp;ust=1623022512408000&amp;usg=AFQjCNG9jD7wreEvNdYMNTf9V3XFtXU3Mg">https://www.change.org/<wbr>p/united-nations-request-seti-<wbr>to-reveal-the-billy-meier-ufo-<wbr>contacts?recruiter=1140336243&amp;<wbr>recruited_by_id=7d12b370-dc0b-<wbr>11ea-907e-cfcc5004e73b&amp;utm_<wbr>source=share_petition&amp;utm_<wbr>medium=copylink&amp;utm_campaign=<wbr>petition_dashboard</a></p></div>';
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



function run(){
    global $arrMail;
    $count = count($arrMail);
    for($i = 0; $i < $count; $i++){
        $name = ucwords(strtolower(trim($arrMail[$i][0]))); 
        $email = trim($arrMail[$i][1]); 


        if(!strpos($name, ' ')){
            $name = $name;
        }elseif (strpos($name, ' ')<3) {
            $pos = strpos($name, ' ', strpos($name, ' ')+strlen(' '));
            $name = substr($name, 0, $pos);    
        } else {
            $name = substr($name, 0, strpos($name, ' ')); 
        }

        if($name == ''){
            $name = ucwords(strtolower(trim($arrMail[$i][0]))); 
        }

        sendTheMail($name, $email);
        echo "$name and $email <br>";
        flush();

       sleep(4);
    }

}

run();