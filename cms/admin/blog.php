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
  <h1>Blog Posts</h1>
  <a href="blog-edit.php" class="btn">+ New Post</a>
</div>

<?php if (!empty($_GET['saved'])): ?><div class="flash">Post saved.</div><?php endif; ?>
<?php if (!empty($_GET['deleted'])): ?><div class="flash">Post deleted.</div><?php endif; ?>

<table>
  <thead><tr><th>Cover</th><th>Title</th><th>Published</th><th>Status</th><th></th></tr></thead>
  <tbody>
  <?php foreach ($posts as $p): ?>
    <tr>
      <td><?php if ($p['cover_image']): ?><img class="thumb" src="../<?= htmlspecialchars($p['cover_image']) ?>" alt=""><?php endif; ?></td>
      <td><?= htmlspecialchars($p['title']) ?></td>
      <td><?= htmlspecialchars($p['published_at'] ?? '—') ?></td>
      <td><?= $p['is_published'] ? 'Published' : 'Draft' ?></td>
      <td>
        <a href="blog-edit.php?id=<?= $p['id'] ?>" class="btn secondary">Edit</a>
        <a href="blog.php?delete=<?= $p['id'] ?>" class="btn danger" onclick="return confirm('Delete this post?');">Delete</a>
      </td>
    </tr>
  <?php endforeach; ?>
  <?php if (!$posts): ?><tr><td colspan="5">No posts yet.</td></tr><?php endif; ?>
  </tbody>
</table>

<?php require __DIR__ . '/layout-bottom.php'; ?>
