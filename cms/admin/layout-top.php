<?php
require_once __DIR__ . '/../includes/auth.php';
require_login();
$admin = current_admin();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($pageTitle ?? 'Admin') ?> — NSML CMS</title>
<style>
  :root { --navy:#0d1f3c; --green:#1f9d55; --border:#e3e7ee; --bg:#f6f8fb; }
  * { box-sizing: border-box; }
  body { margin:0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: var(--bg); color:#111; }
  .shell { display:flex; min-height:100vh; }
  nav.side { width:220px; background:var(--navy); color:#fff; padding:1.5rem 1rem; flex-shrink:0; }
  nav.side a { display:block; color:rgba(255,255,255,0.75); text-decoration:none; padding:0.6rem 0.75rem; border-radius:0.5rem; font-size:0.9rem; margin-bottom:0.25rem; }
  nav.side a:hover, nav.side a.active { background:rgba(255,255,255,0.1); color:#fff; }
  nav.side .brand { font-weight:700; font-size:1.1rem; margin-bottom:1.5rem; color:#fff; }
  main { flex:1; padding:2rem 2.5rem; max-width:1100px; }
  h1 { font-size:1.5rem; margin:0 0 1.5rem; }
  table { width:100%; border-collapse:collapse; background:#fff; border:1px solid var(--border); border-radius:0.5rem; overflow:hidden; }
  th, td { padding:0.75rem 1rem; text-align:left; border-bottom:1px solid var(--border); font-size:0.9rem; }
  th { background:#fafbfc; font-weight:600; }
  .btn { display:inline-block; background:var(--green); color:#fff; padding:0.5rem 1rem; border-radius:0.4rem; text-decoration:none; font-size:0.875rem; border:none; cursor:pointer; }
  .btn.secondary { background:#fff; color:var(--navy); border:1px solid var(--border); }
  .btn.danger { background:#c0392b; }
  form.inline { display:inline; }
  .field { margin-bottom:1.1rem; }
  .field label { display:block; font-size:0.85rem; font-weight:600; margin-bottom:0.35rem; }
  .field input, .field textarea, .field select { width:100%; padding:0.55rem 0.7rem; border:1px solid var(--border); border-radius:0.4rem; font-size:0.9rem; font-family:inherit; }
  .field textarea { resize:vertical; }
  .row3 { display:grid; grid-template-columns:1fr 1fr 1fr; gap:1rem; }
  .card { background:#fff; border:1px solid var(--border); border-radius:0.6rem; padding:1.75rem; max-width:720px; }
  .topbar { display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem; }
  .flash { background:#eafaf0; border:1px solid #b7e4c7; color:#1b6b35; padding:0.75rem 1rem; border-radius:0.5rem; margin-bottom:1.25rem; font-size:0.9rem; }
  .flash.error { background:#fdecea; border-color:#f5b7b1; color:#922b21; }
  thumb { display:block; }
  img.thumb { height:42px; width:auto; border-radius:0.3rem; object-fit:cover; }
</style>
</head>
<body>
<div class="shell">
  <nav class="side">
    <div class="brand">NSML CMS</div>
    <a href="blog.php" class="<?= ($activeNav ?? '') === 'blog' ? 'active' : '' ?>">Blog Posts</a>
    <a href="properties.php" class="<?= ($activeNav ?? '') === 'properties' ? 'active' : '' ?>">Properties</a>
    <a href="team.php" class="<?= ($activeNav ?? '') === 'team' ? 'active' : '' ?>">Team</a>
    <a href="logout.php" style="margin-top:1.5rem;">Log out (<?= htmlspecialchars($admin['username']) ?>)</a>
  </nav>
  <main>
