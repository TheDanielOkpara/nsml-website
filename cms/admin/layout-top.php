<?php
require_once __DIR__ . '/../includes/auth.php';
require_login();
$admin = current_admin();

$navItems = [
    ['key' => 'dashboard',   'href' => 'index.php',       'label' => 'Dashboard',   'icon' => 'M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z'],
    ['key' => 'blog',        'href' => 'blog.php',        'label' => 'Blog Posts',  'icon' => 'M4 19.5A2.5 2.5 0 0 1 6.5 17H20M4 4.5A2.5 2.5 0 0 1 6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15z'],
    ['key' => 'properties',  'href' => 'properties.php',  'label' => 'Properties',  'icon' => 'M3 9.5 12 3l9 6.5V21a1 1 0 0 1-1 1h-5v-7H9v7H4a1 1 0 0 1-1-1V9.5z'],
    ['key' => 'team',        'href' => 'team.php',        'label' => 'Team',        'icon' => 'M17 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8zM22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75'],
    ['key' => 'messages',    'href' => 'messages.php',    'label' => 'Messages',    'icon' => 'M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v10z'],
    ['key' => 'subscribers', 'href' => 'subscribers.php', 'label' => 'Subscribers', 'icon' => 'M22 6 12 13 2 6m0 0v12a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2z'],
    ['key' => 'tools',       'href' => 'fix-content.php', 'label' => 'Tools',       'icon' => 'M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94L6.3 20.7a2.12 2.12 0 0 1-3-3L10.6 10.4a6 6 0 0 1 7.94-7.94L14.7 6.3z'],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($pageTitle ?? 'Admin') ?> — NSML CMS</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
  :root {
    --navy:#0d1f3c; --navy-light:#16294a; --green:#1f9d55; --green-dark:#187a42; --green-glow:rgba(31,157,85,0.12);
    --border:#e6e9f0; --bg:#f5f7fa; --text:#1a2238; --text-sub:#5b6479; --text-muted:#8e96a8;
    --r-sm:0.45rem; --r-md:0.65rem; --r-lg:0.9rem;
    --shadow-sm: 0 1px 2px rgba(13,31,60,0.06);
    --shadow-md: 0 4px 16px rgba(13,31,60,0.08);
    --ease: cubic-bezier(0.32,0.72,0,1);
  }
  * { box-sizing: border-box; }
  body { margin:0; font-family:'Inter',-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif; background:var(--bg); color:var(--text); -webkit-font-smoothing:antialiased; }
  a { color:inherit; }

  .shell { display:flex; min-height:100vh; }

  /* ── SIDEBAR ── */
  nav.side {
    width:240px; background:var(--navy); color:#fff; padding:1.5rem 1rem;
    flex-shrink:0; position:sticky; top:0; height:100vh; display:flex; flex-direction:column;
  }
  nav.side .brand { display:flex; align-items:center; gap:0.625rem; font-weight:800; font-size:1.0625rem; letter-spacing:-0.01em; margin:0.25rem 0.5rem 1.75rem; color:#fff; }
  nav.side .brand-mark { width:30px; height:30px; border-radius:0.5rem; background:var(--green); display:flex; align-items:center; justify-content:center; font-size:0.8125rem; font-weight:800; flex-shrink:0; }
  nav.side .nav-list { list-style:none; margin:0; padding:0; display:flex; flex-direction:column; gap:0.125rem; flex:1; }
  nav.side .nav-link {
    display:flex; align-items:center; gap:0.75rem; color:rgba(255,255,255,0.62); text-decoration:none;
    padding:0.625rem 0.75rem; border-radius:var(--r-sm); font-size:0.875rem; font-weight:500;
    transition:background 0.2s var(--ease), color 0.2s var(--ease);
  }
  nav.side .nav-link svg { width:18px; height:18px; flex-shrink:0; opacity:0.85; }
  nav.side .nav-link:hover { background:rgba(255,255,255,0.07); color:#fff; }
  nav.side .nav-link.active { background:var(--green); color:#fff; font-weight:600; }
  nav.side .nav-link.active svg { opacity:1; }
  nav.side .nav-divider { height:1px; background:rgba(255,255,255,0.1); margin:0.875rem 0.25rem; }
  nav.side .nav-foot { padding-top:0.875rem; border-top:1px solid rgba(255,255,255,0.1); }
  nav.side .nav-user { display:flex; align-items:center; gap:0.625rem; padding:0.5rem 0.75rem; font-size:0.8125rem; color:rgba(255,255,255,0.55); }
  nav.side .nav-user-avatar { width:28px; height:28px; border-radius:50%; background:rgba(255,255,255,0.12); display:flex; align-items:center; justify-content:center; font-size:0.75rem; font-weight:700; color:#fff; flex-shrink:0; }

  main { flex:1; padding:2.25rem 2.75rem 4rem; max-width:1180px; min-width:0; }

  h1 { font-size:1.5rem; font-weight:800; letter-spacing:-0.02em; margin:0 0 0.25rem; }
  h2 { font-size:1.125rem; font-weight:700; margin:0 0 1rem; }

  .page-sub { color:var(--text-muted); font-size:0.9375rem; margin:0 0 1.5rem; }

  .topbar { display:flex; justify-content:space-between; align-items:flex-end; margin-bottom:1.75rem; gap:1rem; flex-wrap:wrap; }
  .topbar .topbar-text h1 { margin-bottom:0.375rem; }

  /* ── BUTTONS ── */
  .btn {
    display:inline-flex; align-items:center; gap:0.4rem; background:var(--green); color:#fff;
    padding:0.55rem 1.125rem; border-radius:9999px; text-decoration:none; font-size:0.875rem; font-weight:600;
    border:none; cursor:pointer; transition:background 0.2s var(--ease), transform 0.15s var(--ease), box-shadow 0.2s var(--ease);
    font-family:inherit; line-height:1.3;
  }
  .btn:hover { background:var(--green-dark); box-shadow:0 4px 14px var(--green-glow); }
  .btn:active { transform:scale(0.97); }
  .btn.secondary { background:#fff; color:var(--navy); border:1.5px solid var(--border); }
  .btn.secondary:hover { background:var(--bg); box-shadow:none; border-color:var(--text-muted); }
  .btn.danger { background:#fff; color:#c0392b; border:1.5px solid #f5d4cf; }
  .btn.danger:hover { background:#fdecea; box-shadow:none; }
  .btn.sm { padding:0.4rem 0.875rem; font-size:0.8125rem; }
  form.inline { display:inline; }

  /* ── CARDS ── */
  .card { background:#fff; border:1px solid var(--border); border-radius:var(--r-lg); padding:1.75rem; max-width:760px; box-shadow:var(--shadow-sm); }
  .panel { background:#fff; border:1px solid var(--border); border-radius:var(--r-lg); box-shadow:var(--shadow-sm); overflow:hidden; }

  /* ── STAT CARDS (dashboard) ── */
  .stat-grid { display:grid; grid-template-columns:repeat(auto-fit, minmax(180px, 1fr)); gap:1rem; margin-bottom:2rem; }
  .stat-card { background:#fff; border:1px solid var(--border); border-radius:var(--r-lg); padding:1.375rem 1.5rem; box-shadow:var(--shadow-sm); text-decoration:none; display:block; transition:transform 0.25s var(--ease), box-shadow 0.25s var(--ease); }
  .stat-card:hover { transform:translateY(-2px); box-shadow:var(--shadow-md); }
  .stat-val { font-size:1.875rem; font-weight:800; letter-spacing:-0.03em; color:var(--navy); line-height:1; margin-bottom:0.375rem; }
  .stat-lbl { font-size:0.8125rem; color:var(--text-muted); font-weight:600; }

  /* ── FORM FIELDS ── */
  .field { margin-bottom:1.25rem; }
  .field label { display:block; font-size:0.8125rem; font-weight:600; margin-bottom:0.4rem; color:var(--text); }
  .field .hint { font-size:0.75rem; color:var(--text-muted); font-weight:400; margin-left:0.375rem; }
  .field input[type=text], .field input[type=date], .field input[type=number],
  .field input[type=password], .field textarea, .field select {
    width:100%; padding:0.625rem 0.8rem; border:1.5px solid var(--border); border-radius:var(--r-sm);
    font-size:0.9rem; font-family:inherit; background:#fff; color:var(--text);
    transition:border-color 0.2s var(--ease), box-shadow 0.2s var(--ease); outline:none;
  }
  .field input:focus, .field textarea:focus, .field select:focus { border-color:var(--green); box-shadow:0 0 0 3px var(--green-glow); }
  .field textarea { resize:vertical; line-height:1.5; }
  .field input[type=file] {
    width:100%; padding:0.7rem 0.9rem; border:1.5px dashed var(--border); border-radius:var(--r-sm);
    background:var(--bg); font-size:0.85rem; cursor:pointer;
  }
  .checkbox-field { display:flex; align-items:center; gap:0.5rem; font-size:0.875rem; font-weight:500; cursor:pointer; }
  .checkbox-field input { width:auto !important; margin:0; accent-color:var(--green); }
  .row3 { display:grid; grid-template-columns:1fr 1fr 1fr; gap:1rem; }
  .row2 { display:grid; grid-template-columns:1fr 1fr; gap:1rem; }
  .field-group { background:var(--bg); border:1px solid var(--border); border-radius:var(--r-md); padding:1.25rem 1.25rem 0.25rem; margin-bottom:1.25rem; }
  .field-group-title { font-size:0.75rem; font-weight:700; letter-spacing:0.06em; text-transform:uppercase; color:var(--text-muted); margin-bottom:0.875rem; }
  .current-file { font-size:0.75rem; color:var(--text-muted); margin-top:0.375rem; }
  .current-file a { color:var(--green-dark); font-weight:600; text-decoration:none; }
  .current-file a:hover { text-decoration:underline; }
  .form-actions { display:flex; gap:0.625rem; margin-top:1.75rem; padding-top:1.5rem; border-top:1px solid var(--border); }

  /* ── TABLE ── */
  table { width:100%; border-collapse:collapse; background:#fff; font-size:0.875rem; }
  th, td { padding:0.875rem 1.25rem; text-align:left; border-bottom:1px solid var(--border); vertical-align:middle; }
  th { background:var(--bg); font-weight:600; font-size:0.75rem; letter-spacing:0.04em; text-transform:uppercase; color:var(--text-muted); }
  tbody tr { transition:background 0.15s var(--ease); }
  tbody tr:hover { background:#fafbfd; }
  tbody tr:last-child td { border-bottom:none; }
  td.actions-cell { white-space:nowrap; text-align:right; }
  td.empty-cell { text-align:center; color:var(--text-muted); padding:3rem 1.25rem; }
  img.thumb { height:44px; width:64px; border-radius:var(--r-sm); object-fit:cover; display:block; border:1px solid var(--border); background:var(--bg); }

  /* ── BADGES / PILLS ── */
  .pill { display:inline-flex; align-items:center; gap:0.3rem; font-size:0.75rem; font-weight:700; padding:0.25rem 0.7rem; border-radius:9999px; letter-spacing:0.01em; }
  .pill::before { content:''; width:6px; height:6px; border-radius:50%; background:currentColor; }
  .pill.live { background:var(--green-glow); color:var(--green-dark); }
  .pill.draft { background:#f1f3f7; color:var(--text-muted); }
  .pill.yes { background:var(--green-glow); color:var(--green-dark); }
  .pill.no { background:#f1f3f7; color:var(--text-muted); }
  .pill.unread { background:#fff4e0; color:#9a6b00; }

  /* ── FLASH ── */
  .flash { background:var(--green-glow); border:1px solid #b7e4c7; color:var(--green-dark); padding:0.75rem 1.125rem; border-radius:var(--r-sm); margin-bottom:1.25rem; font-size:0.875rem; font-weight:500; display:flex; align-items:center; gap:0.5rem; }
  .flash.error { background:#fdecea; border-color:#f5b7b1; color:#922b21; }

  @media (max-width: 900px) {
    nav.side { width:72px; padding:1.25rem 0.5rem; }
    nav.side .brand span, nav.side .nav-link span, nav.side .nav-user span { display:none; }
    nav.side .brand { justify-content:center; }
    nav.side .nav-link { justify-content:center; }
    main { padding:1.5rem 1.25rem 3rem; }
    .row3, .row2 { grid-template-columns:1fr; }
  }
</style>
</head>
<body>
<div class="shell">
  <nav class="side">
    <div class="brand"><span class="brand-mark">N</span><span>NSML CMS</span></div>
    <ul class="nav-list">
      <?php foreach ($navItems as $item): ?>
      <li>
        <a href="<?= $item['href'] ?>" class="nav-link <?= ($activeNav ?? '') === $item['key'] ? 'active' : '' ?>">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="<?= $item['icon'] ?>"/></svg>
          <span><?= htmlspecialchars($item['label']) ?></span>
        </a>
      </li>
      <?php endforeach; ?>
    </ul>
    <div class="nav-foot">
      <div class="nav-user">
        <span class="nav-user-avatar"><?= htmlspecialchars(mb_strtoupper(mb_substr($admin['username'], 0, 1))) ?></span>
        <span><?= htmlspecialchars($admin['username']) ?></span>
      </div>
      <a href="logout.php" class="nav-link">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4M16 17l5-5-5-5M21 12H9"/></svg>
        <span>Log out</span>
      </a>
    </div>
  </nav>
  <main>
