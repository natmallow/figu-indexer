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

    $greeting = ($name == '') ? "Hello there," : "Dear $name,";

    $injectText = 'As official representitives of the so called intelligentsia';

    $subject = 'On behalf of Better Future ';

    $headers = "From: natmallow@gmail.com \r\n";
    
    $headers .= "Reply-To: natmallow@gmail.com \r\n";
    //$headers .= "CC: susan@example.com\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

    $message = '<html><body>';
    $message .= '<div id=":3t5" class="Am Al editable LW-avf tS-tW tS-tY" hidefocus="true" aria-label="Message Body" g_editable="true" role="textbox" aria-multiline="true" contenteditable="true" tabindex="1" style="direction: ltr; min-height: 250px;" spellcheck="false"><span id="gmail-docs-internal-guid-6b3b3610-7fff-b27a-2c2c-2f3ba5ca1764"><p dir="ltr" style="line-height: 1.38; margin-top: 0pt; margin-bottom: 0pt;"><span style="font-size: 11pt; font-family: Arial; color: rgb(34, 34, 34); background-color: rgb(255, 255, 255); font-weight: 400; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;">'.$greeting.'<br><br>We are asking you to help put an end to the war in Ukraine and help stop further war. War is never right and would not exist without constant promotion and belief.</span></p><p dir="ltr" style="line-height: 1.38; margin-top: 0pt; margin-bottom: 0pt;"><span style="font-size: 11pt; font-family: Arial; color: rgb(34, 34, 34); background-color: rgb(255, 255, 255); font-weight: 400; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;">Supplying arms to either side of a conflict means that the supplier is complacent in murder theft rape and destruction of life.</span></p><p dir="ltr" style="line-height: 1.38; margin-top: 0pt; margin-bottom: 0pt;"><span style="font-size: 11pt; font-family: Arial; color: rgb(34, 34, 34); background-color: rgb(255, 255, 255); font-weight: 400; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;"><br></span></p><p dir="ltr" style="line-height: 1.38; margin-top: 0pt; margin-bottom: 0pt;"><span style="font-size: 11pt; font-family: Arial; color: rgb(34, 34, 34); background-color: rgb(255, 255, 255); font-weight: 400; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;">When everything is known without the ignorance of belief leading the way, where true thought discernment, facts and knowledge are applied and judged correctly then it become clear that the whole of war is nothing but theft, destruction, murder and rape of humans, men women and childern.</span></p><p dir="ltr" style="line-height: 1.38; margin-top: 0pt; margin-bottom: 0pt;"><span style="font-size: 11pt; font-family: Arial; color: rgb(34, 34, 34); background-color: rgb(255, 255, 255); font-weight: 400; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;"><br></span></p><p dir="ltr" style="line-height: 1.38; margin-top: 0pt; margin-bottom: 0pt;"><span style="font-size: 11pt; font-family: Arial; color: rgb(34, 34, 34); background-color: rgb(255, 255, 255); font-weight: 400; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;">Actions that prolong war are action that are complicit in the theft, destruction, murder and rape of humans. These actions should not be tolerated, promoted or accepted in any way by those that have been charged with public service, nor should any leader promote actions of this nature neither in speech nor in profiteering directly or indirectly from the purchasing of war stocks, bonds, etc. because this is not the way of progress to a better future let alone it also does not represent the will of the people.</span></p><p dir="ltr" style="line-height: 1.38; margin-top: 0pt; margin-bottom: 0pt;"><span style="font-size: 11pt; font-family: Arial; color: rgb(34, 34, 34); background-color: rgb(255, 255, 255); font-weight: 400; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;"><br></span><span style="font-size: 11pt; font-family: Arial; color: rgb(34, 34, 34); background-color: rgb(255, 255, 255); font-weight: 400; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;">The delivery of weapons for war is a participation in the war and should be banned and not allowed. Those who do such things are complicit in the further murdering of their fellow man, and whose actions prolong war. The states and corporations that do this are accomplices in the murder humans and destruction of good acomplishments. If these actions continue then those who haven\'t completely lost their minds will see that the war they fight against is not only a direct one but one that encompasses all the moving parts behind the scenes. This means that those who have wised-up about the machinations of war will begin to strike at the supporters of such.</span></p><br><p dir="ltr" style="line-height: 1.38; margin-top: 0pt; margin-bottom: 0pt;"><span style="font-size: 11pt; font-family: Arial; color: rgb(34, 34, 34); background-color: rgb(255, 255, 255); font-weight: 400; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;">We urge you not to supply weapons to warring parties and not to support such actions do not become or contiunes to be an accomplice in the murder of humans.&nbsp; Participation in war automatically arises as soon as weapons are supplied to any warring party for the purpose of waging war.</span></p><p dir="ltr" style="line-height: 1.38; margin-top: 0pt; margin-bottom: 0pt;"><span style="font-size: 11pt; font-family: Arial; color: rgb(34, 34, 34); background-color: rgb(255, 255, 255); font-weight: 400; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;"><br></span></p><p dir="ltr" style="line-height: 1.38; margin-top: 0pt; margin-bottom: 0pt;"><span style="font-size: 11pt; font-family: Arial; color: rgb(34, 34, 34); background-color: rgb(255, 255, 255); font-weight: 400; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;">To be clear we must put an end to this war as quickly as possible and this can only come about if we no longer support Ukraine with war material and weapons. Remove NATO from Ukraine and keep any further expansion of NATO from the only warsaw regions. If NATO moves into Finland then you have just signed the deaths of many more humans.</span></p><p dir="ltr" style="line-height: 1.38; margin-top: 0pt; margin-bottom: 0pt;"><span style="font-size: 11pt; font-family: Arial; color: rgb(34, 34, 34); background-color: rgb(255, 255, 255); font-weight: 400; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;"><br></span><span style="font-size: 11pt; font-family: Arial; color: rgb(34, 34, 34); background-color: rgb(255, 255, 255); font-weight: 400; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;">We hope freedom and peace can count on you as the future can only be changed toward good and positive or toward the evil and negative with our actions we take now.</span></p><p dir="ltr" style="line-height: 1.38; margin-top: 0pt; margin-bottom: 0pt;"><span id="gmail-docs-internal-guid-3683de60-7fff-0a99-04bc-7e4a8c4efbcf"><br><span style="font-size: 11pt; font-family: Arial; font-variant-numeric: normal; font-variant-east-asian: normal; vertical-align: baseline; white-space: pre-wrap;">Thank you,</span><span style="font-size: 11pt; font-family: Arial; font-variant-numeric: normal; font-variant-east-asian: normal; vertical-align: baseline; white-space: pre-wrap;"><br></span><span style="font-size: 11pt; font-family: Arial; font-variant-numeric: normal; font-variant-east-asian: normal; vertical-align: baseline; white-space: pre-wrap;">Nathanael Mallow</span></span><br></p></span></div>';
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


// $arrMail = [['Honourable Joe Barton,<br>Of the 6th Congressional District, Texas','hello' ,'natdrip@gmail.com']];

$arrMail2 = [
    ['Honourable Joe Barton,<br>Of the 6th Congressional District, Texas','6th Congressional District, Texas<br>Rm. 2264 Rayburn House Office Building<br>Washington, DC 20515','BARTON06@HR.HOUSE.GOV'],
    ['Honourable Sherwood Boehlert,<br>Of the 23rd Congressional District, New York','23rd Congressional District, New York<br>Rm. 2246 Rayburn House Office Building<br>Washington, DC 20515','BOEHLERT@HR.HOUSE.GOV'],
    ['Honourable Rick Boucher,<br>Of the 9th Congressional District, Virginia','9th Congressional District, Virginia<br>Rm. 2245 Rayburn House Office Building<br>Washington, DC 20515','NINTHNET@HR.HOUSE.GOV'],
    ['Honourable Richard Burr,<br>Of the 5th Congressional District, North Carolina','5th Congressional District, North Carolina<br>Rm. 1431 Longworth House Office Building<br>Washington, DC 20515','MAIL2NC5@HR.HOUSE.GOV'],
    ['Honourable Dave Camp,<br>Of the 4th Congressional District, Michigan','4th Congressional District, Michigan<br>Rm. 137 Cannon House Office Building<br>Washington, DC 20515','DAVECAMP@HR.HOUSE.GOV'],
    ['Honourable Ben Cardin,<br>Of the 3rd Congressional District, Maryland','3rd Congressional District, Maryland<br>Rm. 104 Cannon House Office Building<br>Washington, DC 20515','CARDIN@HR.HOUSE.GOV'],
    ['Honourable Saxby Chambliss,<br>Of the 8th Congressional District, Georgia','8th Congressional District, Georgia<br>Rm. 1708 Longworth House Office Building<br>Washington, DC 20515','SAXBY@HR.HOUSE.GOV'],
    ['Honourable Dick Chrysler,<br>Of the 8th Congressional District, Michigan','8th Congressional District, Michigan<br>Rm. 327 Cannon House Office Building<br>Washington, DC 20515','CHRYSLER@HR.HOUSE.GOV'],
    ['Honourable John Conyers Jr,<br>Of the 14th Congressional District, Michigan','14th Congressional District, Michigan<br>Rm. 2426 Rayburn House Office Building<br>Washington, DC 20515','JCONYERS@HR.HOUSE.GOV'],
    ['Honourable Bud Cramer,<br>Of the 5th Congressional District, Alabama','5th Congressional District, Alabama<br>Rm. 236 Cannon House Office Building<br>Washington, DC 20515','BUDMAIL@HR.HOUSE.GOV'],
    ['Honourable Peter Defazio,<br>Of the 4th Congressional District, Oregon','4th Congressional District, Oregon<br>Rm. 2134 Rayburn House Office Building<br>Washington, DC 20515','PDEFAZIO@HR.HOUSE.GOV'],
    ['Honourable Peter Deutsch,<br>Of the 20th Congressional District, Florida','20th Congressional District, Florida<br>Rm. 204 Cannon House Office Building<br>Washington, DC 20515','PDEUTSCH@HR.HOUSE.GOV'],
    ['Honourable Jay Dickey,<br>Of the 4th Congressional District, Arkansas','4th Congressional District, Arkansas<br>Rm. 230 Cannon House Office Building<br>Washington, DC 20515','JDICKEY@HR.HOUSE.GOV'],
    ['Honourable Lloyd Doggett,<br>Of the 10th Congressional District, Texas','10th Congressional District, Texas<br>Rm. 126 Cannon House Office Building<br>Washington, DC 20515','DOGGETT@HR.HOUSE.GOV'],
    ['Honourable Jennifer Dunn,<br>Of the 8th Congressional District, Washington','8th Congressional District, Washington<br>Rm. 432 Cannon House Office Building<br>Washington, DC 20515','DUNN@HR.HOUSE.GOV'],
    ['Honourable Vernon Ehlers,<br>Of the 3rd Congressional District, Michigan','3rd Congressional District, Michigan<br>Rm. 1717 Longworth House Office Building<br>Washington, DC 20515','CONGEHLR@HR.HOUSE.GOV'],
    ['Honourable Bill Emerson,<br>Of the 8th Congressional District, Missouri','8th Congressional District, Missouri<br>Rm.2268 Rayburn House Office Building<br>Washington, D.C. 20515','BEMERSON@HR.HOUSE.GOV'],
    ['Honourable Eliot Engel,<br>Of the 17th Congressional District, New York','17th Congressional District, New York<br>Rm.1433 Longworth House Office Building<br>Washington, D.C. 20515','ENGELINE@HR.HOUSE.GOV'],
    ['Honourable Anna Eshoo,<br>Of the 14th Congressional District, California','14th Congressional District, California<br>Rm. 308 Cannon House Office Building<br>Washington, DC 20515','ANNAGRAM@HR.HOUSE.GOV'],
    ['Honourable Terry Everett,<br>Of the 2nd Congressional District, Alabama','2nd Congressional District, Alabama<br>Rm. 208 Cannon House Office Building<br>Washington, DC 20515','EVERETT@HR.HOUSE.GOV'],
    ['Honourable Sam Farr,<br>Of the 17th Congressional District, California','17th Congressional District, California<br>Rm. 1117 Longworth House Office Building<br>Washington, DC 20515','SAMFARR@HR.HOUSE.GOV'],
    ['Honourable Michael Forbes,<br>Of the 1st Congressional District, New York','1st Congressional District, New York<br>Rm. 502 Cannon House Office Building<br>Washington, DC 20515','MPFORBES@HR.HOUSE.GOV'],
    ['Honourable Jon Fox,<br>Of the 13th Congressional District, Pennsylvania','13th Congressional District, Pennsylvania<br>Rm. 510 Cannon House Office Building<br>Washington, DC 20515','JONFOX@HR.HOUSE.GOV'],
    ['Honourable Bob Franks,<br>Of the 7th Congressional District, New Jersey','7th Congressional District, New Jersey<br>Rm. 429 Cannon House Office Building<br>Washington, DC 20515','FRANKSNJ@HR.HOUSE.GOV'],
    ['Honourable Elizabeth Furse,<br>Of the 1st Congressional District, Oregon','1st Congressional District, Oregon<br>Rm. 316 Cannon House Office Building<br>Washington, DC 20515','FURSEOR1@HR.HOUSE.GOV'],
    ['Honourable Sam Gejdenson,<br>Of the 2nd Congressional District, Connecticut','2nd Congressional District, Connecticut<br>Rm. 2416 Rayburn House Office Building<br>Washington, DC 20515','BOZRAH@HR.HOUSE.GOV'],
    ['Honourable Newt Gingrich,<br>Of the 6th Congressional District, Georgia','6th Congressional District, Georgia<br>Rm. 2428 Rayburn House Office Building<br>Washington, DC 20515','GEORGIA6@HR.HOUSE.GOV'],
    ['Honourable Bob Goodlatte,<br>Of the 6th Congressional District, Virginia','6th Congressional District, Virginia<br>Rm. 123 Cannon House Office Building<br>Washington, DC 20515','TALK2BOB@HR.HOUSE.GOV'],
    ['Honourable Gene Green,<br>Of the 29th Congressional District, Texas','29th Congressional District, Texas<br>Rm. 1024 Longworth House Office Building<br>Washington, DC 20515','GGREEN@HR.HOUSE.GOV'],
    ['Honourable Gil Gutknecht,<br>Of the 1st Congressional District, Minnesota','1st Congressional District, Minnesota<br>Rm. 425 Cannon House Office Building<br>Washington, DC 20515','GIL@HR.HOUSE.GOV'],
    ['Honourable Jane Harman,<br>Of the 36th Congressional District, California','36th Congressional District, California<br>Rm. 325 Cannon House Office Building<br>Washington, D.C. 20515','JHARMAN@HR.HOUSE.GOV'],
    ['Honourable Dennis Hastert,<br>Of the 14th Congressional District, Illinois','14th Congressional District, Illinois<br>Rm. 2453 Rayburn House Office Building<br>Washington, DC 20515','DHASTERT@HR.HOUSE.GOV'],
    ['Honourable Alcee Hastings,<br>Of the 23rd Congressional District, Florida','23rd Congressional District, Florida<br>Rm. 1039 Longworth House Office Building<br>Washington, D.C. 20515','HASTINGS@HR.HOUSE.GOV'],
    ['Honourable Frederick Heineman,<br>Of the 4th Congressional District, North Carolina','4th Congressional District, North Carolina<br>Rm. 1440 Longworth House Office Building<br>Washington, DC 20515','THECHIEF@HR.HOUSE.GOV'],
    ['Honourable Martin Hoke,<br>Of the 10th Congressional District, Ohio','10th Congressional District, Ohio<br>Rm. 212 Cannon House Office Building<br>Washington, DC 20515','HOKEMAIL@HR.HOUSE.GOV'],
    ['Honourable Ernest J. Istook, Jr.,<br>Of the 5th Congressional District, Oklahoma','5th Congressional District, Oklahoma<br>Rm. 119 Cannon House Office Building<br>Washington, DC 20515','ISTOOK@HR.HOUSE.GOV'],
    ['Honourable Sam Johnson,<br>Of the 3rd Congressional District, Texas','3rd Congressional District, Texas<br>Rm. 1030 Longworth House Office Building<br>Washington, DC 20515','SAMTX03@HR.HOUSE.GOV'],
    ['Honourable Tom Lantos,<br>Of the 12th Congressional District, California','12th Congressional District, California<br>Rm. 2217 Rayburn House Office Building<br>Washington, DC 20515','TALK2TOM@HR.HOUSE.GOV'],
    ['Honourable Rick Lazio,<br>Of the 2nd Congressional District, New York','2nd Congressional District, New York<br>Rm. 314 Cannon House Office Building<br>Washington, D.C. 20515','LAZIO@HR.HOUSE.GOV'],
    ['Honourable John Linder,<br>Of the 4th Congressional District, Georgia','4th Congressional District, Georgia<br>Rm. 1318 Longworth House Office Building<br>Washington, D.C. 20515','JLINDER@HR.HOUSE.GOV'],
    ['Honourable Bill Luther,<br>Of the 6th Congressional District, Minnesota','6th Congressional District, Minnesota<br>Rm. 1419 Longworth House Office Building<br>Washington, D.C. 20515','TELLBILL@HR.HOUSE.GOV'],
    ['Honourable Thomas Manton,<br>Of the 7th Congressional District, New York','7th Congressional District, New York<br>Rm. 2235 Rayburn House Office Building<br>Washington, DC 20515','TMANTON@HR.HOUSE.GOV'],
    ['Honourable Paul McHale,<br>Of the 15th Congressional District, Pennsylvania','15th Congressional District, Pennsylvania<br>Rm. 217 Cannon House Office Building<br>Washington, DC 20515','MCHALE@HR.HOUSE.GOV'],
    ['Honourable Howard McKeon,<br>Of the 25th Congressional District, California','25th Congressional District, California<br>Rm. 307 Cannon House Office Building<br>Washington, DC 20515','TELLBUCK@HR.HOUSE.GOV'],
    ['Honourable George Miller,<br>Of the 7th Congressional District, California','7th Congressional District, California<br>Rm. 2205 Rayburn House Office Building<br>Washington, DC 20515','GMILLER@HR.HOUSE.GOV'],
    ['Honourable Norman Y. Mineta,<br>Of the 15th Congressional District, California','15th Congressional District, California<br>Rm. 2221 Rayburn House Office Building<br>Washington, D.C. 20515','TELLNORM@HR.HOUSE.GOV'],
    ['Honourable David Minge,<br>Of the 2nd Congressional District, Minnesota','2nd Congressional District, Minnesota<br>1415 Longworth House Office Building<br>Washington, D.C. 20515','DMINGE@HR.HOUSE.GOV'],
    ['Honourable Sue Myrick,<br>Of the 9th Congressional District, North Carolina','9th Congressional District, North Carolina<br>Rm. 509 Cannon House Office Building<br>Washington, DC 20515','MYRICK@HR.HOUSE.GOV'],
    ['Honourable Charlie Norwood,<br>Of the 10th Congressional District','10th Congressional District, Georgia<br>Rm. 1707 Longworth House Office Building<br>Washington, DC 20515','GA10@HR.HOUSE.GOV'],
    ['Honourable Bill Orton,<br>Of the 3rd Congressional District, Utah','3rd Congressional District, Utah<br>440 Cannon House Office Building<br>Washington, DC 20515','ORTONUT3@HR.HOUSE.GOV'],
    ['Honourable Ron Packard,<br>Of the 48th Congressional District, California','48th Congressional District, California<br>2162 Rayburn House Office Building<br>Washington, DC 20515','RPACKARD@HR.HOUSE.GOV'],
    ['Honourable Ed Pastor,<br>Of the 2nd Congressional District, Arizona','2nd Congressional District, Arizona<br>Rm. 223 Cannon House Office Building<br>Washington, DC 20515','EDPASTOR@HR.HOUSE.GOV'],
    ['Honourable Nancy Pelosi,<br>Of the 8th Congressional District, California','8th Congressional District, California<br>Rm. 2457 Rayburn House Office Building<br>Washington, DC 20515','SFNANCY@HR.HOUSE.GOV'],
    ['Honourable Colin Peterson,<br>Of the 7th Congressional District, Minnesota','7th Congressional District, Minnesota<br>Rm. 1314 Longworth House Office Building<br>Washington, DC 20515','TOCOLLIN@HR.HOUSE.GOV'],
    ['Honourable Owen Pickett,<br>Of the 2nd Congressional District, Virginia','2nd Congressional District, Virginia<br>Rm. 2430 Rayburn House Office Building<br>Washington, DC 20515','OPICKETT@HR.HOUSE.GOV'],
    ['Honourable Earl Pomeroy,<br>Of North Dakota','North Dakota, At Large<br>Rm. 1533 Longworth House Office Building<br>Washington, DC 20515','EPOMEROY@HR.HOUSE.GOV'],
    ['Honourable Rob Portman,<br>Of the 2nd Congressional District, Ohio','2nd Congressional District, Ohio<br>Rm. 238 Cannon House Office Building<br>Washington, D.C. 20515','PORTMAIL@HR.HOUSE.GOV'],
    ['Honourable Jim Ramstad,<br>Of the 3rd Congressional District, Minnesota','3rd Congressional District, Minnesota<br>Rm. 103 Cannon House Office Building<br>Washington, DC 20515','MN03@HR.HOUSE.GOV'],
    ['Honourable Pat Roberts,<br>Of the 1st Congressional District, Kansas','1st Congressional District, Kansas<br>Rm. 1126 Longworth House Office Building<br>Washington, DC 20515','EMAILPAT@HR.HOUSE.GOV'],
    ['Honourable Charlie Rose,<br>Of the 7th Congressional District, North Carolina','7th Congressional District, North Carolina<br>Rm. 242 Cannon House Office Building<br>Washington, DC 20515','CROSE@HR.HOUSE.GOV'],
    ['Honourable Dan Schaefer,<br>Of the 6th Congressional District, Colorado','6th Congressional District, Colorado<br>Rm. 2353 Rayburn House Office Building<br>Washington, D.C. 20515','SCHAEFER@HR.HOUSE.GOV'],
    ['Honourable Jose Serrano,<br>Of the 16th Congressional District, New York','16th Congressional District, New York<br>Rm. 2342 Rayburn House Office Building<br>Washington, DC 20515','JSERRANO@HR.HOUSE.GOV'],
    ['Honourable Christopher Shays,<br>Of the 4th Congressional District, Connecticut','4th Congressional District, Connecticut<br>Rm. 1502 Longworth House Office Building<br>Washington, DC 20515','CSHAYS@HR.HOUSE.GOV'],
    ['Honourable David Skaggs,<br>Of the 2nd Congressional District, Colorado','2nd Congressional District, Colorado<br>Rm. 1124 Longworth House Office Building<br>Washington, DC 20515','SKAGGS@HR.HOUSE.GOV'],
    ['Honourable Linda Smith,<br>Of the 3rd Congressional District, Washington','3rd Congressional District, Washington<br>Rm. 1217 Longworth House Office Building<br>Washington, DC 20515','ASKLINDA@HR.HOUSE.GOV'],
    ['Honourable Nick Smith,<br>Of the 7th Congressional District, Michigan','7th Congressional District, Michigan<br>Rm. 1530 Longworth House Office Building<br>Washington, DC 20515','REPSMITH@HR.HOUSE.GOV'],
    ['Honourable John Spratt,<br>Of the 5th Congressional District, South Carolina','5th Congressional District, South Carolina<br>Rm. 1536 Longworth House Office Building<br>Washington, DC 20515','JSPRATT@HR.HOUSE.GOV'],
    ['Honourable \'Pete\' Stark,<br>Of the 13th Congressional District, California','13th Congressional District, California<br>Rm. 239 Cannon House Office Building<br>Washington, DC 20515','PETEMAIL@HR.HOUSE.GOV'],
    ['Honourable Cliff Stearns,<br>Of the 6th Congressional District, Florida','6th Congressional District, Florida<br>Rm. 2352 Rayburn House Office Building<br>Washington, DC 20515','CSTEARNS@HR.HOUSE.GOV'],
    ['Honourable Randy Tate,<br>Of the 9th Congressional District, Washington','9th Congressional District, Washington<br>Rm. 1118 Longworth House Office Building<br>Washington, DC 20515','RTATE@HR.HOUSE.GOV'],
    ['Honourable Charles Taylor,<br>Of the 11th Congressional District, North Carolina','11th Congressional District, North Carolina<br>Rm. 231 Cannon House Office Building<br>Washington, DC 20515','CHTAYLOR@HR.HOUSE.GOV'],
    ['Honourable Karen Thurman,<br>Of the 5th Congressional District, Florida','5th Congressional District, Florida<br>Rm. 130 Cannon House Office Building<br>Washington, DC 20515','KTHURMAN@HR.HOUSE.GOV'],
    ['Honourable Peter Torkildsen,<br>Of the 6th Congressional District, Massachusetts','6th Congressional District, Massachusetts<br>Rm. 120 Cannon House Office Building<br>Washington, DC 20515','TORKMA06@HR.HOUSE.GOV'],
    ['Honourable Walter R. Tucker, III, <br>Of the 37th Congressional District, California','37th Congressional District, California<br>Rm. 419 Cannon House Office Building<br>Washington, DC 20515','TUCKER96@HR.HOUSE.GOV'],
    ['Honourable Bruce Vento,<br>Of the 4th Congressional District, Minnesota','4th Congressional District, Minnesota<br>Rm. 2304 Rayburn House Office Building<br>Washington, DC 20515','VENTO@HR.HOUSE.GOV'],
    ['Honourable Enid Waldholtz,<br>Of the 2nd Congressional District, Utah','2nd Congressional District, Utah<br>Rm. 2515 Cannon House Office Building<br>Washington, DC 20515','ENIDUTAH@HR.HOUSE.GOV'],
    ['Honourable Robert Walker,<br>Of the 16th Congressional District, Pennsylvania','16th Congressional District, Pennsylvania<br>Rm. 2369 Rayburn House Office Building<br>Washington, DC 20515','PA16@HR.HOUSE.GOV'],
    ['Honourable Mel Watt,<br>Of the 12th Congressional District, North Carolina','12th Congressional District, North Carolina<br>Rm. 1230 Longworth House Office Building<br>Washington, DC 20515','MELMAIL@HR.HOUSE.GOV'],
    ['Honourable Rick White,<br>Of the 1st Congressional District, Washington','1st Congressional District, Washington<br>Rm. 116 Cannon House Office Building<br>Washington, D.C. 20515','REPWHITE@HR.HOUSE.GOV'],
    ['Honourable Ed Whitfield,<br>Of the 1st Congressional District, Kentucky','1st Congressional District, Kentucky<br>Rm. 1541 Longworth House Office Building<br>Washington, D.C. 20515','EDKY01@HR.HOUSE.GOV'],
    ['Honourable Charles Wilson,<br>Of the 2nd Congressional District, Texas','2nd Congressional District, Texas<br>Rm. 2256 Rayburn House Office Building<br>Washington, D.C. 20515','CWILSON@HR.HOUSE.GOV'],
    ['Honourable Lynn C. Woolsey,<br>Of the 6th Congressional District, California','6th Congressional District, California<br>Rm. 439 Cannon House Office Building<br>Washington, D.C. 20515','WOOLSEY@HR.HOUSE.GOV'],
    ['Honourable Bill Zeliff, Jr.,<br>Of the 1st Congressional District, New Hampshire','1st Congressional District, New Hampshire<br>Rm. 1210 Longworth House Office Building<br>Washington, DC 20515','ZELIFF@HR.HOUSE.GOV'],
    ['Honourable Dick Zimmer,<br>Of the 12th Congressional District, New Jersey','12th Congressional District, New Jersey<br>Rm. 228 Cannon House Office Building<br>Washington, DC 20515','DZIMMER@HR.HOUSE.GOV']
];


function run() {
    global $arrMail;
    $count = count($arrMail);
    for ($i = 0; $i < $count; $i++) {
        $name = ucwords(strtolower(trim($arrMail[$i][0])));
        $email = trim($arrMail[$i][2]);


        // if (!strpos($name, ' ')) {
        //     $name = $name;
        // } elseif (strpos($name, ' ') < 3) {
        //     $pos = strpos($name, ' ', strpos($name, ' ') + strlen(' '));
        //     $name = substr($name, 0, $pos);
        // } else {
        //     $name = substr($name, 0, strpos($name, ' '));
        // }

        // if ($name == '') {
        //     $name = ucwords(strtolower(trim($arrMail[$i][0])));
        // }

        sendTheMail($name, $email);
        echo "$name and $email <br>";
        flush();

        sleep(4);
    }
}

run();
