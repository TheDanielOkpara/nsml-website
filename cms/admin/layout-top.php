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
<script>if (localStorage.getItem('nsmlNavCollapsed') === '1') document.documentElement.classList.add('nav-collapsed');</script>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($pageTitle ?? 'Admin') ?> — NSML CMS</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;450;500;600;700;800&display=swap" rel="stylesheet">
<style>
  :root {
    /* Brand anchors (kept from the public site, used sparingly) */
    --navy:#0d1f3c; --ink:#16213d;
    --green:#1f9d55; --green-deep:#178a49; --green-tint:rgba(31,157,85,0.10); --green-ring:rgba(31,157,85,0.22);
    /* Calm cool neutrals */
    --bg:#f5f6f9; --surface:#ffffff; --surface-2:#f8f9fb;
    --border:#e6e8ef; --border-strong:#d6dae3; --hairline:#eef0f4;
    --text:#19213a; --text-sub:#525c75; --text-muted:#8a92a6;
    /* Amber for attention/unread */
    --amber-tint:#fff6e6; --amber-border:#f4dca6; --amber-ink:#8a5a00;
    --red:#c0392b; --red-tint:#fdecea; --red-border:#f3cdc7;
    --r-sm:7px; --r-md:10px; --r-lg:13px;
    --shadow-sm:0 1px 2px rgba(13,31,60,0.05);
    --shadow-pop:0 8px 28px -8px rgba(13,31,60,0.18);
    --ease:cubic-bezier(0.32,0.72,0,1);
  }
  * { box-sizing:border-box; }
  html { -webkit-text-size-adjust:100%; }
  body {
    margin:0; font-family:'Inter',-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;
    background:var(--bg); color:var(--text); font-size:15px; line-height:1.5;
    -webkit-font-smoothing:antialiased; text-rendering:optimizeLegibility;
  }
  a { color:inherit; }
  ::selection { background:var(--green-ring); }
  :focus-visible { outline:2px solid var(--green); outline-offset:2px; border-radius:3px; }

  .shell { display:flex; min-height:100vh; }

  /* ───────────── FLOATING SIDEBAR ───────────── */
  :root { --nav-w:240px; --nav-w-collapsed:76px; --nav-gap:1rem; }
  nav.side {
    width:var(--nav-w); background:var(--navy); color:#fff; padding:1.125rem 0.75rem 0.875rem;
    flex-shrink:0; position:fixed; top:var(--nav-gap); left:var(--nav-gap); bottom:var(--nav-gap);
    display:flex; flex-direction:column; border-radius:18px; box-shadow:0 16px 40px -16px rgba(13,31,60,0.45);
    transition:width 0.2s var(--ease); z-index:20; overflow:hidden;
  }
  html.nav-collapsed nav.side { width:var(--nav-w-collapsed); }
  main { flex:1; min-width:0; margin-left:calc(var(--nav-w) + var(--nav-gap) * 2); padding:2rem 2rem 4rem; transition:margin-left 0.2s var(--ease); }
  html.nav-collapsed main { margin-left:calc(var(--nav-w-collapsed) + var(--nav-gap) * 2); }
  main > * { max-width:1120px; margin-left:auto; margin-right:auto; }

  nav.side .brand { display:flex; align-items:center; margin:0.25rem 0.25rem 1.25rem; height:30px; }
  nav.side .brand-logo-wrap { width:auto; height:30px; overflow:hidden; display:flex; align-items:center; transition:width 0.2s var(--ease); }
  nav.side .brand-logo-wrap img { height:30px; width:auto; display:block; flex-shrink:0; }
  html.nav-collapsed nav.side .brand-logo-wrap { width:30px; }

  .nav-toggle {
    position:absolute; top:1.4rem; right:-12px; width:24px; height:24px; border-radius:50%;
    background:var(--surface); border:1px solid var(--border-strong); color:var(--text-sub);
    display:flex; align-items:center; justify-content:center; cursor:pointer; box-shadow:var(--shadow-sm);
    transition:transform 0.2s var(--ease), background 0.16s; z-index:21;
  }
  .nav-toggle:hover { background:var(--surface-2); }
  .nav-toggle svg { width:13px; height:13px; transition:transform 0.2s var(--ease); }
  html.nav-collapsed .nav-toggle svg { transform:rotate(180deg); }

  nav.side .nav-eyebrow { font-size:0.6875rem; font-weight:600; letter-spacing:0.08em; text-transform:uppercase; color:rgba(255,255,255,0.35); padding:0 0.75rem; margin-bottom:0.5rem; white-space:nowrap; }
  html.nav-collapsed nav.side .nav-eyebrow { opacity:0; }
  nav.side .nav-list { list-style:none; margin:0; padding:0; display:flex; flex-direction:column; gap:2px; flex:1; overflow-y:auto; }
  nav.side .nav-link {
    display:flex; align-items:center; gap:0.7rem; color:rgba(255,255,255,0.62); text-decoration:none;
    padding:0.5rem 0.75rem; border-radius:var(--r-sm); font-size:0.875rem; font-weight:500; position:relative;
    transition:background 0.16s var(--ease), color 0.16s var(--ease); white-space:nowrap; overflow:hidden;
  }
  nav.side .nav-link svg { width:17px; height:17px; flex-shrink:0; opacity:0.72; transition:opacity 0.16s, color 0.16s; }
  nav.side .nav-link:hover { background:rgba(255,255,255,0.06); color:#fff; }
  nav.side .nav-link:hover svg { opacity:1; }
  nav.side .nav-link.active { background:rgba(255,255,255,0.10); color:#fff; font-weight:600; }
  nav.side .nav-link.active svg { opacity:1; color:#41d684; }
  nav.side .nav-foot { padding-top:0.75rem; margin-top:0.5rem; border-top:1px solid rgba(255,255,255,0.09); }
  nav.side .nav-user { display:flex; align-items:center; gap:0.625rem; padding:0.5rem 0.75rem 0.625rem; }
  nav.side .nav-user-avatar { width:30px; height:30px; border-radius:50%; background:rgba(255,255,255,0.13); display:flex; align-items:center; justify-content:center; font-size:0.8125rem; font-weight:700; color:#fff; flex-shrink:0; }
  nav.side .nav-user-name { font-size:0.8125rem; font-weight:600; color:#fff; line-height:1.2; white-space:nowrap; }
  nav.side .nav-user-role { font-size:0.6875rem; color:rgba(255,255,255,0.45); white-space:nowrap; }
  html.nav-collapsed nav.side .nav-user span:not(.nav-user-avatar),
  html.nav-collapsed nav.side .nav-link span:not(.nav-link-icon) { display:none; }

  @media (max-width:900px) {
    nav.side { width:var(--nav-w-collapsed) !important; }
    main { margin-left:calc(var(--nav-w-collapsed) + var(--nav-gap) * 2) !important; padding:1.5rem 1.25rem 3rem; }
    nav.side .brand-word, .nav-eyebrow, nav.side .nav-user span:not(.nav-user-avatar), nav.side .nav-link span:not(.nav-link-icon) { display:none !important; }
    .nav-toggle { display:none; }
  }

  h1 { font-size:1.5rem; font-weight:700; letter-spacing:-0.025em; margin:0 0 0.3rem; color:var(--ink); }
  h2 { font-size:1.0625rem; font-weight:650; letter-spacing:-0.01em; margin:0 0 1rem; color:var(--ink); }
  .page-sub { color:var(--text-sub); font-size:0.9375rem; margin:0 0 1.5rem; max-width:60ch; }

  .topbar { display:flex; justify-content:space-between; align-items:flex-end; margin-bottom:1.75rem; gap:1rem; flex-wrap:wrap; }
  .topbar .topbar-text h1 { margin-bottom:0.3rem; }
  .topbar .topbar-text .page-sub { margin:0; }

  /* ───────────── BUTTONS ───────────── */
  .btn {
    display:inline-flex; align-items:center; gap:0.4rem; background:var(--green); color:#fff;
    padding:0.5rem 0.9rem; border-radius:var(--r-sm); text-decoration:none; font-size:0.875rem; font-weight:600;
    border:1px solid transparent; cursor:pointer; line-height:1.3; white-space:nowrap; font-family:inherit;
    transition:background 0.16s var(--ease), border-color 0.16s var(--ease), transform 0.1s var(--ease);
  }
  .btn:hover { background:var(--green-deep); }
  .btn:active { transform:translateY(1px); }
  .btn.secondary { background:var(--surface); color:var(--ink); border-color:var(--border-strong); }
  .btn.secondary:hover { background:var(--surface-2); border-color:var(--text-muted); }
  .btn.danger { background:var(--surface); color:var(--red); border-color:var(--red-border); }
  .btn.danger:hover { background:var(--red-tint); }
  .btn.sm { padding:0.35rem 0.7rem; font-size:0.8125rem; }
  .btn svg { width:15px; height:15px; }
  form.inline { display:inline; }

  /* ───────────── SURFACES ───────────── */
  .card { background:var(--surface); border:1px solid var(--border); border-radius:var(--r-md); padding:1.75rem; max-width:780px; box-shadow:var(--shadow-sm); }
  .panel { background:var(--surface); border:1px solid var(--border); border-radius:var(--r-md); box-shadow:var(--shadow-sm); overflow:hidden; }
  .panel-head { display:flex; justify-content:space-between; align-items:center; gap:0.75rem; padding:1rem 1.25rem; border-bottom:1px solid var(--hairline); }
  .panel-head h2 { margin:0; font-size:0.9375rem; }

  /* ───────────── DASHBOARD ───────────── */
  .quick-actions { display:flex; gap:0.625rem; flex-wrap:wrap; }

  .attention {
    display:flex; align-items:center; gap:0.875rem; background:var(--amber-tint); border:1px solid var(--amber-border);
    border-radius:var(--r-md); padding:0.875rem 1.125rem; margin-bottom:1.5rem; text-decoration:none; color:var(--amber-ink);
    transition:background 0.16s var(--ease);
  }
  .attention:hover { background:#fdeecb; }
  .attention svg { width:20px; height:20px; flex-shrink:0; }
  .attention .at-text { flex:1; font-size:0.9375rem; font-weight:600; }
  .attention .at-go { font-size:0.8125rem; font-weight:700; display:inline-flex; align-items:center; gap:0.25rem; }

  .dash-cols { display:grid; grid-template-columns:1fr 1fr; gap:1.25rem; align-items:start; }

  .row-link { display:flex; align-items:center; gap:0.75rem; padding:0.75rem 1.25rem; border-bottom:1px solid var(--hairline); text-decoration:none; transition:background 0.14s var(--ease); }
  .row-link:last-child { border-bottom:none; }
  .row-link:hover { background:var(--surface-2); }
  .row-link .rl-main { flex:1; min-width:0; }
  .row-link .rl-title { font-weight:600; font-size:0.875rem; color:var(--ink); white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
  .row-link .rl-sub { font-size:0.8125rem; color:var(--text-muted); white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }

  .overview { display:flex; flex-wrap:wrap; gap:0; margin-top:1.75rem; background:var(--surface); border:1px solid var(--border); border-radius:var(--r-md); box-shadow:var(--shadow-sm); overflow:hidden; }
  .overview-item { flex:1; min-width:120px; padding:1rem 1.25rem; border-right:1px solid var(--hairline); }
  .overview-item:last-child { border-right:none; }
  .overview-num { font-size:1.375rem; font-weight:700; letter-spacing:-0.02em; color:var(--ink); line-height:1; }
  .overview-lbl { font-size:0.8125rem; color:var(--text-muted); margin-top:0.3rem; font-weight:500; }

  /* Legacy stat classes kept for safety, calmed down */
  .stat-grid { display:grid; grid-template-columns:repeat(auto-fit, minmax(170px, 1fr)); gap:0.875rem; margin-bottom:1.75rem; }
  .stat-card { background:var(--surface); border:1px solid var(--border); border-radius:var(--r-md); padding:1.25rem 1.375rem; box-shadow:var(--shadow-sm); text-decoration:none; display:block; transition:border-color 0.18s var(--ease); }
  .stat-card:hover { border-color:var(--border-strong); }
  .stat-val { font-size:1.5rem; font-weight:700; letter-spacing:-0.025em; color:var(--ink); line-height:1; margin-bottom:0.3rem; }
  .stat-lbl { font-size:0.8125rem; color:var(--text-muted); font-weight:500; }

  /* ───────────── FORMS ───────────── */
  .field { margin-bottom:1.25rem; }
  .field label { display:block; font-size:0.8125rem; font-weight:600; margin-bottom:0.4rem; color:var(--text); }
  .field .hint { font-size:0.75rem; color:var(--text-muted); font-weight:450; margin-left:0.375rem; }
  .field input[type=text], .field input[type=date], .field input[type=number],
  .field input[type=email], .field input[type=password], .field textarea, .field select {
    width:100%; padding:0.6rem 0.75rem; border:1px solid var(--border-strong); border-radius:var(--r-sm);
    font-size:0.9rem; font-family:inherit; background:var(--surface); color:var(--text);
    transition:border-color 0.16s var(--ease), box-shadow 0.16s var(--ease); outline:none;
  }
  .field input::placeholder, .field textarea::placeholder { color:var(--text-muted); }
  .field input:focus, .field textarea:focus, .field select:focus { border-color:var(--green); box-shadow:0 0 0 3px var(--green-tint); }
  .field textarea { resize:vertical; line-height:1.55; }
  .field input[type=file] {
    width:100%; padding:0.65rem 0.85rem; border:1.5px dashed var(--border-strong); border-radius:var(--r-sm);
    background:var(--surface-2); font-size:0.85rem; cursor:pointer; color:var(--text-sub);
  }
  .field input[type=file]:hover { border-color:var(--green); }
  .checkbox-field { display:flex; align-items:center; gap:0.5rem; font-size:0.875rem; font-weight:500; cursor:pointer; }
  .checkbox-field input { width:auto !important; margin:0; accent-color:var(--green); width:16px; height:16px; }
  .row3 { display:grid; grid-template-columns:1fr 1fr 1fr; gap:1rem; }
  .row2 { display:grid; grid-template-columns:1fr 1fr; gap:1rem; }
  .field-group { background:var(--surface-2); border:1px solid var(--border); border-radius:var(--r-md); padding:1.25rem 1.25rem 0.25rem; margin-bottom:1.25rem; }
  .field-group-title { font-size:0.75rem; font-weight:700; letter-spacing:0.05em; text-transform:uppercase; color:var(--text-muted); margin-bottom:0.875rem; }
  .current-file { font-size:0.75rem; color:var(--text-muted); margin-top:0.375rem; }
  .current-file a { color:var(--green-deep); font-weight:600; text-decoration:none; }
  .current-file a:hover { text-decoration:underline; }
  .form-actions { display:flex; gap:0.625rem; margin-top:1.75rem; padding-top:1.5rem; border-top:1px solid var(--hairline); }
  .preview-thumb-wrap { display:flex; align-items:center; gap:0.75rem; margin-top:0.5rem; }
  .preview-thumb { height:64px; width:64px; border-radius:var(--r-sm); object-fit:cover; display:block; border:1px solid var(--border); background:var(--surface-2); }
  .preview-thumb-wide { height:80px; width:140px; }

  /* ───────────── GALLERY ───────────── */
  .gallery-grid { display:grid; grid-template-columns:repeat(auto-fill, minmax(160px, 1fr)); gap:1rem; margin-top:1rem; }
  .gallery-item { position:relative; border:1px solid var(--border); border-radius:var(--r-sm); overflow:hidden; background:var(--surface-2); }
  .gallery-item img { width:100%; height:120px; object-fit:cover; display:block; }
  .gallery-item-actions { display:flex; align-items:center; justify-content:space-between; gap:0.5rem; padding:0.5rem 0.625rem; background:var(--surface); border-top:1px solid var(--border); }
  .gallery-item-actions input[type=number] { width:3.5rem; padding:0.3rem 0.4rem; font-size:0.8rem; }

  /* ───────────── TABLE ───────────── */
  table { width:100%; border-collapse:collapse; background:var(--surface); font-size:0.875rem; }
  th, td { padding:0.8rem 1.25rem; text-align:left; border-bottom:1px solid var(--hairline); vertical-align:middle; }
  th { background:var(--surface-2); font-weight:600; font-size:0.6875rem; letter-spacing:0.05em; text-transform:uppercase; color:var(--text-muted); }
  tbody tr { transition:background 0.14s var(--ease); }
  tbody tr:hover { background:var(--surface-2); }
  tbody tr:last-child td { border-bottom:none; }
  td.actions-cell { white-space:nowrap; text-align:right; }
  td.actions-cell .btn { margin-left:0.375rem; }
  td.empty-cell { text-align:center; color:var(--text-muted); padding:3rem 1.25rem; }
  img.thumb { height:44px; width:64px; border-radius:var(--r-sm); object-fit:cover; display:block; border:1px solid var(--border); background:var(--surface-2); }
  .thumb-empty { height:44px; width:64px; border-radius:var(--r-sm); border:1px solid var(--border); background:var(--surface-2); display:flex; align-items:center; justify-content:center; color:var(--text-muted); font-size:0.625rem; }

  /* ───────────── EMPTY STATE ───────────── */
  .empty-state { text-align:center; padding:3.5rem 1.5rem; color:var(--text-sub); }
  .empty-state .es-icon { width:44px; height:44px; border-radius:var(--r-md); background:var(--surface-2); border:1px solid var(--border); display:inline-flex; align-items:center; justify-content:center; color:var(--text-muted); margin-bottom:1rem; }
  .empty-state .es-title { font-weight:650; color:var(--ink); font-size:0.9375rem; margin-bottom:0.375rem; }
  .empty-state .es-text { font-size:0.875rem; margin-bottom:1.25rem; }

  /* ───────────── BADGES ───────────── */
  .pill { display:inline-flex; align-items:center; gap:0.35rem; font-size:0.75rem; font-weight:600; padding:0.2rem 0.65rem; border-radius:9999px; }
  .pill::before { content:''; width:6px; height:6px; border-radius:50%; background:currentColor; }
  .pill.live, .pill.yes { background:var(--green-tint); color:var(--green-deep); }
  .pill.draft, .pill.no { background:#eef0f4; color:var(--text-muted); }
  .pill.unread { background:var(--amber-tint); color:var(--amber-ink); }

  /* ───────────── FLASH ───────────── */
  .flash { background:var(--green-tint); border:1px solid #b7e4c7; color:var(--green-deep); padding:0.7rem 1.1rem; border-radius:var(--r-sm); margin-bottom:1.25rem; font-size:0.875rem; font-weight:500; display:flex; align-items:center; gap:0.5rem; }
  .flash::before { content:''; width:7px; height:7px; border-radius:50%; background:currentColor; flex-shrink:0; }
  .flash.error { background:var(--red-tint); border-color:var(--red-border); color:#922b21; }

  /* ───────────── MAIL EXPERIENCE (Messages) ───────────── */
  .mail-panel { display:flex; height:min(680px, calc(100vh - 14rem)); }
  .mail-list { width:320px; flex-shrink:0; border-right:1px solid var(--hairline); overflow-y:auto; }
  .mail-list-item { display:block; padding:0.875rem 1.125rem; border-bottom:1px solid var(--hairline); text-decoration:none; color:inherit; transition:background 0.14s var(--ease); }
  .mail-list-item:hover { background:var(--surface-2); }
  .mail-list-item.active { background:var(--green-tint); }
  .mli-top { display:flex; justify-content:space-between; align-items:baseline; gap:0.5rem; margin-bottom:0.2rem; }
  .mli-name { font-size:0.875rem; font-weight:600; color:var(--ink); white-space:nowrap; overflow:hidden; text-overflow:ellipsis; display:flex; align-items:center; gap:0.4rem; }
  .mli-dot { width:7px; height:7px; border-radius:50%; background:var(--green); flex-shrink:0; }
  .mli-time { font-size:0.75rem; color:var(--text-muted); flex-shrink:0; }
  .mli-subject { font-size:0.8125rem; font-weight:500; color:var(--text-sub); margin-bottom:0.15rem; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
  .mli-snippet { font-size:0.8125rem; color:var(--text-muted); white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }

  .mail-detail { flex:1; min-width:0; overflow-y:auto; padding:1.75rem 2rem; }
  .mail-detail-head { display:flex; justify-content:space-between; align-items:flex-start; gap:1rem; flex-wrap:wrap; margin-bottom:1rem; }
  .mail-detail-actions { display:flex; gap:0.5rem; flex-shrink:0; }
  .mail-from { font-size:0.875rem; color:var(--text-sub); }
  .mail-from-name { font-weight:600; color:var(--ink); }
  .mail-from a { color:var(--green-deep); text-decoration:none; font-weight:500; }
  .mail-detail-date { font-size:0.8125rem; color:var(--text-muted); padding-bottom:1.25rem; margin-bottom:1.25rem; border-bottom:1px solid var(--hairline); }
  .mail-body { font-size:0.9375rem; line-height:1.65; color:var(--text); white-space:pre-wrap; }
  @media (max-width: 760px) {
    .mail-panel { flex-direction:column; height:auto; }
    .mail-list { width:100%; max-height:280px; border-right:none; border-bottom:1px solid var(--hairline); }
  }

  /* ───────────── CONFIRM MODAL ───────────── */
  .modal-overlay {
    position:fixed; inset:0; background:rgba(13,31,60,0.32); display:none; align-items:center; justify-content:center;
    z-index:100; padding:1.5rem;
  }
  .modal-overlay.open { display:flex; }
  .modal-box {
    background:var(--surface); border-radius:var(--r-lg); padding:1.5rem; width:360px; max-width:100%;
    box-shadow:0 24px 60px -16px rgba(13,31,60,0.35); border:1px solid var(--border);
  }
  .modal-box h3 { margin:0 0 0.5rem; font-size:1.0625rem; font-weight:700; color:var(--ink); }
  .modal-box p { margin:0 0 1.375rem; font-size:0.875rem; color:var(--text-sub); }
  .modal-actions { display:flex; justify-content:flex-end; gap:0.625rem; }

  @media (max-width: 1000px) { .dash-cols { grid-template-columns:1fr; } }
  @media (max-width: 900px) { .row3, .row2 { grid-template-columns:1fr; } }
  @media (prefers-reduced-motion: reduce) { * { transition-duration:0.01ms !important; } }
</style>
</head>
<body>
<div class="shell">
  <nav class="side">
    <button type="button" class="nav-toggle" id="navToggle" aria-label="Collapse navigation">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M15 18l-6-6 6-6"/></svg>
    </button>
    <div class="brand">
      <span class="brand-logo-wrap"><img src="/images/logo.png" alt="NSML"></span>
    </div>
    <div class="nav-eyebrow">Manage</div>
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
        <span>
          <span class="nav-user-name"><?= htmlspecialchars($admin['username']) ?></span><br>
          <span class="nav-user-role">Administrator</span>
        </span>
      </div>
      <a href="logout.php" class="nav-link">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4M16 17l5-5-5-5M21 12H9"/></svg>
        <span>Log out</span>
      </a>
    </div>
  </nav>
  <main>
