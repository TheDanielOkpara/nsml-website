<?php
require_once __DIR__ . '/db.php';

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
