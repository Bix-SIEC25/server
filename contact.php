<?php
// contact.php -- simple POST handler
// SECURITY NOTE: This is a minimal example. In production validate/sanitize and add anti-spam (CSRF, CAPTCHA), and use SMTP with auth.

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo "Method not allowed";
    exit;
}

$name = substr(trim($_POST['name'] ?? 'No name'), 0, 200);
$email = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
$role = substr(trim($_POST['role'] ?? ''), 0, 200);
$message = substr(trim($_POST['message'] ?? ''), 0, 2000);

if (!$email) {
    http_response_code(400);
    echo "Invalid email";
    exit;
}

$to = "hello@bix.ovh"; // <-- CHANGE THIS to your real address
$subject = "Bix site contact from {$name} ({$role})";
$body = "Name: {$name}\nEmail: {$email}\nRole: {$role}\n\nMessage:\n{$message}\n\n---\nSent: " . date('c');

$headers = "From: no-reply@bix.ovh\r\n";
$headers .= "Reply-To: {$email}\r\n";

if (mail($to, $subject, $body, $headers)) {
    echo "OK";
} else {
    http_response_code(500);
    echo "Oops, the mail doesn't seem to have been delivered";
}
