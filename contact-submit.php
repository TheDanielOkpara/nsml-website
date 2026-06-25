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

// Email notification to the NSML inbox. Stored-in-DB already succeeded, so a
// mail failure never fails the request — but we make delivery as reliable as
// cPanel/Exim allows.
$fullName = trim($first . ' ' . $last);
$subject  = "New enquiry: {$interest} — {$fullName}";
$body = "You have a new enquiry from the nilayosports.com contact form.\n\n"
      . "Name:     {$fullName}\n"
      . "Email:    {$email}\n"
      . "Phone:    " . ($phone !== '' ? $phone : '—') . "\n"
      . "Interest: {$interest}\n\n"
      . "Message:\n{$message}\n\n"
      . "--\nReply directly to this email to respond to {$first}.\n";

$fromAddr = CONTACT_NOTIFY_FROM;
$headers  = implode("\r\n", [
    'From: NSML Website <' . $fromAddr . '>',
    // Reply-To is the visitor, so staff can reply straight from their inbox.
    'Reply-To: ' . $fullName . ' <' . $email . '>',
    'MIME-Version: 1.0',
    'Content-Type: text/plain; charset=UTF-8',
    'X-Mailer: NSML-Contact',
]);

// The 5th arg (-f envelope sender) sets a proper Return-Path so Exim doesn't
// fall back to the web-server user, which is the usual reason cPanel mail()
// notifications get spam-filtered or silently dropped. RFC2047-encode the
// subject so names with accents/symbols survive.
$encodedSubject = '=?UTF-8?B?' . base64_encode($subject) . '?=';
@mail(CONTACT_NOTIFY_TO, $encodedSubject, $body, $headers, '-f' . $fromAddr);

echo json_encode(['ok' => true]);
