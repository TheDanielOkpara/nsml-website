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

$hour = (int) date('G');
$greeting = $hour < 12 ? 'Good morning' : ($hour < 17 ? 'Good afternoon' : 'Good evening');

$pageTitle = 'Dashboard';
$activeNav = 'dashboard';
require __DIR__ . '/layout-top.php';
?>

<div class="topbar">
  <div class="topbar-text">
    <h1><?= $greeting ?>, <?= htmlspecialchars(current_admin()['username']) ?></h1>
    <p class="page-sub"><?= date('l, j F Y') ?></p>
  </div>
  <div class="quick-actions">
    <a href="blog-edit.php" class="btn">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><path d="M12 5v14M5 12h14"/></svg>
      New post
    </a>
    <a href="properties-edit.php" class="btn secondary">Add property</a>
  </div>
</div>

<?php if ($counts['unread'] > 0): ?>
<a href="messages.php" class="attention">
  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 6 12 13 2 6m0 0v12a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2z"/></svg>
  <span class="at-text"><?= $counts['unread'] ?> new <?= $counts['unread'] === 1 ? 'message' : 'messages' ?> waiting to be read</span>
  <span class="at-go">Review →</span>
</a>
<?php endif; ?>

<div class="dash-cols">
  <div class="panel">
    <div class="panel-head">
      <h2>Recent posts</h2>
      <a href="blog.php" class="btn sm secondary">View all</a>
    </div>
    <?php if ($recentPosts): ?>
      <?php foreach ($recentPosts as $p): ?>
      <a href="blog-edit.php?id=<?= $p['id'] ?>" class="row-link">
        <div class="rl-main">
          <div class="rl-title"><?= htmlspecialchars($p['title']) ?></div>
          <div class="rl-sub"><?= $p['published_at'] ? htmlspecialchars(date('j M Y', strtotime($p['published_at']))) : 'No date' ?></div>
        </div>
        <span class="pill <?= $p['is_published'] ? 'live' : 'draft' ?>"><?= $p['is_published'] ? 'Published' : 'Draft' ?></span>
      </a>
      <?php endforeach; ?>
    <?php else: ?>
      <div class="empty-state" style="padding:2.5rem 1.5rem;">
        <div class="es-title">No posts yet</div>
        <div class="es-text">Write your first article for the News page.</div>
        <a href="blog-edit.php" class="btn">New post</a>
      </div>
    <?php endif; ?>
  </div>

  <div class="panel">
    <div class="panel-head">
      <h2>Recent messages</h2>
      <a href="messages.php" class="btn sm secondary">View all</a>
    </div>
    <?php if ($recentMsgs): ?>
      <?php foreach ($recentMsgs as $m): ?>
      <a href="messages.php?id=<?= $m['id'] ?>" class="row-link">
        <div class="rl-main">
          <div class="rl-title"><?= htmlspecialchars(trim($m['first_name'] . ' ' . $m['last_name'])) ?: 'Anonymous' ?></div>
          <div class="rl-sub"><?= htmlspecialchars($m['email']) ?></div>
        </div>
        <?php if (!$m['is_read']): ?><span class="pill unread">New</span><?php else: ?><span class="rl-sub" style="white-space:nowrap;"><?= htmlspecialchars(date('j M', strtotime($m['created_at']))) ?></span><?php endif; ?>
      </a>
      <?php endforeach; ?>
    <?php else: ?>
      <div class="empty-state" style="padding:2.5rem 1.5rem;">
        <div class="es-title">No messages yet</div>
        <div class="es-text">Contact-form submissions will appear here.</div>
      </div>
    <?php endif; ?>
  </div>
</div>

<div class="overview">
  <a href="blog.php" class="overview-item" style="text-decoration:none;">
    <div class="overview-num"><?= $counts['published'] ?> <span style="color:var(--text-muted); font-weight:500; font-size:0.875rem;">/ <?= $counts['posts'] ?></span></div>
    <div class="overview-lbl">Published posts</div>
  </a>
  <a href="properties.php" class="overview-item" style="text-decoration:none;">
    <div class="overview-num"><?= $counts['properties'] ?></div>
    <div class="overview-lbl">Properties</div>
  </a>
  <a href="team.php" class="overview-item" style="text-decoration:none;">
    <div class="overview-num"><?= $counts['team'] ?></div>
    <div class="overview-lbl">Team members</div>
  </a>
  <a href="subscribers.php" class="overview-item" style="text-decoration:none;">
    <div class="overview-num"><?= $counts['subscribers'] ?></div>
    <div class="overview-lbl">Subscribers</div>
  </a>
</div>

<?php require __DIR__ . '/layout-bottom.php'; ?>
