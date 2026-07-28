<?php
header('Content-Type: application/json');

// ── Cargar PHPMailer (sin Composer) ──────────────────────────────────────────
require_once __DIR__ . '/phpmailer/Exception.php';
require_once __DIR__ . '/phpmailer/PHPMailer.php';
require_once __DIR__ . '/phpmailer/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// ── Sanitización ─────────────────────────────────────────────────────────────
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

// ── Validación básica ─────────────────────────────────────────────────────────
if ($name === '' || $email === '' || $subject === '' || $message === '') {
    echo json_encode(['status' => 'error', 'message' => 'Required fields missing']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid email']);
    exit;
}

// ── Credenciales SMTP (Titan Email) ───────────────────────────────────────────
define('SMTP_HOST', 'smtp.titan.email');
define('SMTP_PORT', 465);                       // 587 = TLS (STARTTLS)
define('SMTP_USER', 'contacto@angulo360.info'); // tu cuenta Titan
define('SMTP_PASS', 'Angulo360!Contacto2026!');      // ← reemplaza esto
define('MAIL_FROM', 'contacto@angulo360.info');
define('MAIL_FROM_NAME', 'Angulo360');
define('MAIL_TO', 'contacto@angulo360.info');

// ── Envío con PHPMailer ───────────────────────────────────────────────────────
$mail = new PHPMailer(true);

try {
    // Servidor SMTP
    $mail->isSMTP();
    $mail->Host = SMTP_HOST;
    $mail->SMTPAuth = true;
    $mail->Username = SMTP_USER;
    $mail->Password = SMTP_PASS;
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // SSL directo (puerto 465)
    $mail->Port = SMTP_PORT;
    $mail->CharSet = 'UTF-8';

    // Remitente y destinatario
    $mail->setFrom(MAIL_FROM, MAIL_FROM_NAME);
    $mail->addReplyTo($email, $name);       // "Responder a" → el visitante
    $mail->addAddress(MAIL_TO);

    // Contenido
    $mail->Subject = "Formulario de contacto: $subject";
    $mail->Body = "Nuevo mensaje del formulario de contacto:\n\n"
        . "Nombre:        $name\n"
        . "Email:         $email\n"
        . "Teléfono:      $phone\n"
        . "Tipo proyecto: $projectType\n\n"
        . "Mensaje:\n$message";

    $mail->send();
    echo json_encode(['status' => 'success', 'message' => 'Form submitted successfully']);

} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Mail error: ' . $mail->ErrorInfo]);
}
?>