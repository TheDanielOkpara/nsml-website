<?php
// One-time setup script. Visit this page once to create your first admin user,
// then DELETE this file from the server — it has no login protection.
require_once __DIR__ . '/../includes/db.php';

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    if ($username === '' || strlen($password) < 8) {
        $message = 'Username required and password must be at least 8 characters.';
    } else {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = db()->prepare('INSERT INTO admin_users (username, password_hash) VALUES (?, ?)
            ON DUPLICATE KEY UPDATE password_hash = VALUES(password_hash)');
        $stmt->execute([$username, $hash]);
        $message = "Admin user '{$username}' created/updated. Delete this file now, then log in at login.php.";
    }
}
?>
<!DOCTYPE html>
<html><head><meta charset="UTF-8"><title>Create Admin</title></head>
<body style="font-family:sans-serif;max-width:420px;margin:4rem auto;">
<h1>Create Admin User</h1>
<?php if ($message): ?><p style="background:#eafaf0;padding:0.75rem;border-radius:0.4rem;"><?= htmlspecialchars($message) ?></p><?php endif; ?>
<form method="post">
  <p><label>Username<br><input type="text" name="username" required style="width:100%;padding:0.5rem;"></label></p>
  <p><label>Password<br><input type="password" name="password" required style="width:100%;padding:0.5rem;"></label></p>
  <button type="submit" style="padding:0.6rem 1.2rem;">Create</button>
</form>
<p style="color:#922b21;"><strong>Delete this file (cms/admin/create-admin.php) immediately after use.</strong></p>
</body></html>
