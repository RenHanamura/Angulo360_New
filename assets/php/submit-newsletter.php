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

$email = sanitize($_POST['newsletter'] ?? $_POST['email'] ?? '');

// ── Validación ────────────────────────────────────────────────────────────────
if ($email === '') {
    echo json_encode(['status' => 'error', 'message' => 'Email required']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid email address']);
    exit;
}

// ── Credenciales SMTP (Titan Email) ───────────────────────────────────────────
define('SMTP_HOST', 'smtp.titan.email');
define('SMTP_PORT', 465);                        // 587 = TLS (STARTTLS)
define('SMTP_USER', 'contacto@angulo360.info');  // tu cuenta Titan
define('SMTP_PASS', 'Angulo360!Contacto2026!');       // ← reemplaza esto
define('MAIL_FROM', 'contacto@angulo360.info');
define('MAIL_FROM_NAME', 'Angulo360');
define('MAIL_TO', 'contacto@angulo360.info');

// ── Envío con PHPMailer ───────────────────────────────────────────────────────
$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host = SMTP_HOST;
    $mail->SMTPAuth = true;
    $mail->Username = SMTP_USER;
    $mail->Password = SMTP_PASS;
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // SSL directo (puerto 465)
    $mail->Port = SMTP_PORT;
    $mail->CharSet = 'UTF-8';

    $mail->setFrom(MAIL_FROM, MAIL_FROM_NAME);
    $mail->addReplyTo($email, $email);
    $mail->addAddress(MAIL_TO);

    $mail->Subject = 'Nueva suscripción al newsletter - Angulo360';
    $mail->Body = "Nueva suscripción al newsletter:\n\nEmail: $email";

    $mail->send();
    echo json_encode(['status' => 'success']);

} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Mail error: ' . $mail->ErrorInfo]);
}
?>