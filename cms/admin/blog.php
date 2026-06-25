<?php
require_once __DIR__ . '/../includes/auth.php';
require_login();

if (isset($_GET['delete'])) {
    $stmt = db()->prepare('DELETE FROM blog_posts WHERE id = ?');
    $stmt->execute([(int)$_GET['delete']]);
    header('Location: blog.php?deleted=1');
    exit;
}

$posts = db()->query('SELECT * FROM blog_posts ORDER BY published_at DESC, id DESC')->fetchAll();
$pageTitle = 'Blog Posts';
$activeNav = 'blog';
require __DIR__ . '/layout-top.php';
?>

<div class="topbar">
  <div class="topbar-text">
    <h1>Blog Posts</h1>
    <p class="page-sub" style="margin:0;">Manage articles shown on the News page.</p>
  </div>
  <a href="blog-edit.php" class="btn">+ New Post</a>
</div>

<?php if (!empty($_GET['saved'])): ?><div class="flash">Post saved.</div><?php endif; ?>
<?php if (!empty($_GET['deleted'])): ?><div class="flash">Post deleted.</div><?php endif; ?>

<div class="panel">
<table>
  <thead><tr><th>Cover</th><th>Title</th><th>Published</th><th>Status</th><th></th></tr></thead>
  <tbody>
  <?php foreach ($posts as $p): ?>
    <tr>
      <td><?php if ($p['cover_image']): ?><img class="thumb" src="<?= htmlspecialchars(asset_url($p['cover_image'])) ?>" alt=""><?php else: ?><div class="thumb" style="display:flex;align-items:center;justify-content:center;color:var(--text-muted);font-size:0.6875rem;">No image</div><?php endif; ?></td>
      <td style="font-weight:600;"><?= htmlspecialchars($p['title']) ?></td>
      <td><?= htmlspecialchars($p['published_at'] ?? '—') ?></td>
      <td><span class="pill <?= $p['is_published'] ? 'live' : 'draft' ?>"><?= $p['is_published'] ? 'Published' : 'Draft' ?></span></td>
      <td class="actions-cell">
        <?php if ($p['is_published']): ?><a href="https://nilayosports.com/<?= rawurlencode($p['slug']) ?>" class="btn sm secondary" target="_blank">View</a><?php endif; ?>
        <a href="blog-edit.php?id=<?= $p['id'] ?>" class="btn sm secondary">Edit</a>
        <a href="blog.php?delete=<?= $p['id'] ?>" class="btn sm danger" data-confirm="This will permanently delete the post. This can't be undone.">Delete</a>
      </td>
    </tr>
  <?php endforeach; ?>
  <?php if (!$posts): ?><tr><td colspan="5" class="empty-cell">No posts yet.</td></tr><?php endif; ?>
  </tbody>
</table>
</div>

<?php require __DIR__ . '/layout-bottom.php'; ?>
