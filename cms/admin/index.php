<?php
require_once __DIR__ . '/../includes/auth.php';
require_login();

$counts = [
    'posts'       => (int) db()->query('SELECT COUNT(*) c FROM blog_posts')->fetch()['c'],
    'published'   => (int) db()->query('SELECT COUNT(*) c FROM blog_posts WHERE is_published = 1')->fetch()['c'],
    'properties'  => (int) db()->query('SELECT COUNT(*) c FROM properties')->fetch()['c'],
    'team'        => (int) db()->query('SELECT COUNT(*) c FROM team_members')->fetch()['c'],
    'unread'      => (int) db()->query('SELECT COUNT(*) c FROM contact_submissions WHERE is_read = 0')->fetch()['c'],
    'subscribers' => (int) db()->query("SELECT COUNT(*) c FROM subscribers WHERE is_active = 1")->fetch()['c'],
];

$recentPosts = db()->query('SELECT id, title, is_published, published_at FROM blog_posts ORDER BY id DESC LIMIT 5')->fetchAll();
$recentMsgs  = db()->query('SELECT id, first_name, last_name, email, created_at, is_read FROM contact_submissions ORDER BY id DESC LIMIT 5')->fetchAll();

$pageTitle = 'Dashboard';
$activeNav = 'dashboard';
require __DIR__ . '/layout-top.php';
?>

<div class="topbar">
  <div class="topbar-text">
    <h1>Welcome back</h1>
    <p class="page-sub" style="margin:0;">Here's what's happening across the site.</p>
  </div>
</div>

<div class="stat-grid">
  <a href="blog.php" class="stat-card">
    <div class="stat-val"><?= $counts['published'] ?>/<?= $counts['posts'] ?></div>
    <div class="stat-lbl">Published Posts</div>
  </a>
  <a href="properties.php" class="stat-card">
    <div class="stat-val"><?= $counts['properties'] ?></div>
    <div class="stat-lbl">Properties</div>
  </a>
  <a href="team.php" class="stat-card">
    <div class="stat-val"><?= $counts['team'] ?></div>
    <div class="stat-lbl">Team Members</div>
  </a>
  <a href="messages.php" class="stat-card">
    <div class="stat-val"><?= $counts['unread'] ?></div>
    <div class="stat-lbl">Unread Messages</div>
  </a>
  <a href="subscribers.php" class="stat-card">
    <div class="stat-val"><?= $counts['subscribers'] ?></div>
    <div class="stat-lbl">Active Subscribers</div>
  </a>
</div>

<div class="row2" style="align-items:start;">
  <div class="panel">
    <div style="padding:1.25rem 1.5rem; border-bottom:1px solid var(--border); display:flex; justify-content:space-between; align-items:center;">
      <h2 style="margin:0;">Recent Posts</h2>
      <a href="blog-edit.php" class="btn sm">+ New</a>
    </div>
    <table>
      <tbody>
      <?php foreach ($recentPosts as $p): ?>
        <tr>
          <td><a href="blog-edit.php?id=<?= $p['id'] ?>" style="text-decoration:none; font-weight:600;"><?= htmlspecialchars($p['title']) ?></a></td>
          <td style="text-align:right;"><span class="pill <?= $p['is_published'] ? 'live' : 'draft' ?>"><?= $p['is_published'] ? 'Published' : 'Draft' ?></span></td>
        </tr>
      <?php endforeach; ?>
      <?php if (!$recentPosts): ?><tr><td class="empty-cell">No posts yet.</td></tr><?php endif; ?>
      </tbody>
    </table>
  </div>

  <div class="panel">
    <div style="padding:1.25rem 1.5rem; border-bottom:1px solid var(--border); display:flex; justify-content:space-between; align-items:center;">
      <h2 style="margin:0;">Recent Messages</h2>
      <a href="messages.php" class="btn sm secondary">View all</a>
    </div>
    <table>
      <tbody>
      <?php foreach ($recentMsgs as $m): ?>
        <tr>
          <td>
            <div style="font-weight:600;"><?= htmlspecialchars(trim($m['first_name'] . ' ' . $m['last_name'])) ?></div>
            <div style="color:var(--text-muted); font-size:0.8125rem;"><?= htmlspecialchars($m['email']) ?></div>
          </td>
          <td style="text-align:right;"><?php if (!$m['is_read']): ?><span class="pill unread">New</span><?php endif; ?></td>
        </tr>
      <?php endforeach; ?>
      <?php if (!$recentMsgs): ?><tr><td class="empty-cell">No messages yet.</td></tr><?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require __DIR__ . '/layout-bottom.php'; ?>
