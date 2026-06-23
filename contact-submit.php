<?php
// Contact form endpoint — stores the submission and emails a notification.
// Returns JSON so the page's fetch() can show its success state.
require_once __DIR__ . '/cms/includes/db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'error' => 'Method not allowed']);
    exit;
}

$first    = trim($_POST['fname'] ?? '');
$last     = trim($_POST['lname'] ?? '');
$email    = trim($_POST['email'] ?? '');
$phone    = trim($_POST['phone'] ?? '');
$interest = trim($_POST['interest'] ?? '');
$message  = trim($_POST['message'] ?? '');

// Server-side validation — never trust the client.
$errors = [];
if ($first === '')                              $errors[] = 'First name is required.';
if ($last === '')                               $errors[] = 'Last name is required.';
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'A valid email is required.';
if ($interest === '')                           $errors[] = 'Please select an area of interest.';
if ($message === '')                            $errors[] = 'A message is required.';

if ($errors) {
    http_response_code(422);
    echo json_encode(['ok' => false, 'error' => implode(' ', $errors)]);
    exit;
}

try {
    $stmt = db()->prepare(
        'INSERT INTO contact_submissions (first_name, last_name, email, phone, interest, message, ip_address)
         VALUES (?, ?, ?, ?, ?, ?, ?)'
    );
    $stmt->execute([
        mb_substr($first, 0, 120), mb_substr($last, 0, 120), mb_substr($email, 0, 255),
        mb_substr($phone, 0, 60), mb_substr($interest, 0, 120), $message,
        $_SERVER['REMOTE_ADDR'] ?? null,
    ]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'Could not save your message. Please try again.']);
    exit;
}

// Best-effort email notification — failure here doesn't fail the submission.
$subject = "New contact enquiry: {$interest}";
$body = "New enquiry from the NSML website:\n\n"
      . "Name: {$first} {$last}\n"
      . "Email: {$email}\n"
      . "Phone: {$phone}\n"
      . "Interest: {$interest}\n\n"
      . "Message:\n{$message}\n";
$headers = 'From: ' . CONTACT_NOTIFY_FROM . "\r\n"
         . 'Reply-To: ' . $email . "\r\n"
         . 'Content-Type: text/plain; charset=utf-8';
@mail(CONTACT_NOTIFY_TO, $subject, $body, $headers);

echo json_encode(['ok' => true]);
