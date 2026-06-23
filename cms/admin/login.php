<?php
require_once __DIR__ . '/../includes/auth.php';

if (current_admin()) {
    header('Location: blog.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    if (attempt_login($_POST['username'] ?? '', $_POST['password'] ?? '')) {
        header('Location: blog.php');
        exit;
    }
    $error = 'Invalid username or password.';
}
$token = csrf_token();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Log in — NSML CMS</title>
<style>
  body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background:#0d1f3c; height:100vh; margin:0; display:flex; align-items:center; justify-content:center; }
  form { background:#fff; padding:2.5rem; border-radius:0.75rem; width:320px; }
  h1 { font-size:1.25rem; margin:0 0 1.5rem; }
  .field { margin-bottom:1rem; }
  label { display:block; font-size:0.85rem; font-weight:600; margin-bottom:0.35rem; }
  input { width:100%; padding:0.6rem 0.7rem; border:1px solid #e3e7ee; border-radius:0.4rem; font-size:0.9rem; box-sizing:border-box; }
  button { width:100%; background:#1f9d55; color:#fff; border:none; padding:0.7rem; border-radius:0.4rem; font-size:0.95rem; cursor:pointer; margin-top:0.5rem; }
  .error { background:#fdecea; color:#922b21; padding:0.6rem 0.8rem; border-radius:0.4rem; font-size:0.85rem; margin-bottom:1rem; }
</style>
</head>
<body>
<form method="post">
  <h1>NSML CMS Login</h1>
  <?php if ($error): ?><div class="error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
  <input type="hidden" name="csrf" value="<?= htmlspecialchars($token) ?>">
  <div class="field"><label>Username</label><input type="text" name="username" required autofocus></div>
  <div class="field"><label>Password</label><input type="password" name="password" required></div>
  <button type="submit">Log in</button>
</form>
</body>
</html>
