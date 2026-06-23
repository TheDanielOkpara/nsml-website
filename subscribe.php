<?php
// Newsletter signup endpoint — stores the email in the subscribers table.
// Returns JSON for the page's fetch() to show its success state.
require_once __DIR__ . '/cms/includes/db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'error' => 'Method not allowed']);
    exit;
}

$email = trim($_POST['email'] ?? '');
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(422);
    echo json_encode(['ok' => false, 'error' => 'Please enter a valid email address.']);
    exit;
}

try {
    // Re-subscribe if the address exists but was deactivated; otherwise insert.
    $stmt = db()->prepare(
        'INSERT INTO subscribers (email, ip_address) VALUES (?, ?)
         ON DUPLICATE KEY UPDATE is_active = 1'
    );
    $stmt->execute([mb_substr($email, 0, 255), $_SERVER['REMOTE_ADDR'] ?? null]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'Could not subscribe. Please try again.']);
    exit;
}

echo json_encode(['ok' => true]);
