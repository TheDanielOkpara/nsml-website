<?php
require_once __DIR__ . '/../includes/auth.php';

if (current_admin()) {
    header('Location: index.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    if (attempt_login($_POST['username'] ?? '', $_POST['password'] ?? '')) {
        header('Location: index.php');
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
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Log in — NSML CMS</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
  * { box-sizing: border-box; }
  body {
    font-family:'Inter',-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;
    background:#f5f6f9; color:#19213a;
    height:100vh; margin:0; display:flex; align-items:center; justify-content:center; padding:1.5rem;
    -webkit-font-smoothing:antialiased;
  }
  .login-card { background:#fff; padding:2.25rem; border-radius:13px; width:360px; max-width:100%; border:1px solid #e6e8ef; box-shadow:0 12px 40px -12px rgba(13,31,60,0.18); }
  .login-mark { height:36px; margin-bottom:1.375rem; }
  .login-mark img { height:100%; width:auto; display:block; }
  h1 { font-size:1.1875rem; font-weight:700; letter-spacing:-0.02em; margin:0 0 0.25rem; color:#16213d; }
  .sub { font-size:0.875rem; color:#525c75; margin:0 0 1.5rem; }
  .field { margin-bottom:1rem; }
  label { display:block; font-size:0.8125rem; font-weight:600; margin-bottom:0.4rem; color:#19213a; }
  input { width:100%; padding:0.6rem 0.75rem; border:1px solid #d6dae3; border-radius:7px; font-size:0.9rem; font-family:inherit; outline:none; color:#19213a; transition:border-color 0.16s, box-shadow 0.16s; }
  input:focus { border-color:#1f9d55; box-shadow:0 0 0 3px rgba(31,157,85,0.12); }
  button { width:100%; background:#1f9d55; color:#fff; border:none; padding:0.7rem; border-radius:7px; font-size:0.9375rem; font-weight:600; cursor:pointer; margin-top:0.625rem; font-family:inherit; transition:background 0.16s, transform 0.1s; }
  button:hover { background:#178a49; }
  button:active { transform:translateY(1px); }
  .error { background:#fdecea; color:#922b21; padding:0.65rem 0.875rem; border-radius:7px; font-size:0.8125rem; margin-bottom:1.25rem; font-weight:500; }
</style>
</head>
<body>
<form method="post" class="login-card">
  <div class="login-mark"><img src="/images/logo.png" alt="NSML"></div>
  <h1>Welcome back</h1>
  <p class="sub">Sign in to manage NSML's site content.</p>
  <?php if ($error): ?><div class="error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
  <input type="hidden" name="csrf" value="<?= htmlspecialchars($token) ?>">
  <div class="field"><label>Username</label><input type="text" name="username" required autofocus></div>
  <div class="field"><label>Password</label><input type="password" name="password" required></div>
  <button type="submit">Log in</button>
</form>
</body>
</html>
