<?php
/**
 * This example shows settings to use when sending via Google's Gmail servers.
 */

//SMTP needs accurate times, and the PHP time zone MUST be set
//This should be done in your php.ini, but this is how to do it if you don't have access to that
date_default_timezone_set('Etc/UTC');

require 'PHPMailerAutoload.php';

// Clean up the input values
	foreach($_POST as $key => $value) {
		if(ini_get('magic_quotes_gpc'))
			$_POST[$key] = stripslashes($_POST[$key]);
		
		$_POST[$key] = htmlspecialchars(strip_tags($_POST[$key]));
	}
	
	// Assign the input values to variables for easy reference
	$name = $_POST["name"];
	$email = $_POST["email"];
	$subject = $_POST["subject"];
	$message = $_POST["message"];
	
	// Check input values for errors
	$errors = array();
	if(strlen($name) < 2) {
		if(!$name) {
			$errors[] = "Prosze wprowadź imię!";
		} else {
			$errors[] = "Przynajmniej dwa znaki!";
		}
	}
	if(!$email) {
		$errors[] = "Proszę wprowadź email!";
	} else if(!validEmail($email)) {
		$errors[] = "Proszę wprowadź poprawy adres email!";
	}
	if(strlen($message) < 10) {
		if(!$message) {
			$errors[] = "Proszę wprowadź wiadomość!";
		} else {
			$errors[] = "Minimum 10 znaków!";
		}
	}
	
	// Output error message(s)
	if($errors) {
		$errortext = "";
		foreach($errors as $error) {
			$errortext .= "<li>".$error."</li>";
		}
		die("<ul class='errors arrowed'>". $errortext ."</ul>
			<a href='javascript:history.go(0)' class='btn'><i class='icon-left-1'></i> Back</a>");
	}
	
	// Send the email
	if($subject!=""){
		$subject = "Contact Form: $subject";
	}
	else {
		$subject = "Contact Form: $name";
	}


//Create a new PHPMailer instance
$mail = new PHPMailer;

//Tell PHPMailer to use SMTP
$mail->isSMTP();

//Enable SMTP debugging
// 0 = off (for production use)
// 1 = client messages
// 2 = client and server messages
$mail->SMTPDebug = 0;

//Ask for HTML-friendly debug output
$mail->Debugoutput = 'html';

//Set the hostname of the mail server
$mail->Host = 'smtp.gmail.com';
// use
// $mail->Host = gethostbyname('smtp.gmail.com');
// if your network does not support SMTP over IPv6

//Set the SMTP port number - 587 for authenticated TLS, a.k.a. RFC4409 SMTP submission
$mail->Port = 587;

//Set the encryption system to use - ssl (deprecated) or tls
$mail->SMTPSecure = 'tls';

//Whether to use SMTP authentication
$mail->SMTPAuth = true;

//Username to use for SMTP authentication - use full email address for gmail
$mail->Username = "alexwebdesigncontactform@gmail.com";

//Password to use for SMTP authentication
$mail->Password = "1234qwerzxcv)(*&";

//Set who the message is to be sent from
$mail->setFrom($email, $name);

//Set an alternative reply-to address
$mail->addReplyTo($email, $name);

//Set who the message is to be sent to
$mail->addAddress('cezary@alexwebdesign.pl', 'Cezary Brzozowski');

//Set the subject line
$mail->Subject = $subject;

//Read an HTML message body from an external file, convert referenced images to embedded,
//convert HTML into a basic plain-text alternative body
$mail->msgHTML($message);
//Replace the plain text body with one created manually
$mail->AltBody = $message;


//send the message, check for errors
if (!$mail->send()) {
    die("<p class='success'>Pojawił się bład. Spróbuj wysłać email na adres cezary@alexwebdesign.pl !</p>");
} else {
    die("<p class='success'>Dziękuję! – Wiadomość została wysłana!</p>");
}
function validEmail($email) {
		$isValid = true;
		$atIndex = strrpos($email, "@");
		if (is_bool($atIndex) && !$atIndex)	{
			$isValid = false;
		}
		else {
			$domain = substr($email, $atIndex+1);
			$local = substr($email, 0, $atIndex);
			$localLen = strlen($local);
			$domainLen = strlen($domain);
			if ($localLen < 1 || $localLen > 64) {
				// Local part length exceeded
				$isValid = false;
			}
			else if ($domainLen < 1 || $domainLen > 255) {
				// Domain part length exceeded
				$isValid = false;
			}
			else if ($local[0] == '.' || $local[$localLen-1] == '.') {
				// Local part starts or ends with '.'
				$isValid = false;
			}
			else if (preg_match('/\\.\\./', $local)) {
				// Local part has two consecutive dots
				$isValid = false;
			}
			else if (!preg_match('/^[A-Za-z0-9\\-\\.]+$/', $domain)) {
				// Character not valid in domain part
				$isValid = false;
			}
			else if (preg_match('/\\.\\./', $domain)) {
				// Domain part has two consecutive dots
				$isValid = false;
			}
			else if(!preg_match('/^(\\\\.|[A-Za-z0-9!#%&`_=\\/$\'*+?^{}|~.-])+$/',
			str_replace("\\\\","",$local))) {
				// Character not valid in local part unless local part is quoted
				if (!preg_match('/^"(\\\\"|[^"])+"$/',
				str_replace("\\\\","",$local))) {
					$isValid = false;
				}
			}
			if ($isValid && !(checkdnsrr($domain,"MX") || checkdnsrr($domain,"A"))) {
				// Domain not found in DNS
				$isValid = false;
			}
		}
		return $isValid;
	}
?>
