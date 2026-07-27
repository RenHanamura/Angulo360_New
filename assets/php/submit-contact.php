<?php
header('Content-Type: application/json');

function sanitize($str)
{
    return htmlspecialchars(trim($str), ENT_QUOTES, 'UTF-8');
}

$name = sanitize($_POST['name'] ?? '');
$email = sanitize($_POST['email'] ?? '');
$phone = sanitize($_POST['phone'] ?? '');
$subject = sanitize($_POST['subject'] ?? '');
$projectType = sanitize($_POST['project-type'] ?? '');
$message = sanitize($_POST['message'] ?? '');

if ($name === '' || $email === '' || $subject === '' || $message === '') {
    echo json_encode(["status" => "error", "message" => "Required fields missing"]);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(["status" => "error", "message" => "Invalid email"]);
    exit;
}

$to = "contacto@angulo360.info";

$email_subject = "Formulario de contacto: $subject";

$email_body = "
Nuevo mensaje recibido del formulario de contacto:

Name: $name
Email: $email
Phone: $phone
Project Type: $projectType

Message:
$message
";

$headers = "From: $email\r\nReply-To: $email\r\n";

if (mail($to, $email_subject, $email_body, $headers)) {
    echo json_encode(["status" => "success", "message" => "Form submitted successfully"]);
} else {
    echo json_encode(["status" => "error", "message" => "Mail sending failed"]);
}
?>