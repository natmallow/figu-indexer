<?php 
$mail->isSMTP();
// change this to 0 if the site is going live
$mail->SMTPDebug = 0;
$mail->Debugoutput = 'html';
$mail->Host = 'smtp.gmail.com';
$mail->Port = 587;
$mail->SMTPSecure = 'tls';
//use SMTP authentication
$mail->SMTPAuth = true;
//Username to use for SMTP authentication
$mail->Username = 'natdrip@gmail.com';
$mail->Password = 'hythltqafaodvbgy';