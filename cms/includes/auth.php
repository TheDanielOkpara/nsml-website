<?php
require_once __DIR__ . '/db.php';

session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'secure' => !empty($_SERVER['HTTPS']),
    'httponly' => true,
    'samesite' => 'Lax',
]);
session_start();

function current_admin(): ?array {
    return $_SESSION['admin'] ?? null;
}

function require_login(): void {
    if (!current_admin()) {
        header('Location: login.php');
        exit;
    }
}

function attempt_login(string $username, string $password): bool {
    $stmt = db()->prepare('SELECT * FROM admin_users WHERE username = ?');
    $stmt->execute([$username]);
    $user = $stmt->fetch();
    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['admin'] = ['id' => $user['id'], 'username' => $user['username']];
        return true;
    }
    return false;
}

function logout(): void {
    $_SESSION = [];
    session_destroy();
}

function csrf_token(): string {
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function csrf_check(): void {
    $token = $_POST['csrf'] ?? '';
    if (!hash_equals($_SESSION['csrf'] ?? '', $token)) {
        http_response_code(403);
        die('Invalid CSRF token.');
    }
}

// Brute-force throttling for login.php, keyed by IP. Self-creates its table
// so no manual migration step is needed on deploy.
function login_is_rate_limited(string $ip, int $maxAttempts = 5, int $windowMinutes = 15): bool {
    db()->exec('CREATE TABLE IF NOT EXISTS login_attempts (
        id INT AUTO_INCREMENT PRIMARY KEY,
        ip_address VARCHAR(45) NOT NULL,
        attempted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_ip_time (ip_address, attempted_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4');

    $windowMinutes = (int) $windowMinutes;
    $stmt = db()->prepare("SELECT COUNT(*) FROM login_attempts WHERE ip_address = ? AND attempted_at > NOW() - INTERVAL $windowMinutes MINUTE");
    $stmt->execute([$ip]);
    return (int) $stmt->fetchColumn() >= $maxAttempts;
}

function login_record_failed_attempt(string $ip): void {
    db()->prepare('INSERT INTO login_attempts (ip_address) VALUES (?)')->execute([$ip]);
}

function login_clear_attempts(string $ip): void {
    db()->prepare('DELETE FROM login_attempts WHERE ip_address = ?')->execute([$ip]);
}
