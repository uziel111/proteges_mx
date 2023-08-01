<?php
/*
Name: 			Contact Form
Written by: 	Okler Themes - (http://www.okler.net)
Theme Version:	9.7.0
*/

namespace PortoContactForm;

session_cache_limiter('nocache');
header('Expires: ' . gmdate('r', 0));

header('Content-type: application/json');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'php-mailer/src/PHPMailer.php';
require 'php-mailer/src/SMTP.php';
require 'php-mailer/src/Exception.php';

// Step 1 - Enter your email address below.
$email = $_POST['email'];

// If the e-mail is not working, change the debug option to 2 | $debug = 2;
$debug = 0;

// If contact form don't has the subject input change the value of subject here
$subject = (isset($_POST['subject'])) ? $_POST['subject'] : 'Define subject in php/contact-form.php line 29';




if ($_SERVER["REQUEST_METHOD"] == "POST")
{
	$name = test_input($_POST["name"]);
	$email = test_input($_POST["email"]);
	$website = test_input($_POST["subject"]);
	$comment = test_input($_POST["message"]);
}

$pattern_correo = "/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/";
$pattern_nombre = "/^[a-zA-Zá-úÁ-Ú\s]+$/";
$pattern_asunto = "/^[\wá-úÁ-Ú\s]+$/";
$pattern_mensaje = "/^[\wá-úÁ-Ú\s.,!?]+$/";


if (!preg_match($pattern_correo, $email))
{
	header('Location: ../contacto.php?status=3');
	exit;
}
if (!preg_match($pattern_nombre, $name))
{
	header('Location: ../contacto.php?status=2');
	exit;
}
if (!preg_match($pattern_asunto, $website))
{
	header('Location: ../contacto.php?status=4');
	exit;
}
if (!preg_match($pattern_mensaje, $comment))
{
	header('Location: ../contacto.php?status=5');
	exit;
}

// echo "<pre>";
// print_r($_POST);
// die();

$message = '<h1>Gracias por contactarnos a través del sitio web de proteges.mx</h1>
    <p>Hemos recibido su mensaje y le confirmamos que se ha enviado un correo electrónico con los detalles de su consulta. Nuestro equipo revisará su mensaje detalladamente y le daremos seguimiento en breve.</p>
    <p>Agradecemos su interés y la oportunidad de atender sus inquietudes. Si tiene alguna otra pregunta o necesita asistencia adicional, no dude en contactarnos nuevamente.</p>
    <p>¡Gracias por elegir proteges.mx!</p><br>';

foreach ($_POST as $label => $value)
{
	$label = ucwords($label);

	// Use the commented code below to change label texts. On this example will change "Email" to "Email Address"

	if ($label == 'Email')
	{
		$label = 'Correo electrónico';
	}
	if ($label == 'Name')
	{
		$label = 'Nombre completo';
	}
	if ($label == 'Subject')
	{
		$label = 'Asunto';
	}
	if ($label == 'Message')
	{
		$label = 'Mensaje';
	}

	// Checkboxes
	if (is_array($value))
	{
		// Store new value
		$value = implode(', ', $value);
	}

	$message .= $label . ": " . nl2br(htmlspecialchars($value, ENT_QUOTES)) . "<br>";
}

$mail = new PHPMailer(true);

try
{

	$mail->SMTPDebug = $debug;                                 // Debug Mode

	// Step 2 (Optional) - If you don't receive the email, try to configure the parameters below:

	$mail->IsSMTP();                                         // Set mailer to use SMTP
	$mail->Host = 'smtp.gmail.com';				       // Specify main and backup server
	$mail->SMTPAuth = true;                                  // Enable SMTP authentication
	$mail->Username = 'info@segurosproteges.com';                    // SMTP username
	$mail->Password = '9U36nc3e	';                              // SMTP password
	$mail->SMTPSecure = 'tls';                               // Enable encryption, 'ssl' also accepted
	$mail->Port = 587;   								       // TCP port to connect to

	$mail->AddAddress($email);	 						       // Add another recipient

	// $mail->AddAddress('uziel.cap.123@gmail.com', 'Person 2');     // Add a secondary recipient
	//$mail->AddCC('person3@domain.com', 'Person 3');          // Add a "Cc" address. 
	//$mail->AddBCC('person4@domain.com', 'Person 4');         // Add a "Bcc" address. 

	// From - Name
	$fromName = (isset($_POST['name'])) ? $_POST['name'] : 'Website User';
	$mail->SetFrom($email, $fromName);

	// Repply To
	if (isset($_POST['email']) && !empty($_POST['email']))
	{
		$mail->AddReplyTo($_POST['email'], $fromName);
	}

	$mail->IsHTML(true);                                       // Set email format to HTML

	$mail->CharSet = 'UTF-8';

	$mail->Subject = $subject;
	$mail->Body    = $message;

	$mail->Send();
	$result = 1; //success
}
catch (Exception $e)
{
	$result = 6; //$e->errorMessage());
}
catch (\Exception $e)
{
	$result = 6; //$e->getMessage());
}

if ($debug == 0)
{
	// echo json_encode($result);
	header('Location: ../contacto.php?status=' . $result);
	exit;
}

function test_input($data)
{
	$data = trim($data);
	$data = stripslashes($data);
	$data = htmlspecialchars($data);
	return $data;
}
