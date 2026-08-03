<?php

/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @since         3.0.0
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */

namespace Cake\Controller\Component;

use Cake\Controller\Component;
use Exception;

/**
 * The CakePHP FlashComponent provides a way for you to write a flash variable
 * to the session from your controllers, to be rendered in a view with the
 * FlashHelper.
 *
 * @method void success(string $message, array $options = []) Set a message using "success" element
 * @method void error(string $message, array $options = []) Set a message using "error" element
 */
class EmailComponent extends Component
{
  //error_reporting(0);

  public function send($to, $subject, $message, $cc = null, $listcompany = null)
  {
    $sender = "contact@doomshell.com";// this will be overwritten by GMail



    $message = stripslashes($message);
    // Replace sender@example.com with your "From" address.
    // This address must be verified with Amazon SES.

    /*
$ical_content = 'BEGIN:VCALENDAR
PRODID:-//Microsoft Corporation//Outlook 16.0 MIMEDIR//EN
VERSION:2.0
METHOD:PUBLISH
X-MS-OLK-FORCEINSPECTOROPEN:TRUE
BEGIN:VTIMEZONE
TZID:India Standard Time
BEGIN:STANDARD
DTSTART:16010101T000000
TZOFFSETFROM:+0530
TZOFFSETTO:+0530
END:STANDARD
END:VTIMEZONE
BEGIN:VEVENT
CLASS:PUBLIC
CREATED:20191203T124654Z
DESCRIPTION:Cumbre LATAM 2019\n
DTEND;TZID="India Standard Time":20200730T000000
DTSTAMP:20191203T124654Z
DTSTART;TZID="India Standard Time":20200730T000000
LAST-MODIFIED:20191203T124654Z
LOCATION:eboxtenders.com
PRIORITY:5
SEQUENCE:0
SUMMARY;LANGUAGE=es:Auction
TRANSP:OPAQUE
UID:040000008200E00074C5B7101A82E00800000000A06260CDADA9D501000000000000000
    010000000FC95C5664F74B04B8AEB1601B7F04AD6
X-ALT-DESC;FMTTYPE=text/html:<html xmlns:v="urn:schemas-microsoft-com:vml" 
    xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:w="urn:schemas-mic
    rosoft-com:office:word" xmlns:m="http://schemas.microsoft.com/office/2004/

X-MICROSOFT-CDO-BUSYSTATUS:BUSY
X-MICROSOFT-CDO-IMPORTANCE:1
X-MICROSOFT-DISALLOW-COUNTER:FALSE
X-MS-OLK-AUTOFILLLOCATION:FALSE
X-MS-OLK-CONFTYPE:0
BEGIN:VALARM
TRIGGER:-PT15M
ACTION:DISPLAY
DESCRIPTION:Reminder
END:VALARM
END:VEVENT
END:VCALENDAR';

*/
    // Replace recipient@example.com with a "To" address. If your account
    // is still in the sandbox, this address must be verified.

    $recipient = $to;

    // Replace smtp_username with your Amazon SES SMTP user name.
    $usernameSmtp = 'contact@doomshell.com';

    // Replace smtp_password with your Amazon SES SMTP password.
    $passwordSmtp = 'zxrz ypql enpw npxo';

    //$host = 'email-smtp.ap-south-1.amazonaws.com';
    $host = 'smtp.gmail.com';
    $port = 587;
    $mail = new \PHPMailer(true);
    $senderName = "Operify";

    try {
      // Specify the SMTP settings.
      $mail->isSMTP();
      $mail->setFrom($sender, $senderName);
      $mail->Username   = $usernameSmtp;
      $mail->Password   = $passwordSmtp;
      $mail->Host       = $host;
      $mail->Port       = $port;
      $mail->SMTPAuth   = true;
      $mail->SMTPSecure = 'tls';
      //	$mail->SMTPDebug = '4';
      //$mail->addCustomHeader('X-SES-CONFIGURATION-SET', $configurationSet);
      if ($cc) {
        $mail->addCC($cc);
      }

      // Specify the message recipients.
      $mail->addAddress($recipient);
      // You can also add CC, BCC, and additional To recipients here.

      // Specify the content of the message.
      $mail->isHTML(true);
      $mail->Subject    = $subject;
      $mail->Body       = $message;
      $mail->AltBody    = $message;
      // $mail->addStringAttachment($ical_content,'ical.ics','base64','text/calendar');
      $mail->Send();
      $rt = "1";
    } catch (phpmailerException $e) {
      $rt = "2"; //Catch errors from PHPMailer.
    } catch (Exception $e) {
      $rt = "2"; //Catch errors from Amazon SES.
    }
    return $rt;
  }
}
